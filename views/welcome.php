<?php include __DIR__ . '/layout.php'; ?>
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Message de bienvenue -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6 text-center">
            <h1 class="text-3xl font-bold text-blue-600 mb-4">
                Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !
            </h1>
            <p class="text-gray-600">
                Voici un résumé de votre profil sur HelloCandidate. Utilisez les liens ci-dessous pour naviguer.
            </p>
        </div>

        <!-- 3x3 Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- Case 1: Rank and Sub-Rank (No Icon) -->
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center justify-center">
                <div class="mb-4">
                    <?php
                    // Normalize the rank_name to match the image filename
                    $rankImageName = strtolower($currentRank['rank_name']);
                    $rankImageName = str_replace(' ', '-', $rankImageName); // e.g., "Grand Master" -> "grand-master"
                    $rankImagePath = "/public/rank/{$rankImageName}.png";
                    ?>
                    <img src="<?php echo htmlspecialchars($rankImagePath); ?>" alt="<?php echo htmlspecialchars($currentRank['rank_name']); ?>" class="w-16 h-16 mx-auto">
                </div>
                <h3 class="text-lg font-semibold text-gray-800">
                    <?php echo htmlspecialchars($currentRank['rank_name'] . ' ' . $currentRank['sub_rank']); ?>
                </h3>
                <p class="text-sm text-gray-600 mt-2">
                    Candidatures : <?php echo htmlspecialchars($candidatureCount); ?>
                </p>
            </div>

            <!-- Case 2: Profile Link -->
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center justify-center">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <div class="text-center">
                    <a href="/profile" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Profil
                    </a>
                </div>
            </div>

            <!-- Case 3: Add Application Link -->
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center justify-center">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <div class="text-center">
                    <a href="/applications/add" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Ajouter une candidature
                    </a>
                </div>
            </div>

            <!-- Case 4: List Applications Link -->
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center justify-center">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h18M3 18h18"></path>
                </svg>
                <div class="text-center">
                    <a href="/applications" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Liste des candidatures
                    </a>
                </div>
            </div>

            <!-- Case 5: Rankings Link -->
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center justify-center">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path>
                </svg>
                <div class="text-center">
                    <a href="/rankings" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Classement
                    </a>
                </div>
            </div>

            <!-- Case 6: Notifications Link -->
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center justify-center">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <div class="text-center">
                    <a href="/notifications" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Notifications
                    </a>
                </div>
            </div>

            <!-- Case 7: Help Link -->
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center justify-center">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-center">
                    <a href="/help" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Aide
                    </a>
                </div>
            </div>

            <!-- Case 8: Placeholder (Motivational Message) -->
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center justify-center text-center">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <p class="text-gray-600">
                    Continuez à postuler pour grimper dans les rangs !
                </p>
            </div>

            <!-- Case 9: Placeholder (Additional Message) -->
            <div class="bg-white rounded-lg shadow-lg p-6 flex flex-col items-center justify-center text-center">
                <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
                <p class="text-gray-600">
                    Visez le top 100 du classement !
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>