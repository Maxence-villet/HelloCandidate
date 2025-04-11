<?php
// controllers/ApplicationController.php
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
            // Mettre à jour le compteur de candidatures
            $this->updateApplicationCount($userId);
            
            // Ajouter une notification pour l'ajout de candidature
            $this->addNotification($userId, "Votre candidature chez $companyName a été enregistrée avec succès !");

            // Vérifier si l'utilisateur a monté de rang ou sous-rang
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
     * Met à jour le compteur de candidatures de l'utilisateur
     */
    private function updateApplicationCount($userId) {
        $conn = $this->db->getConnection();
        $conn->query("UPDATE users SET candidature_count = candidature_count + 1 WHERE user_id = $userId");
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
     * Vérifie si l'utilisateur a monté de rang ou sous-rang
     */
    private function checkRankProgression($userId) {
        $conn = $this->db->getConnection();

        // Récupérer le candidature_count et le rank_id actuel
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
            $this->addNotification($userId, $message);
            $_SESSION['rank_up_message'] = $message; // Stocker le message pour l'afficher
            // Indiquer qu'un son doit être joué pour la montée de rang
            $_SESSION['play_level_up_sound'] = true;
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
        // Utiliser $_POST au lieu de $_GET
        $searchQuery = trim($_POST['search'] ?? '');
        $statusFilter = $_POST['status'] ?? null;
        $dateFrom = $_POST['date_from'] ?? null;
        $dateTo = $_POST['date_to'] ?? null;

        // Réinitialiser les filtres si le bouton "Réinitialiser" est cliqué
        if (isset($_POST['reset'])) {
            $searchQuery = '';
            $statusFilter = null;
            $dateFrom = null;
            $dateTo = null;
        }
    
        $conn = $this->db->getConnection();
        
        // Construction dynamique de la requête
        $query = "SELECT * FROM applications WHERE user_id = ?";
        $params = [$userId];
        $types = 'i'; // user_id est un entier
    
        // Filtre par recherche texte
        if (!empty($searchQuery)) {
            $query .= " AND (company_name LIKE ? OR position LIKE ? OR description LIKE ?)";
            $searchParam = "%$searchQuery%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            $types .= 'sss';
        }
    
        // Filtre par statut
        if ($statusFilter && in_array($statusFilter, ['pending', 'interview', 'rejected', 'accepted'])) {
            $query .= " AND status = ?";
            $params[] = $statusFilter;
            $types .= 's';
        }
    
        // Filtre par date
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
    
        $query .= " ORDER BY submission_date DESC";
    
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
        // Démarrer la session pour vérifier l'utilisateur connecté
    
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    
        $conn = $this->db->getConnection();
    
        // Convertir les IDs en entier pour éviter les injections
        $applicationId = (int)$applicationId;
        $userId = (int)$_SESSION['user_id'];
    
        // Vérifier que l'ID est valide (supérieur à 0)
        if ($applicationId <= 0) {
            http_response_code(400);
            echo "ID de candidature invalide";
            exit;
        }
    
        // Construire la requête SQL - SEULEMENT les champs de la table applications
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
    
        // Exécuter la requête
        $stmt = $conn->prepare($query);
    
        // Supposons que application_id et user_id sont des entiers
        $stmt->bind_param("ii", $applicationId, $userId);
    
        $stmt->execute();
    
        // Récupérer le résultat
        $result = $stmt->get_result();
    
        // Vérifier si la requête a réussi
        if ($result === false) {
            http_response_code(500);
            echo "Erreur lors de l'exécution de la requête : " . $conn->error;
            exit;
        }
    
        // Récupérer les données
        $application = $result->fetch_assoc();
    
        // Vérifier si la candidature existe
        if ($application) {
            // Inclure la vue pour afficher les détails
            require __DIR__ . '/../views/applications/view.php';
        } else {
            // Si aucune candidature n'est trouvée, renvoyer une erreur 404
            http_response_code(404);
            echo "Candidature non trouvée ou vous n'avez pas l'autorisation de la voir.";
            exit;
        }
    }
}