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

    // Restrict access to spectators only
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

    // Show the form to create a group
    public function showCreateGroupForm() {
        $this->restrictToSpectators();
        include __DIR__ . '/../views/group/create.php';
    }

    // Create a new group
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

    // Show the group management page
    public function manageGroup($groupId) {
        $this->restrictToSpectators();

        $conn = $this->db->getConnection();

        // Verify the group exists and was created by the current user
        $stmt = $conn->prepare("SELECT group_name FROM groups WHERE group_id = ? AND created_by = ?");
        $stmt->bind_param("ii", $groupId, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header('Location: /spectator/dashboard');
            exit;
        }

        $group = $result->fetch_assoc();
        $groupName = $group['group_name'];
        $stmt->close();

        // Fetch group members with their rank and candidature count
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
        $stmt->close();

        include __DIR__ . '/../views/group/manage.php';
    }

    // Add a student to the group
    public function addStudentToGroup($groupId) {
        $this->restrictToSpectators();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /group/manage/' . $groupId);
            exit;
        }

        $identifier = trim($_POST['identifier'] ?? ''); // Can be username or email
        $errors = [];

        if (empty($identifier)) {
            $errors[] = "Veuillez entrer un nom d'utilisateur ou une adresse e-mail.";
        }

        $conn = $this->db->getConnection();

        // Verify the group exists and was created by the current user
        $stmt = $conn->prepare("SELECT group_name FROM groups WHERE group_id = ? AND created_by = ?");
        $stmt->bind_param("ii", $groupId, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header('Location: /spectator/dashboard');
            exit;
        }
        $stmt->close();

        // Find the student by username or email
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_type = 'student'");
        $stmt->bind_param("ss", $identifier, $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $errors[] = "Aucun étudiant trouvé avec ce nom d'utilisateur ou cette adresse e-mail.";
            $stmt->close();
            $this->manageGroup($groupId);
            return;
        }

        $student = $result->fetch_assoc();
        $studentId = $student['user_id'];
        $stmt->close();

        // Check if the student is already in the group
        $stmt = $conn->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $groupId, $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];
        if ($count > 0) {
            $errors[] = "Cet étudiant est déjà dans le groupe.";
            $stmt->close();
            $this->manageGroup($groupId);
            return;
        }
        $stmt->close();

        // Add the student to the group
        $stmt = $conn->prepare("INSERT INTO group_members (group_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $groupId, $studentId);

        if ($stmt->execute()) {
            header('Location: /group/manage/' . $groupId);
        } else {
            $errors[] = "Une erreur s'est produite lors de l'ajout de l'étudiant. Veuillez réessayer.";
            $this->manageGroup($groupId);
        }
        $stmt->close();
    }

    // Remove a student from the group
    public function removeStudentFromGroup($groupId, $studentId) {
        $this->restrictToSpectators();

        $conn = $this->db->getConnection();

        // Verify the group exists and was created by the current user
        $stmt = $conn->prepare("SELECT group_name FROM groups WHERE group_id = ? AND created_by = ?");
        $stmt->bind_param("ii", $groupId, $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            header('Location: /spectator/dashboard');
            exit;
        }
        $stmt->close();

        // Remove the student from the group
        $stmt = $conn->prepare("DELETE FROM group_members WHERE group_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $groupId, $studentId);

        if ($stmt->execute()) {
            header('Location: /group/manage/' . $groupId);
        } else {
            header('Location: /group/manage/' . $groupId);
        }
        $stmt->close();
    }
}