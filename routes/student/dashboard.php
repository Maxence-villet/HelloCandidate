<?php
require_once __DIR__ . '/../../controllers/student/DashboardController.php';

$dashboardController = new DashboardController();
$dashboardController->showDashboardPage();