<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? htmlspecialchars($_SESSION['username']) : 'Guest';
$userType = $isLoggedIn ? $_SESSION['user_type'] : null;

// Determine the dashboard URL based on user type
$dashboardUrl = ($userType === 'spectator') ? '/spectator/dashboard' : '/student/dashboard';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelloCandidate - <?php echo htmlspecialchars($page_title ?? 'Accueil'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-gray-50">
    <!-- Top Bar -->
    <header class="bg-white shadow-sm p-4 flex justify-between items-center">
        <div class="flex items-center">
            <h1 class="text-2xl font-bold text-blue-600">HelloCandidate</h1>
        </div>
        <div class="flex items-center space-x-4">
            <?php if ($isLoggedIn): ?>
                <!-- Logged-in user navbar -->
                <span class="text-gray-700 text-sm font-medium"><?php echo $username; ?></span>
                <a href="<?php echo $dashboardUrl; ?>" class="text-blue-600 hover:text-blue-700 transition-colors duration-200" title="Tableau de bord">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </a>
                <a href="/logout" class="text-red-600 hover:text-red-700 transition-colors duration-200" title="Déconnexion">
                    <i class="fas fa-power-off text-xl"></i>
                </a>
            <?php else: ?>
                <!-- Non-logged-in user navbar -->
                <a href="/register" class="inline-flex items-center px-4 py-2 bg-white text-blue-600 text-sm font-medium rounded-md border border-blue-600 hover:bg-gray-100 transition-colors duration-200">
                    Inscription
                </a>
                <a href="/login" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-600 transition-colors duration-200">
                    Connexion
                </a>
            <?php endif; ?>
        </div>
    </header>