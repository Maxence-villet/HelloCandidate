<?php
require_once __DIR__ . '/../utils/database.php';

class RankingController {
    private $db;

    public function __construct() {
        $this->db = new Database();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showRanking() {
        $conn = $this->db->getConnection();

        $stmt = $conn->prepare("SELECT rank_id, rank_name, sub_rank FROM ranks ORDER BY rank_id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $ranks = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $rankGroups = [];
        foreach ($ranks as $rank) {
            $rankGroups[$rank['rank_name']][] = $rank['sub_rank'];
        }
        $orderedRanks = [
            'Challenger', 'Grand Maître', 'Maître', 'Diamant', 'Émeraude',
            'Platine', 'Or', 'Argent', 'Bronze', 'Fer'
        ];
        $sortedRankGroups = [];
        foreach ($orderedRanks as $rankName) {
            if (isset($rankGroups[$rankName])) {
                $sortedRankGroups[$rankName] = $rankGroups[$rankName];
            }
        }

        $rankFilter = $_POST['rank_filter'] ?? null;
        $subRankFilter = $_POST['sub_rank_filter'] ?? null;

        $query = "
            SELECT u.user_id, u.username, u.points, r.rank_name, r.sub_rank
            FROM users u
            LEFT JOIN ranks r ON u.rank_id = r.rank_id
            WHERE u.user_type = 'student'
        ";
        $params = [];
        $types = '';

        if ($rankFilter && in_array($rankFilter, $orderedRanks)) {
            $query .= " AND r.rank_name = ?";
            $params[] = $rankFilter;
            $types .= 's';
        }

        if ($subRankFilter && in_array($subRankFilter, [1, 2, 3])) {
            $query .= " AND r.sub_rank = ?";
            $params[] = $subRankFilter;
            $types .= 'i';
        }

        $query .= " ORDER BY u.rank_id DESC, u.points DESC LIMIT 100";

        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $userPosition = null;
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $stmt = $conn->prepare("
                SELECT position
                FROM (
                    SELECT user_id, 
                           RANK() OVER (ORDER BY rank_id DESC, points DESC) as position
                    FROM users
                    WHERE user_type = 'student'
                ) ranked_users
                WHERE user_id = ?
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $userPosition = $result->fetch_assoc()['position'] ?? null;
            $stmt->close();
        }

        include __DIR__ . '/../views/ranking.php';
    }
}
?>