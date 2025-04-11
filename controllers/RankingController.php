<?php
// controllers/RankingController.php
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

        // Récupérer tous les rangs pour l'affichage en haut
        $stmt = $conn->prepare("SELECT rank_id, rank_name, sub_rank FROM ranks ORDER BY rank_id DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $ranks = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Regrouper les rangs par rank_name pour l'affichage
        $rankGroups = [];
        foreach ($ranks as $rank) {
            $rankGroups[$rank['rank_name']][] = $rank['sub_rank'];
        }
        // Trier les rangs selon l'ordre spécifié
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

        // Récupérer les filtres depuis les paramètres POST
        $rankFilter = $_POST['rank_filter'] ?? null;
        $subRankFilter = $_POST['sub_rank_filter'] ?? null;

        // Construire la requête pour les utilisateurs avec les filtres
        $query = "
            SELECT u.user_id, u.username, u.candidature_count, r.rank_name, r.sub_rank
            FROM users u
            LEFT JOIN ranks r ON u.rank_id = r.rank_id
            WHERE u.user_type = 'student'
        ";
        $params = [];
        $types = '';

        // Ajouter le filtre par rang si sélectionné
        if ($rankFilter && in_array($rankFilter, $orderedRanks)) {
            $query .= " AND r.rank_name = ?";
            $params[] = $rankFilter;
            $types .= 's';
        }

        // Ajouter le filtre par sous-rang si sélectionné
        if ($subRankFilter && in_array($subRankFilter, [1, 2, 3])) {
            $query .= " AND r.sub_rank = ?";
            $params[] = $subRankFilter;
            $types .= 'i';
        }

        // Ajouter le tri et la limite
        $query .= " ORDER BY u.rank_id DESC, u.candidature_count DESC LIMIT 100";

        // Exécuter la requête
        $stmt = $conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $users = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Trouver la position de l'utilisateur connecté (si connecté)
        $userPosition = null;
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $stmt = $conn->prepare("
                SELECT position
                FROM (
                    SELECT user_id, 
                           RANK() OVER (ORDER BY rank_id DESC, candidature_count DESC) as position
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

        // Passer les données à la vue
        include __DIR__ . '/../views/ranking.php';
    }
}