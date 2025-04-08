<?php
require_once __DIR__ . '/../controllers/WelcomeController.php';

$welcomeController = new WelcomeController();
$welcomeController->showWelcomePage();