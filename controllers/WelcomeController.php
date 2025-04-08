<?php
require_once __DIR__ . '/../utils/database.php';

class WelcomeController {
    private $db;

    public function __construct() {
        $this->db = new Database();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showWelcomePage() {
        // Redirect spectators to their dashboard
        if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'spectator') {
            header('Location: /spectator/dashboard');
            exit;
        }

        // Redirect if not logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $conn = $this->db->getConnection();
        $userId = $_SESSION['user_id'];

        // Fetch user's rank and sub-rank
        $stmt = $conn->prepare("
            SELECT u.candidature_count, r.rank_name, r.sub_rank, r.min_applications
            FROM users u
            LEFT JOIN ranks r ON u.rank_id = r.rank_id
            WHERE u.user_id = ?
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            header('Location: /login?error=Utilisateur non trouvé.');
            exit;
        }
        $currentRank = $result->fetch_assoc();
        $stmt->close();

        // Calculate progress to next rank
        $nextRankMin = null;
        if ($currentRank['rank_name']) {
            $stmt = $conn->prepare("
                SELECT min_applications
                FROM ranks
                WHERE (rank_name = ? AND sub_rank < ?) OR (rank_name > ?)
                ORDER BY rank_name ASC, sub_rank DESC
                LIMIT 1
            ");
            $stmt->bind_param("sis", $currentRank['rank_name'], $currentRank['sub_rank'], $currentRank['rank_name']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $nextRank = $result->fetch_assoc();
                $nextRankMin = $nextRank['min_applications'];
            }
            $stmt->close();
        }

        $candidatureCount = $currentRank['candidature_count'];
        $progressPercentage = $nextRankMin ? round(($candidatureCount - $currentRank['min_applications']) / ($nextRankMin - $currentRank['min_applications']) * 100) : 100;

        // Pass data to the view
        include __DIR__ . '/../views/welcome.php';
    }
}