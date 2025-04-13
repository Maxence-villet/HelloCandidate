<?php 
if($_SESSION["user_type"] == "spectator") {
    include __DIR__ . '/../utils/header/header_spectator.php';
} else {
    include __DIR__ . '/../utils/header/header_student.php';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aide - Système de Rangs | HelloCandidate</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
<div class="min-h-screen">
    <!-- Ajustement de la div principale pour être centrée avec la sidebar -->
    <div class="md:ml-16 md:mr-16 max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900 sm:text-5xl sm:tracking-tight lg:text-6xl">
                Système de Rangs HelloCandidate
            </h1>
            <p class="mt-5 max-w-xl mx-auto text-xl text-gray-500">
                Maximisez votre progression avec notre système de gamification motivant
            </p>
        </div>

        <!-- Rank Visualization -->
        <div class="bg-white shadow-lg rounded-xl p-6 sm:p-8 mb-12">
            <div class="text-center mb-8 sm:mb-10">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Votre parcours de progression</h2>
                <p class="mt-2 text-base sm:text-lg text-gray-500">10 rangs à conquérir pour devenir un champion de la recherche d'alternance</p>
            </div>
            
            <div class="grid grid-cols-3 sm:grid-cols-5 gap-4 sm:gap-6">
                <?php 
                $globalIndex = 0; // Index global pour les points
                foreach ($ranks as $rank): ?>
                    <div class="flex flex-col items-center group">
                        <?php
                        $rankImageName = strtolower($rank);
                        $rankImagePath = "/public/rank/" . rawurlencode($rankImageName) . ".png";
                        ?>
                        <div class="flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 bg-white rounded-full shadow-md border-2 border-gray-200 group-hover:border-indigo-400 transition-all duration-300">
                            <img src="<?php echo htmlspecialchars($rankImagePath); ?>" alt="<?php echo htmlspecialchars($rank); ?>" class="w-6 h-6 sm:w-8 sm:h-8">
                        </div>
                        <div class="mt-2 sm:mt-3 text-center">
                            <span class="text-sm sm:text-base font-medium text-gray-700"><?php echo htmlspecialchars($rank); ?></span>
                            <div class="text-xs sm:text-sm text-gray-400"><?php echo $globalIndex * 30; ?> points</div>
                        </div>
                    </div>
                    <?php $globalIndex++; // Incrémentation de l'index global ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- How It Works Section -->
        <div class="grid md:grid-cols-2 gap-8 mb-12">
            <!-- Left Column -->
            <div class="bg-white shadow-xl rounded-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Comment progresser ?</h2>
                
                <div class="space-y-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Candidatures envoyées</h3>
                            <p class="mt-1 text-gray-600">
                                Chaque candidature validée vous rapporte des points. Plus vous postulez, plus vous progressez.
                            </p>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Sous-rangs</h3>
                            <p class="mt-1 text-gray-600">
                                Chaque rang contient 3 sous-rangs (3, 2, 1). Complétez-les pour accéder au rang supérieur.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="bg-white shadow-xl rounded-2xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Avantages</h2>
                
                <div class="space-y-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Suivi de progression</h3>
                            <p class="mt-1 text-gray-600">
                                Visualisez en temps réel votre avancement vers le prochain rang.
                            </p>
                        </div>
                    </div>

                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">Classement</h3>
                            <p class="mt-1 text-gray-600">
                                Comparez votre progression avec les autres utilisateurs et motivez-vous mutuellement.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>