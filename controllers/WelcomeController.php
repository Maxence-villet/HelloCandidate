<?php
// controllers/WelcomeController.php
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
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $conn = $this->db->getConnection();

        // Récupérer les informations de l'utilisateur (candidature_count et rank_id)
        $stmt = $conn->prepare("SELECT candidature_count, rank_id FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        $candidatureCount = $user['candidature_count'] ?? 0;
        $currentRankId = $user['rank_id'];

        // Déterminer le rang actuel en fonction de candidature_count
        $stmt = $conn->prepare("SELECT rank_id, rank_name, sub_rank, min_applications FROM ranks WHERE min_applications <= ? ORDER BY min_applications DESC LIMIT 1");
        $stmt->bind_param("i", $candidatureCount);
        $stmt->execute();
        $result = $stmt->get_result();
        $newRank = $result->fetch_assoc();
        $stmt->close();

        $newRankId = $newRank['rank_id'] ?? 1; // Par défaut, Fer 3 (rank_id = 1)

        // Vérifier si l'utilisateur a monté de rang ou sous-rang
        if (is_null($currentRankId) || $newRankId > $currentRankId) {
            // Mettre à jour le rank_id de l'utilisateur
            $stmt = $conn->prepare("UPDATE users SET rank_id = ? WHERE user_id = ?");
            $stmt->bind_param("ii", $newRankId, $userId);
            $stmt->execute();
            $stmt->close();

            // Générer une notification pour la montée de rang/sous-rang
            $message = "Félicitations ! Vous êtes passé à " . $newRank['rank_name'] . " " . $newRank['sub_rank'] . " !";
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
            $stmt->bind_param("is", $userId, $message);
            $stmt->execute();
            $stmt->close();
        }

        // Récupérer les informations du rang actuel
        $stmt = $conn->prepare("SELECT rank_name, sub_rank, min_applications FROM ranks WHERE rank_id = ?");
        $stmt->bind_param("i", $newRankId);
        $stmt->execute();
        $result = $stmt->get_result();
        $currentRank = $result->fetch_assoc();
        $stmt->close();

        // Récupérer le min_applications du sous-rang suivant (si existant)
        $nextRankId = $newRankId + 1;
        $stmt = $conn->prepare("SELECT min_applications FROM ranks WHERE rank_id = ?");
        $stmt->bind_param("i", $nextRankId);
        $stmt->execute();
        $result = $stmt->get_result();
        $nextRank = $result->fetch_assoc();
        $stmt->close();

        // Calculer la progression
        $nextMinApplications = $nextRank ? $nextRank['min_applications'] : null;
        $progressPercentage = 0;
        if ($nextMinApplications) {
            $progress = $candidatureCount - $currentRank['min_applications'];
            $requiredForNext = $nextMinApplications - $currentRank['min_applications'];
            $progressPercentage = ($progress / $requiredForNext) * 100;
            $progressPercentage = min(100, max(0, $progressPercentage)); // Limiter entre 0 et 100
        }

        // Passer les données à la vue
        include __DIR__ . '/../views/welcome.php';
    }
}