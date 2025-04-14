<?php
require_once __DIR__ . '/../utils/database.php';

class ApplicationController {
    private $db;

    public function __construct() {
        $this->db = new Database();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Affiche le formulaire d'ajout de candidature
     */
    public function showApplicationForm() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        include __DIR__ . '/../views/applications/add.php';
    }

    /**
     * Ajoute une nouvelle candidature
     */
    public function addApplication() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $companyName = trim($_POST['company_name'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $submissionDate = $_POST['submission_date'] ?? date('Y-m-d');
        $status = $_POST['status'] ?? 'pending';
        $address = trim($_POST['address'] ?? '');
        $offerLink = trim($_POST['offer_link'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        // Gestion du fichier de lettre de motivation
        $coverLetterPath = null;
        if (isset($_FILES['cover_letter']) && $_FILES['cover_letter']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/uploads/cover_letters/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileExt = pathinfo($_FILES['cover_letter']['name'], PATHINFO_EXTENSION);
            $fileName = 'user_' . $userId . '_' . time() . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['cover_letter']['tmp_name'], $targetPath)) {
                $coverLetterPath = '/uploads/cover_letters/' . $fileName;
            }
        }

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO applications 
            (user_id, company_name, position, submission_date, status, address, offer_link, description, cover_letter_path) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssss", 
            $userId, $companyName, $position, $submissionDate, $status, 
            $address, $offerLink, $description, $coverLetterPath);

        if ($stmt->execute()) {
            // Mettre à jour le compteur de candidatures et les points
            $this->updateApplicationCountAndPoints($userId);
            
            // Ajouter une notification pour l'ajout de candidature
            $this->addNotification($userId, "Votre candidature chez $companyName a été enregistrée avec succès !");

            // Vérifier si l'utilisateur a monté ou descendu de rang basé sur les points
            $this->checkRankProgression($userId);
            
            // Stocker le message de succès dans la session
            $_SESSION['success_message'] = 'Candidature ajoutée avec succès';
            // Indiquer qu'un son doit être joué pour l'ajout de candidature
            $_SESSION['play_application_sound'] = true;
            header('Location: /applications');
        } else {
            $errors[] = "Erreur lors de l'ajout de la candidature";
            include __DIR__ . '/../views/applications/add.php';
        }
        $stmt->close();
    }

