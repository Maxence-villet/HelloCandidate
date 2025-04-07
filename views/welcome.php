<?php 
    
    session_start();

    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'spectator') {
        header('Location: /spectator/dashboard');
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    include __DIR__ . '/../../utils/header.php'; 
    
?>
    <?php include __DIR__ . '/../utils/header.php'; ?>

    <div class="container mx-auto p-6">
        <!-- Message de bienvenue -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h1 class="text-3xl font-bold text-center text-blue-600 mb-4">
                Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !
            </h1>
            <p class="text-center text-gray-600">
                Vous êtes maintenant inscrit sur HelloCandidate, la plateforme qui vous aide à trouver une alternance tout en vous motivant grâce à un système de gamification unique.
            </p>
        </div>

        <!-- Section de progression (centrée) -->
        <div class="flex justify-center items-center mb-6">
            <div class="text-center">
                <!-- Image du rang (placeholder) -->
                <div class="mb-4">
                    <svg class="mx-auto h-16 w-16 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 9.143l-5.714 2.714L13 21l-2.286-9.143L5 9.143l5.714-2.714L13 3z" />
                    </svg>
                </div>

                <!-- Informations du rang -->
                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                    <?php echo htmlspecialchars($currentRank['rank_name'] . ' ' . $currentRank['sub_rank']); ?>
                </h3>

                <!-- Barre de progression -->
                <div class="w-64 bg-gray-200 rounded-full h-4 mb-2">
                    <div class="bg-blue-600 h-4 rounded-full" style="width: <?php echo $progressPercentage; ?>%;" role="progressbar" aria-valuenow="<?php echo $progressPercentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <!-- Nombre de candidatures -->
                <p class="text-gray-600">
                    <?php
                    if ($nextMinApplications) {
                        echo "$candidatureCount / $nextMinApplications candidatures";
                    } else {
                        echo "$candidatureCount candidatures (maximum atteint)";
                    }
                    ?>
                </p>
            </div>
        </div>

        <!-- Explication du système de gamification -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-semibold text-blue-600 mb-4">Comment fonctionne notre système de gamification ?</h2>
            <p class="text-gray-600 mb-4">
                Chez HelloCandidate, nous avons conçu un système de gamification pour rendre votre recherche d'alternance plus engageante et motivante. Voici les points clés :
            </p>
            <ul class="list-disc list-inside space-y-2 text-gray-600">
                <li><strong>Envoyez des candidatures pour progresser :</strong> Chaque candidature validée vous rapproche d'un nouveau sous-rang. Par exemple, 10 candidatures vous font passer de Fer 3 à Fer 2.</li>
                <li><strong>Montez dans les rangs :</strong> Il y a 10 rangs à conquérir : Fer, Bronze, Argent, Or, Platine, Émeraude, Diamant, Maître, Grand Maître, et Challenger. Pour passer d’un rang à un autre (ex. : Bronze 1 à Argent 3), vous devez compléter les 3 sous-rangs du rang actuel.</li>
                <li><strong>Déclassement hebdomadaire :</strong> Chaque lundi à minuit, votre compteur de candidatures diminue de 30. Si votre compteur tombe en dessous du minimum requis pour votre sous-rang, vous descendez d’un sous-rang (mais jamais en dessous de Fer 3).</li>
                <li><strong>Comparez-vous aux autres :</strong> Consultez le classement global pour voir votre position par rapport aux autres utilisateurs et visez le top 100 !</li>
            </ul>
            <p class="text-gray-600 mt-4">
                Prêt à commencer ? Ajoutez votre première candidature et commencez votre ascension dans les rangs !
            </p>
            <div class="text-center mt-6">
                <a href="/dashboard" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700">
                    Aller au tableau de bord
                </a>
            </div>
        </div>
    </div>
</body>
</html>