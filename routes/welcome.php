<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

require_once __DIR__ . '/../controllers/WelcomeController.php';

$welcomeController = new WelcomeController();
$welcomeController->showWelcomePage();