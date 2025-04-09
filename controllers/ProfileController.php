<?php
require_once __DIR__ . '/../utils/database.php';

class ProfileController {
    private $db;

    public function __construct() {
        $this->db = new Database();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showProfile($userId = null) {
        $conn = $this->db->getConnection();

        if ($userId === null) {
            if (!isset($_SESSION['user_id'])) {
                header('Location: /login');
                exit;
            }
            $userId = $_SESSION['user_id'];
            $isOwnProfile = true;
        } else {
            $isOwnProfile = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId);
        }

        $stmt = $conn->prepare("
            SELECT u.user_id, u.username, u.bio, u.candidature_count, r.rank_name, r.sub_rank, r.min_applications
            FROM users u
            LEFT JOIN ranks r ON u.rank_id = r.rank_id
            WHERE u.user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            header('Location: /welcome?error=Utilisateur non trouvé.');
            exit;
        }
        $user = $result->fetch_assoc();
        $stmt->close();

        $currentRankMin = $user['min_applications'];
        $nextRankMin = $this->getNextRankMinApplications($conn, $user['rank_name'], $user['sub_rank']);
        $progress = $nextRankMin ? round(($user['candidature_count'] - $currentRankMin) / ($nextRankMin - $currentRankMin) * 100) : 100;

        $stmt = $conn->prepare("
            SELECT b.badge_name, b.description
            FROM user_badges ub
            JOIN badges b ON ub.badge_id = b.badge_id
            WHERE ub.user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $badges = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Récupérer les données d'activité sur 6 mois
        $activityData = $this->getActivityData($conn, $userId);

        include __DIR__ . '/../views/profile/profile.php';
    }

    public function updateBio() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /profile');
            exit;
        }

        $bio = trim($_POST['bio'] ?? '');
        $errors = [];

        if (strlen($bio) > 500) {
            $errors[] = "La bio ne peut pas dépasser 500 caractères.";
        }

        if (!empty($errors)) {
            $this->showProfile($_SESSION['user_id']);
            return;
        }

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE user_id = ?");
        $stmt->bind_param("si", $bio, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $success = "Bio mise à jour avec succès !";
        } else {
            $errors[] = "Une erreur s'est produite lors de la mise à jour de la bio.";
        }
        $stmt->close();

        $this->showProfile($_SESSION['user_id']);
    }

    private function getNextRankMinApplications($conn, $currentRankName, $currentSubRank) {
        $stmt = $conn->prepare("
            SELECT min_applications
            FROM ranks
            WHERE (rank_name = ? AND sub_rank < ?) OR rank_id > (SELECT rank_id FROM ranks WHERE rank_name = ? AND sub_rank = ?)
            ORDER BY rank_id ASC
            LIMIT 1
        ");
        $stmt->bind_param("siss", $currentRankName, $currentSubRank, $currentRankName, $currentSubRank);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null;
        }
        $nextRank = $result->fetch_assoc();
        $stmt->close();
        return $nextRank['min_applications'];
    }

    private function getActivityData($conn, $userId) {
        // Modifier pour 6 mois au lieu de 3 mois
        $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
        // Utiliser CURRENT_DATE pour inclure aujourd'hui
        $stmt = $conn->prepare("
            SELECT DATE(submission_date) as date, COUNT(*) as count
            FROM applications
            WHERE user_id = ? AND submission_date >= ? AND submission_date <= CURRENT_DATE
            GROUP BY DATE(submission_date)
            ORDER BY submission_date ASC
        ");
        $stmt->bind_param("is", $userId, $sixMonthsAgo);
        $stmt->execute();
        $result = $stmt->get_result();
        $activity = [];
        while ($row = $result->fetch_assoc()) {
            $activity[$row['date']] = $row['count'];
        }
        $stmt->close();

        // Générer un tableau pour les 6 derniers mois
        $startDate = new DateTime($sixMonthsAgo);
        $endDate = new DateTime(); // Inclut aujourd'hui
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($startDate, $interval, $endDate->modify('+1 day')); // Ajouter un jour pour inclure aujourd'hui

        $activityData = [];
        $maxCount = max(array_values($activity) + [1]); // Éviter division par 0
        foreach ($dateRange as $date) {
            $dateStr = $date->format('Y-m-d');
            $count = isset($activity[$dateStr]) ? $activity[$dateStr] : 0;
            // Ajuster le calcul de l'intensité pour mieux refléter les grandes valeurs
            $intensity = 0;
            if ($count > 0) {
                if ($count >= 10) {
                    $intensity = 4; // 10 candidatures ou plus -> intensité maximale
                } elseif ($count >= 7) {
                    $intensity = 3;
                } elseif ($count >= 4) {
                    $intensity = 2;
                } else {
                    $intensity = 1;
                }
            }
            $activityData[$dateStr] = [
                'count' => $count,
                'intensity' => $intensity
            ];
        }
        return $activityData;
    }
}