    /**
     * Met à jour le compteur de candidatures et les points de l'utilisateur
     */
    private function updateApplicationCountAndPoints($userId) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE users SET candidature_count = candidature_count + 1, points = points + 1 WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Ajoute une notification pour l'utilisateur
     */
    private function addNotification($userId, $message) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $message);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Vérifie si l'utilisateur a monté ou descendu de rang basé sur les points
     */
    private function checkRankProgression($userId) {
        $conn = $this->db->getConnection();

        // Récupérer le nombre de points et le rank_id actuel
        $stmt = $conn->prepare("SELECT points, rank_id FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        $points = $user['points'] ?? 0;
        $currentRankId = $user['rank_id'];

        // Déterminer le rang approprié en fonction des points
        $stmt = $conn->prepare("SELECT rank_id, rank_name, sub_rank, min_applications FROM ranks WHERE min_applications <= ? ORDER BY min_applications DESC LIMIT 1");
        $stmt->bind_param("i", $points);
        $stmt->execute();
        $result = $stmt->get_result();
        $newRank = $result->fetch_assoc();
        $stmt->close();

        $newRankId = $newRank['rank_id'] ?? 1; // Par défaut, Fer 3 (rank_id = 1)

        // Vérifier si l'utilisateur doit monter ou descendre de rang
        if (is_null($currentRankId) || $newRankId != $currentRankId) {
            // Mettre à jour le rank_id de l'utilisateur
            $stmt = $conn->prepare("UPDATE users SET rank_id = ? WHERE user_id = ?");
            $stmt->bind_param("ii", $newRankId, $userId);
            $stmt->execute();
            $stmt->close();

            // Générer une notification pour le changement de rang
            $message = $newRankId > $currentRankId
                ? "Félicitations ! Vous êtes passé à " . $newRank['rank_name'] . " " . $newRank['sub_rank'] . " !"
                : "Votre rang a été ajusté à " . $newRank['rank_name'] . " " . $newRank['sub_rank'] . " en raison de votre score.";
            $this->addNotification($userId, $message);
            $_SESSION['rank_change_message'] = $message; // Stocker le message pour l'afficher
            // Indiquer qu'un son doit être joué pour le changement de rang
            $_SESSION['play_rank_change_sound'] = true;
        }
    }

    /**
     * Liste toutes les candidatures de l'utilisateur avec filtres
     */
    public function listApplications() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    
        $userId = $_SESSION['user_id'];
        $searchQuery = trim($_POST['search'] ?? '');
        $statusFilter = $_POST['status'] ?? null;
        $dateFrom = $_POST['date_from'] ?? null;
        $dateTo = $_POST['date_to'] ?? null;
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $perPage = 10;

        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        if (isset($_POST['reset'])) {
            $searchQuery = '';
            $statusFilter = null;
            $dateFrom = null;
            $dateTo = null;
            $page = 1;
            $offset = 0;
        }
    
        $conn = $this->db->getConnection();
        
        $countQuery = "SELECT COUNT(*) as total FROM applications WHERE user_id = ?";
        $countParams = [$userId];
        $countTypes = 'i';

        if (!empty($searchQuery)) {
            $countQuery .= " AND (company_name LIKE ? OR position LIKE ? OR description LIKE ?)";
            $searchParam = "%$searchQuery%";
            $countParams = array_merge($countParams, [$searchParam, $searchParam, $searchParam]);
            $countTypes .= 'sss';
        }
    
        if ($statusFilter && in_array($statusFilter, ['pending', 'interview', 'rejected', 'accepted'])) {
            $countQuery .= " AND status = ?";
            $countParams[] = $statusFilter;
            $countTypes .= 's';
        }
    
        if ($dateFrom) {
            $countQuery .= " AND submission_date >= ?";
            $countParams[] = $dateFrom;
            $countTypes .= 's';
        }
        if ($dateTo) {
            $countQuery .= " AND submission_date <= ?";
            $countParams[] = $dateTo;
            $countTypes .= 's';
        }

        $stmt = $conn->prepare($countQuery);
        $stmt->bind_param($countTypes, ...$countParams);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalApplications = $result->fetch_assoc()['total'];
        $stmt->close();

        $totalPages = ceil($totalApplications / $perPage);

        $query = "SELECT * FROM applications WHERE user_id = ?";
        $params = [$userId];
        $types = 'i';
    
        if (!empty($searchQuery)) {
            $query .= " AND (company_name LIKE ? OR position LIKE ? OR description LIKE ?)";
            $searchParam = "%$searchQuery%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            $types .= 'sss';
        }
    
        if ($statusFilter && in_array($statusFilter, ['pending', 'interview', 'rejected', 'accepted'])) {
            $query .= " AND status = ?";
            $params[] = $statusFilter;
            $types .= 's';
        }
    
        if ($dateFrom) {
            $query .= " AND submission_date >= ?";
            $params[] = $dateFrom;
            $types .= 's';
        }
        if ($dateTo) {
            $query .= " AND submission_date <= ?";
            $params[] = $dateTo;
            $types .= 's';
        }
    
        $query .= " ORDER BY submission_date DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';
    
        $stmt = $conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $applications = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    
        include __DIR__ . '/../views/applications/list.php';
    }

    /**
     * Affiche les détails d'une candidature spécifique
     */
    public function viewApplication($applicationId) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    
        $conn = $this->db->getConnection();
    
        $applicationId = (int)$applicationId;
        $userId = (int)$_SESSION['user_id'];
    
        if ($applicationId <= 0) {
            http_response_code(400);
            echo "ID de candidature invalide";
            exit;
        }
    
        $query = "SELECT 
            application_id, 
            company_name, 
            position, 
            submission_date, 
            status, 
            address, 
            offer_link, 
            description, 
            cover_letter_path, 
            created_at
          FROM applications 
          WHERE application_id = ? AND user_id = ?";
    
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $applicationId, $userId);
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        if ($result === false) {
            http_response_code(500);
            echo "Erreur lors de l'exécution de la requête : " . $conn->error;
            exit;
        }
    
        $application = $result->fetch_assoc();
    
        if ($application) {
            require __DIR__ . '/../views/applications/view.php';
        } else {
            http_response_code(404);
            echo "Candidature non trouvée ou vous n'avez pas l'autorisation de la voir.";
            exit;
        }
    }

    /**
     * Met à jour le statut d'une candidature
     */
    public function updateApplicationStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $applicationId = (int)$_POST['application_id'];
        $newStatus = $_POST['status'];

        $validStatuses = ['pending', 'interview', 'rejected', 'accepted'];
        if (!in_array($newStatus, $validStatuses)) {
            $_SESSION['error_message'] = 'Statut invalide.';
            header('Location: /applications');
            exit;
        }

        $conn = $this->db->getConnection();

        // Récupérer l'ancien statut de la candidature
        $stmt = $conn->prepare("SELECT user_id, status FROM applications WHERE application_id = ?");
        $stmt->bind_param("i", $applicationId);
        $stmt->execute();
        $result = $stmt->get_result();
        $application = $result->fetch_assoc();
        $stmt->close();

        if (!$application || $application['user_id'] !== $userId) {
            $_SESSION['error_message'] = 'Candidature non trouvée ou non autorisée.';
            header('Location: /applications');
            exit;
        }

        $oldStatus = $application['status'];

        // Mettre à jour le statut de la candidature
        $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE application_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $newStatus, $applicationId, $userId);

        if ($stmt->execute()) {
            // Gérer les points en fonction du changement de statut (US18)
            if ($oldStatus !== $newStatus) {
                if ($newStatus === 'rejected') {
                    $this->updateUserPoints($userId, -1);
                    $this->addNotification($userId, "Vous avez perdu 1 point car une candidature a été marquée comme refusée.");
                } elseif ($newStatus === 'accepted') {
                    $this->updateUserPoints($userId, 5);
                    $this->addNotification($userId, "Félicitations ! Vous avez gagné 5 points car une candidature a été acceptée.");
                }
                // Pas de changement de points pour 'pending' ou 'interview'
            }

            // Vérifier la progression de rang après mise à jour des points
            $this->checkRankProgression($userId);

            $_SESSION['success_message'] = 'Statut mis à jour avec succès.';
        } else {
            $_SESSION['error_message'] = 'Erreur lors de la mise à jour du statut.';
        }
        $stmt->close();

        header('Location: /applications');
    }

    /**
     * Met à jour les points de l'utilisateur
     */
    private function updateUserPoints($userId, $pointsChange) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE users SET points = GREATEST(points + ?, 0) WHERE user_id = ?");
        $stmt->bind_param("ii", $pointsChange, $userId);
        $stmt->execute();
        $stmt->close();
    }
}
?>