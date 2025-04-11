<?php
require_once __DIR__ . '/../controllers/HelpController.php';

$helpController = new HelpController();
$helpController->showHelpPage();