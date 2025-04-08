<?php
require_once __DIR__ . '/../../controllers/GroupController.php';

$groupController = new GroupController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    if ($userId <= 0) {
        header('Location: /spectator/dashboard');
        exit;
    }
    if (empty($username)) {
        header('Location: /spectator/dashboard');
        exit;
    }
    $groupController->viewStudentProfile($userId, $username);
} else {
    header('Location: /spectator/dashboard');
    exit;
}