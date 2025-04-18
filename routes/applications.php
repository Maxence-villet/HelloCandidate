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
    $controller->viewApplication($applicationId);
} elseif ($path === '/applications/update-status') {
    // Route pour mettre à jour le statut d'une candidature
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->updateApplicationStatus();
    } else {
        http_response_code(405);
        echo "Méthode non autorisée";
    }
} else {
    // Route non trouvée
    http_response_code(404);
    echo "Page non trouvée dans /applications";
}