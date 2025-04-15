<?php
// Modified NotificationsController.php for US18
require_once __DIR__ . '/../utils/database.php';

class NotificationsController {
    private $db;

    public function __construct() {
        $this->db = new Database();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function listNotifications() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("SELECT notification_id, message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Récupérer les messages et drapeaux de son depuis la session
        $successMessage = $_SESSION['success_message'] ?? null;
        $rankUpMessage = $_SESSION['rank_up_message'] ?? null;
        $playApplicationSound = $_SESSION['play_application_sound'] ?? false;
        $playLevelUpSound = $_SESSION['play_level_up_sound'] ?? false;

        // Nettoyer les variables de session après utilisation
        unset($_SESSION['success_message']);
        unset($_SESSION['rank_up_message']);
        unset($_SESSION['play_application_sound']);
        unset($_SESSION['play_level_up_sound']);

        include __DIR__ . '/../views/notifications/list.php';
    }

    public function handleInvitation() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /notifications');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $notificationId = $_POST['notification_id'] ?? 0;
        $action = $_POST['action'] ?? '';
        $groupId = $_POST['group_id'] ?? 0;

        if ($notificationId <= 0 || !in_array($action, ['accept', 'refuse']) || $groupId <= 0) {
            header('Location: /notifications');
            exit;
        }

        $conn = $this->db->getConnection();

        // Vérifier que la notification appartient à l'utilisateur
        $stmt = $conn->prepare("SELECT user_id FROM notifications WHERE notification_id = ?");
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $notification = $result->fetch_assoc();
        $stmt->close();

        if (!$notification || $notification['user_id'] != $userId) {
            header('Location: /notifications');
            exit;
        }

        if ($action === 'accept') {
            // Ajouter l'utilisateur au groupe
            $stmt = $conn->prepare("INSERT IGNORE INTO group_members (group_id, user_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $groupId, $userId);
            $stmt->execute();
            $stmt->close();
        }

        // Supprimer la notification après action
        $stmt = $conn->prepare("DELETE FROM notifications WHERE notification_id = ?");
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
        $stmt->close();

        header('Location: /notifications');
        exit;
    }
}
?>