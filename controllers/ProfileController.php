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

    // Show the profile page (view or edit)
    public function showProfile($userId = null) {
        $conn = $this->db->getConnection();

        // If no userId is provided, show the logged-in user's profile
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

        // Fetch user details
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

        // Calculate progress to next rank
        $currentRankMin = $user['min_applications'];
        $nextRankMin = $this->getNextRankMinApplications($conn, $user['rank_name'], $user['sub_rank']);
        $progress = $nextRankMin ? round(($user['candidature_count'] - $currentRankMin) / ($nextRankMin - $currentRankMin) * 100) : 100;

        // Fetch user's badges
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

        include __DIR__ . '/../views/profile/profile.php';
    }

    // Update the user's bio
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

    // Helper method to get the min_applications for the next rank
    private function getNextRankMinApplications($conn, $currentRankName, $currentSubRank) {
        $stmt = $conn->prepare("
            SELECT min_applications
            FROM ranks
            WHERE (rank_name = ? AND sub_rank < ?) OR (rank_name > ?)
            ORDER BY rank_name ASC, sub_rank DESC
            LIMIT 1
        ");
        $stmt->bind_param("sis", $currentRankName, $currentSubRank, $currentRankName);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            return null; // No next rank (user is at the highest rank)
        }
        $nextRank = $result->fetch_assoc();
        $stmt->close();
        return $nextRank['min_applications'];
    }
}