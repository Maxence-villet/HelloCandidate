<?php
require_once __DIR__ . '/../../controllers/GroupController.php';

$groupController = new GroupController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupId = isset($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
    $studentId = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
    if ($groupId <= 0 || $studentId <= 0) {
        $groupController->manageGroup($groupId, ["ID de groupe ou d'étudiant invalide."]);
        exit;
    }
    $groupController->removeStudentFromGroup($groupId, $studentId);
} else {
    $groupId = isset($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
    $groupController->manageGroup($groupId, ["Méthode de requête non autorisée. Veuillez utiliser le formulaire pour retirer un étudiant."]);
    exit;
}