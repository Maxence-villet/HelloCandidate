<?php include __DIR__ . '/layout.php'; ?>
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Page Title -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6 text-center">
            <h1 class="text-3xl font-bold text-blue-600 mb-4">
                Aide - Système de Rangs et Sous-Rangs
            </h1>
            <p class="text-gray-600">
                Découvrez comment fonctionne le système de gamification de HelloCandidate pour rendre votre recherche d'alternance plus motivante !
            </p>
        </div>

        <!-- Rank Progression Line -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h2 class="text-2xl font-semibold text-blue-600 mb-6 text-center">Progression des Rangs</h2>
            <div class="flex flex-wrap justify-center items-center space-x-4">
                <?php foreach ($ranks as $index => $rank): ?>
                    <!-- Rank Image -->
                    <div class="flex flex-col items-center">
                        <?php
                        // Normalize the rank_name to lowercase and encode spaces for the URL
                        $rankImageName = strtolower($rank);
                        // Do NOT replace spaces with hyphens; instead, encode the filename for the URL
                        $rankImagePath = "/public/rank/" . rawurlencode($rankImageName) . ".png";
                        ?>
                        <img src="<?php echo htmlspecialchars($rankImagePath); ?>" alt="<?php echo htmlspecialchars($rank); ?>" class="w-12 h-12 mb-2">
                        <span class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($rank); ?></span>
                    </div>

                    <!-- Arrow (except after the last rank) -->
                    <?php if ($index < count($ranks) - 1): ?>
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Explanation of the Rank and Sub-Rank System -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-semibold text-blue-600 mb-4">Comment fonctionne notre système de gamification ?</h2>
            <p class="text-gray-600 mb-4">
                Chez HelloCandidate, nous avons conçu un système de gamification pour rendre votre recherche d'alternance plus engageante et motivante. Voici les points clés :
            </p>
            <ul class="list-disc list-inside space-y-2 text-gray-600">
                <li><strong>Envoyez des candidatures pour progresser :</strong> Chaque candidature validée vous rapproche d'un nouveau sous-rang. Par exemple, 10 candidatures vous font passer de Fer 3 à Fer 2.</li>
                <li><strong>Montez dans les rangs :</strong> Il y a 10 rangs à conquérir : Fer, Bronze, Argent, Or, Platine, Émeraude, Diamant, Maître, Grand Maître, et Challenger. Pour passer d’un rang à un autre (ex. : Bronze 1 à Argent 3), vous devez compléter les 3 sous-rangs du rang actuel.</li>
                <li><strong>Comparez-vous aux autres :</strong> Consultez le classement global pour voir votre position par rapport aux autres utilisateurs et visez le top 100 !</li>
            </ul>
            <p class="text-gray-600 mt-4">
                Prêt à commencer ? Ajoutez votre première candidature et commencez votre ascension dans les rangs !
            </p>
            <div class="text-center mt-6">
                <a href="/welcome" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                    ← Retour à la page de bienvenue
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>