<?php
require_once __DIR__ . '/../../controllers/GroupController.php';

$groupController = new GroupController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupId = isset($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
    if ($groupId <= 0) {
        $groupController->manageGroup($groupId, ["ID de groupe invalide."]);
        exit;
    }
    $groupController->manageGroup($groupId);
} else {
    $groupId = isset($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
    $groupController->manageGroup($groupId, ["Méthode de requête non autorisée. Veuillez utiliser le tableau de bord pour gérer un groupe."]);
    exit;
}