<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: /login');
    exit;
}

// Inclure la vue de bienvenue
include __DIR__ . '/../views/welcome.php';