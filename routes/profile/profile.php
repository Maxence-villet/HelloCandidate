<?php
require_once __DIR__ . '/../../controllers/ProfileController.php';

$profileController = new ProfileController();

if ($_SERVER['REQUEST_URI'] === '/profile') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_bio') {
        $profileController->updateBio();
    } else {
        $profileController->showProfile();
    }
} else {
    http_response_code(404);
    echo "Page non trouvée";
}