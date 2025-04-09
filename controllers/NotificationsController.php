<?php
// controllers/NotificationsController.php
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