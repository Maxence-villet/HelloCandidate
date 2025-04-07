<?php
require_once __DIR__ . '/../../utils/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

if ($_SESSION['user_type'] !== 'spectator') {
    header('Location: /welcome');
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Fetch groups created by the spectator
$stmt = $conn->prepare("SELECT group_id, group_name FROM groups WHERE created_by = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$groups = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include __DIR__ . '/../../views/spectator/dashboard.php';