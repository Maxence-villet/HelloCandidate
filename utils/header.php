<?php

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
$username = $isLoggedIn ? htmlspecialchars($_SESSION['username']) : 'Invité';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Inclure une bibliothèque d'icônes (par exemple, Heroicons via CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/heroicons@2.0.13/outline.js"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-sm">
        <nav class="container mx-auto px-4 py-4 flex items-center justify-between">
            <!-- Logo / Nom du site -->
            <div class="text-2xl font-bold text-gray-800">
                <a href="<?php echo $isLoggedIn ? '/welcome' : '/'; ?>" class="hover:text-blue-600 transition-colors duration-200">
                    HelloCandidate
                </a>
            </div>

            <!-- Menu principal (visible sur desktop) -->
            <div class="hidden md:flex items-center space-x-6 ">
                <?php if ($isLoggedIn): ?>
                    <a href="/applications" class="text-gray-800 hover:text-blue-600 transition-colors duration-200">Mes candidatures</a>
                    <a href="/rankings" class="text-gray-800 hover:text-blue-600 transition-colors duration-200">Classement</a>
                    <a href="/notifications" class="text-gray-800 hover:text-blue-600 transition-colors duration-200">Notifications</a>
                    <a href="/profile" class="text-gray-800 hover:text-blue-600 transition-colors duration-200">Profil</a>
                    <div class="flex items-center space-x-3">
                        <span class="text-gray-800 font-medium"><?php echo $username; ?></span>
                        <a href="/logout" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Déconnexion
                        </a>
                    </div>
                <?php else: ?>
                    <a href="/login" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors duration-200">
                        Connexion
                    </a>
                    <a href="/register" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300 transition-colors duration-200">
                        Inscription
                    </a>
                <?php endif; ?>
            </div>

            <!-- Bouton menu hamburger (visible sur mobile) -->
            <div class="md:hidden">
                <button id="menu-toggle" class="focus:outline-none" aria-label="Ouvrir le menu" aria-expanded="false">
                    <svg id="menu-icon-open" class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                    <svg id="menu-icon-close" class="w-6 h-6 text-gray-800 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Menu mobile (caché par défaut, affiché via JavaScript) -->
        <div id="mobile-menu" class="md:hidden bg-white border-t border-gray-200 max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div class="container mx-auto px-4 py-4 space-y-3  bg-gray-100">
                <?php if ($isLoggedIn): ?>
                    <a href="/applications" class="block text-gray-800 hover:text-blue-600 transition-colors duration-200 py-2">Mes candidatures</a>
                    <a href="/rankings" class="block text-gray-800 hover:text-blue-600 transition-colors duration-200 py-2">Classement</a>
                    <a href="/notifications" class="block text-gray-800 hover:text-blue-600 transition-colors duration-200 py-2">Notifications</a>
                    <a href="/profile" class="block text-gray-800 hover:text-blue-600 transition-colors duration-200 py-2">Profil</a>
                    <div class="border-t border-gray-200 pt-3">
                        <span class="block text-gray-800 font-medium py-2"><?php echo $username; ?></span>
                        <a href="/logout" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Déconnexion
                        </a>
                    </div>
                <?php else: ?>
                    <a href="/login" class="block text-gray-800 hover:text-blue-600 transition-colors duration-200 py-2">Connexion</a>
                    <a href="/register" class="block text-gray-800 hover:text-blue-600 transition-colors duration-200 py-2">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Script pour toggle le menu mobile -->
    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIconOpen = document.getElementById('menu-icon-open');
        const menuIconClose = document.getElementById('menu-icon-close');

        menuToggle.addEventListener('click', function() {
            const isOpen = mobileMenu.classList.contains('max-h-0');
            
            if (isOpen) {
                // Ouvrir le menu
                mobileMenu.classList.remove('max-h-0');
                mobileMenu.classList.add('max-h-screen');
                menuIconOpen.classList.add('hidden');
                menuIconClose.classList.remove('hidden');
                menuToggle.setAttribute('aria-expanded', 'true');
            } else {
                // Fermer le menu
                mobileMenu.classList.remove('max-h-screen');
                mobileMenu.classList.add('max-h-0');
                menuIconOpen.classList.remove('hidden');
                menuIconClose.classList.add('hidden');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Fermer le menu mobile lorsqu'un lien est cliqué
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('max-h-screen');
                mobileMenu.classList.add('max-h-0');
                menuIconOpen.classList.remove('hidden');
                menuIconClose.classList.add('hidden');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        });
    </script>