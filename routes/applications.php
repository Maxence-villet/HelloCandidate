<?php
// routes/applications.php
require_once __DIR__ . '/../controllers/ApplicationController.php';

$controller = new ApplicationController();
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Gestion des différentes sous-routes
if ($path === '/applications') {
    // Route pour lister les candidatures (avec ou sans paramètres de recherche)
    $controller->listApplications();
} elseif ($path === '/applications/add') {
    // Route pour ajouter une candidature
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->addApplication();
    } else {
        $controller->showApplicationForm();
    }
} elseif (preg_match('#^/applications/view/(\d+)$#', $path, $matches)) {
    // Route pour voir une candidature spécifique
    $applicationId = (int)$matches[1];
    // Vous devrez implémenter une méthode viewApplication dans ApplicationController
    // $controller->viewApplication($applicationId);
    echo "Affichage de la candidature ID $applicationId (à implémenter)";
} else {
    // Route non trouvée
    http_response_code(404);
    echo "Page non trouvée dans /applications";
}