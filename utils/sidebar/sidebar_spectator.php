<?php
// Ensure session is started
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
                <a href="/spectator/dashboard" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded <?php echo ($current_page === 'spectator_dashboard') ? 'bg-blue-50' : ''; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Spectator</span>
                </a>
            </li>
            <li>
                <a href="/group/manage" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded <?php echo ($current_page === 'group_manage') ? 'bg-blue-50' : ''; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Groupes</span>
                </a>
            </li>
            <li>
                <a href="/notifications" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded <?php echo ($current_page === 'notifications') ? 'bg-blue-50' : ''; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span>Notifications</span>
                </a>
            </li>
            <li>
                <a href="/rankings" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded <?php echo ($current_page === 'rankings') ? 'bg-blue-50' : ''; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path>
                    </svg>
                    <span>Classement</span>
                </a>
            </li>
            <li>
                <a href="/logout" class="flex items-center space-x-2 text-red-600 hover:bg-red-50 p-2 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Déconnexion</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>