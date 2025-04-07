<?php
// controllers/ApplicationsController.php
require_once __DIR__ . '/../utils/database.php';
require_once __DIR__ . '/../utils/FileUploader.php'; // Inclure la classe FileUploader

class ApplicationController {
    private $db;

    public function __construct() {
        $this->db = new Database();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showApplicationForm() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        include __DIR__ . '/../views/applications/add.php';
    }

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
        
        // Gestion du fichier de lettre de motivation avec FileUploader
        $coverLetterPath = null;
        $errors = [];
        if (isset($_FILES['cover_letter']) && $_FILES['cover_letter']['error'] !== UPLOAD_ERR_NO_FILE) {
            try {
                $coverLetterPath = FileUploader::uploadPdf($_FILES['cover_letter'], $userId);
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }
        }

        // Si des erreurs ont été détectées lors du téléversement, afficher le formulaire avec les erreurs
        if (!empty($errors)) {
            include __DIR__ . '/../views/applications/add.php';
            return;
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
            
            // Ajouter une notification
            $this->addNotification($userId, "Votre candidature chez $companyName a été enregistrée avec succès !");
            
            // Stocker le message de succès dans la session
            $_SESSION['success_message'] = 'Candidature ajoutée avec succès';
            header('Location: /applications');
        } else {
            $errors[] = "Erreur lors de l'ajout de la candidature";
            include __DIR__ . '/../views/applications/add.php';
        }
        $stmt->close();
    }

    private function updateApplicationCount($userId) {
        $conn = $this->db->getConnection();
        $conn->query("UPDATE users SET candidature_count = candidature_count + 1 WHERE user_id = $userId");
    }

    private function addNotification($userId, $message) {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $userId, $message);
        $stmt->execute();
        $stmt->close();
    }

    public function listApplications() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    
        $userId = $_SESSION['user_id'];
        // Utiliser $_POST pour les paramètres de recherche
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
}