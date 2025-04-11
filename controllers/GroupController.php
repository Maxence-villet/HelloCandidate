<?php
require_once __DIR__ . '/../utils/database.php';

class GroupController {
    private $db;

    public function __construct() {
        $this->db = new Database();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function restrictToSpectators() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        if ($_SESSION['user_type'] !== 'spectator') {
            header('Location: /welcome');
            exit;
        }
    }

    public function showCreateGroupForm() {
        $this->restrictToSpectators();
        include __DIR__ . '/../views/group/create.php';
    }

    public function createGroup() {
        $this->restrictToSpectators();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /group/create');
            exit;
        }

        $groupName = trim($_POST['group_name'] ?? '');
        $errors = [];

        if (empty($groupName)) {
            $errors[] = "Le nom du groupe est requis.";
        } elseif (strlen($groupName) > 50) {
            $errors[] = "Le nom du groupe ne peut pas dépasser 50 caractères.";
        }

        if (!empty($errors)) {
            include __DIR__ . '/../views/group/create.php';
            return;
        }

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO groups (group_name, created_by) VALUES (?, ?)");
        $stmt->bind_param("si", $groupName, $_SESSION['user_id']);

        if ($stmt->execute()) {
            header('Location: /spectator/dashboard');
        } else {
            $errors[] = "Une erreur s'est produite lors de la création du groupe. Veuillez réessayer.";
            include __DIR__ . '/../views/group/create.php';
        }
        $stmt->close();
    }

    public function manageGroup($groupId, $errors = [], $success = null, $error = null) {
        $this->restrictToSpectators();

        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("SELECT group_name FROM groups WHERE group_id = ? AND created_by = ?");
        $stmt->bind_param("ii", $groupId, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $errors[] = "Groupe non trouvé ou accès non autorisé.";
            $groupName = "Groupe inconnu";
            $members = [];
        } else {
            $group = $result->fetch_assoc();
            $groupName = $group['group_name'];

            $stmt = $conn->prepare("
                SELECT u.user_id, u.username, u.candidature_count, r.rank_name, r.sub_rank
                FROM group_members gm
                JOIN users u ON gm.user_id = u.user_id
                LEFT JOIN ranks r ON u.rank_id = r.rank_id
                WHERE gm.group_id = ?
            ");
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
            $members = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();

        include __DIR__ . '/../views/group/manage.php';
    }

    public function addStudentToGroup($groupId) {
        $this->restrictToSpectators();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $errors = ["Méthode de requête non autorisée. Veuillez utiliser le formulaire pour ajouter un étudiant."];
            $this->manageGroup($groupId, $errors);
            return;
        }

        $identifier = trim($_POST['identifier'] ?? '');
        $literal = false;
        $errors = [];

        if (empty($identifier)) {
            $errors[] = "Veuillez entrer un nom d'utilisateur ou une adresse e-mail.";
        }

        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("SELECT group_name FROM groups WHERE group_id = ? AND created_by = ?");
        $stmt->bind_param("ii", $groupId, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $errors[] = "Groupe non trouvé ou accès non autorisé.";
            $stmt->close();
            $this->manageGroup($groupId, $errors);
            return;
        }

        $group = $result->fetch_assoc();
        $groupName = $group['group_name'];
        $stmt->close();

        if (!empty($errors)) {
            $this->manageGroup($groupId, $errors);
            return;
        }

        $stmt = $conn->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_type = 'student'");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $errors[] = "Aucun étudiant trouvé avec ce nom d'utilisateur ou cette adresse e-mail.";
            $stmt->close();
            $this->manageGroup($groupId, $errors);
            return;
        }

        $student = $result->fetch_assoc();
        $studentId = $student['user_id'];
        $stmt->close();

        $stmt = $conn->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $groupId, $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];
        if ($count > 0) {
            $errors[] = "Cet étudiant est déjà dans le groupe.";
            $stmt->close();
            $this->manageGroup($groupId, $errors);
            return;
        }
        $stmt->close();

        // Au lieu d'ajouter directement, envoyer une invitation
        $this->sendInvitation($groupId, $studentId, $groupName);

        $success = "Invitation envoyée avec succès !";
        $this->manageGroup($groupId, [], $success);
    }

    public function removeStudentFromGroup($groupId, $studentId) {
        $this->restrictToSpectators();

        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("SELECT group_name FROM groups WHERE group_id = ? AND created_by = ?");
        $stmt->bind_param("ii", $groupId, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $errors[] = "Groupe non trouvé ou accès non autorisé.";
            $stmt->close();
            $this->manageGroup($groupId, $errors);
            return;
        }
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $groupId, $studentId);

        if ($stmt->execute()) {
            $success = "Étudiant retiré avec succès !";
            $this->manageGroup($groupId, [], $success);
        } else {
            $error = "Une erreur s'est produite lors du retrait de l'étudiant.";
            $this->manageGroup($groupId, [], null, $error);
        }
        $stmt->close();
    }

    public function viewStudentProfile($userId, $username) {
        $this->restrictToSpectators();

        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("
            SELECT u.user_id
            FROM users u
            JOIN group_members gm ON u.user_id = gm.user_id
            JOIN groups g ON gm.group_id = g.group_id
            WHERE u.user_id = ? AND u.user_type = 'student' AND g.created_by = ?
        ");
        $stmt->bind_param("ii", $userId, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header('Location: /spectator/dashboard');
            exit;
        }
        $stmt->close();

        $stmt = $conn->prepare("
            SELECT company_name, position, status
            FROM applications
            WHERE user_id = ?
            ORDER BY submission_date DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $applications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-27 days'));

        $stmt = $conn->prepare("
            SELECT DATE(submission_date) as submission_day, COUNT(*) as application_count
            FROM applications
            WHERE user_id = ? AND submission_date BETWEEN ? AND ?
            GROUP BY DATE(submission_date)
            ORDER BY submission_date DESC
        ");
        $stmt->bind_param("iss", $userId, $startDate, $endDate);
        $stmt->execute();
        $dailyCountsResult = $stmt->get_result();
        $dailyCounts = [];
        while ($row = $dailyCountsResult->fetch_assoc()) {
            $dailyCounts[$row['submission_day']] = $row['application_count'];
        }
        $stmt->close();

        $totalApplications = array_sum($dailyCounts);
        $daysWithApplications = count($dailyCounts);
        $averagePerDay = $daysWithApplications > 0 ? round($totalApplications / 28, 2) : 0;

        $dailyStats = [];
        for ($i = 0; $i < 28; $i++) {
            $date = date('Y-m-d', strtotime("$endDate - $i days"));
            $dailyStats[$date] = [
                'date' => $date,
                'count' => isset($dailyCounts[$date]) ? $dailyCounts[$date] : 0,
            ];
        }

        include __DIR__ . '/../views/profile/stats.php';
    }

    public function sendInvitation($groupId, $studentId, $groupName) {
        $this->restrictToSpectators();

        $conn = $this->db->getConnection();
        $message = "Vous avez été invité à rejoindre le groupe '$groupName' (group_id:$groupId)";
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $studentId, $message);
        $stmt->execute();
        $stmt->close();
    }
}