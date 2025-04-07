<?php
require_once __DIR__ . '/../controllers/GroupController.php';

$groupController = new GroupController();

$request = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($request, '/'));
$groupId = isset($parts[1]) ? (int)$parts[1] : 0;
$studentId = isset($parts[2]) ? (int)$parts[2] : 0;

if ($groupId <= 0 || $studentId <= 0) {
    header('Location: /spectator/dashboard');
    exit;
}

$groupController->removeStudentFromGroup($groupId, $studentId);