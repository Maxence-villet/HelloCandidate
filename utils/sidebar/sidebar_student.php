<?php
// Ensure session is started (this should be handled in the including file, but we can check for safety)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if username is set in the session
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';
?>

<aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg p-4 transform md:transform-none transition-transform duration-300 z-20">
    <div class="flex items-center space-x-3 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800"><?php echo $username; ?></h2>
            <p class="text-sm text-gray-500"><?php echo $_SESSION["user_type"]; ?></p>
        </div>
    </div>
    <nav>
        <ul class="space-y-2">
            <li>
                <a href="/student/dashboard" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded">
                    <i class="fas fa-tachometer-alt w-5 h-5"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/profile" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profil</span>
                </a>
            </li>
            <li>
                <a href="/applications/add" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Ajouter une candidature</span>
                </a>
            </li>
            <li>
                <a href="/applications" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h18M3 18h18"></path>
                    </svg>
                    <span>Liste des candidatures</span>
                </a>
            </li>
            <li>
                <a href="/rankings" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path>
                    </svg>
                    <span>Classement</span>
                </a>
            </li>
            <li>
                <a href="/notifications" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span>Notifications</span>
                </a>
            </li>
            <li>
                <a href="/help" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Aide</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>