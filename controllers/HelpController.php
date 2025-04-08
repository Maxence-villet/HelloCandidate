<?php
require_once __DIR__ . '/../utils/database.php';

class HelpController {
    private $db;

    public function __construct() {
        $this->db = new Database();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showHelpPage() {
        // Redirect if not logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        // Fetch all ranks (without sub-ranks, just the main rank names)
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("
            SELECT DISTINCT rank_name
            FROM ranks
            ORDER BY min_applications ASC
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $ranks = [];
        while ($row = $result->fetch_assoc()) {
            $ranks[] = $row['rank_name'];
        }
        $stmt->close();

        // Include the view
        include __DIR__ . '/../views/help.php';
    }
}