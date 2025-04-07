<?php
require_once __DIR__ . '/../../controllers/GroupController.php';

$groupController = new GroupController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupController->createGroup();
} else {
    $groupController->showCreateGroupForm();
}