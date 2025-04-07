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
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $conn = $this->db->getConnection();

        // Récupérer les notifications de l'utilisateur, triées par date (plus récentes en haut)
        $stmt = $conn->prepare("SELECT notification_id, message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Inclure la vue pour afficher les notifications
        include __DIR__ . '/../views/notifications/list.php';
    }
}