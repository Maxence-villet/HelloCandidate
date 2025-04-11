<?php

if($_SESSION["user_type"] == "spectator") {
    header('Location: /spectator/dashboard');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelloCandidate - <?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body class="bg-gray-50">
    <!-- Top Bar -->
    <header class="bg-white shadow-sm p-4 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <!-- Burger Menu Button (Visible on Mobile) -->
            <button id="burger-menu" class="md:hidden text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            <h1 class="text-2xl font-bold text-black">HelloCandidate</h1>
        </div>
        <div class="flex items-center">
            <a href="/logout" class="text-red-600 hover:text-red-500 focus:outline-none" title="Déconnexion">
                <i class="fas fa-power-off fa-lg"></i>
            </a>
        </div>
    </header>

    <div class="flex min-h-screen">
        <!-- Sidebar (Hidden by default on mobile) -->
        <div id="sidebar" class="w-64 bg-white shadow-lg p-4 md:block hidden">
            <?php include __DIR__ . '/../sidebar/sidebar_student.php'; ?>
        </div>

        <!-- Overlay for mobile (to close sidebar when clicking outside) -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 md:hidden hidden"></div>

        <!-- JavaScript to toggle sidebar on mobile -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const burgerMenu = document.getElementById('burger-menu');
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');

                // Toggle sidebar and overlay on burger menu click
                burgerMenu.addEventListener('click', function () {
                    sidebar.classList.toggle('hidden');
                    overlay.classList.toggle('hidden');
                });

                // Close sidebar when clicking on overlay
                overlay.addEventListener('click', function () {
                    sidebar.classList.add('hidden');
                    overlay.classList.add('hidden');
                });
            });
        </script>