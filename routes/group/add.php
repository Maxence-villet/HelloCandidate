<?php
require_once __DIR__ . '/../controllers/GroupController.php';

$groupController = new GroupController();

// Extract group ID from the URL (e.g., /add-student-to-group/1)
$request = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($request, '/'));
$groupId = isset($parts[1]) ? (int)$parts[1] : 0;

if ($groupId <= 0) {
    header('Location: /spectator/dashboard');
    exit;
}

$groupController->addStudentToGroup($groupId);