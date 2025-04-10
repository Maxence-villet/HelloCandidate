<?php
// Ensure session is started if needed (though not required for auth pages in this case)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if($_SESSION["user_type"] == "spectator") {
    header('Location: /spectator/dashboard');
} 
if($_SESSION["user_type"] == "student") {
    header('Location: /student/dashboard');
} 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelloCandidate - <?php echo htmlspecialchars($page_title ?? 'Authentification'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Top Bar -->
    <header class="bg-white shadow-sm p-4 flex justify-between items-center">
        <div class="flex items-center">
            <h1 class="text-2xl font-bold text-blue-600">HelloCandidate</h1>
        </div>
        <div class="flex items-center space-x-4">
            <?php if ($page_title === 'Connexion'): ?>
                <a href="/register" class="text-sm text-blue-600 hover:underline">S'inscrire</a>
            <?php elseif ($page_title === 'Inscription'): ?>
                <a href="/login" class="text-sm text-blue-600 hover:underline">Se connecter</a>
            <?php endif; ?>
        </div>
    </header>