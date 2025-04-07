
<?php require 'layout.php'; ?>

    <div class="container mx-auto p-6">
        <!-- Message de bienvenue -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <h1 class="text-3xl font-bold text-center text-indigo-600 mb-4">
                Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !
            </h1>
            <p class="text-center text-gray-600">
                Vous êtes maintenant inscrit sur HelloCandidate, la plateforme qui vous aide à trouver une alternance tout en vous motivant grâce à un système de gamification unique.
            </p>
        </div>

        <!-- Explication du système de gamification -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-semibold text-indigo-600 mb-4">Comment fonctionne notre système de gamification ?</h2>
            <p class="text-gray-600 mb-4">
                Chez HelloCandidate, nous avons conçu un système de gamification pour rendre votre recherche d'alternance plus engageante et motivante. Voici les points clés :
            </p>
            <ul class="list-disc list-inside space-y-2 text-gray-600">
                <li><strong>Envoyez des candidatures pour progresser :</strong> Chaque candidature validée vous rapproche d'un nouveau sous-rang. Par exemple, 10 candidatures vous font passer de Bronze 3 à Bronze 2.</li>
                <li><strong>Montez dans les rangs :</strong> Il y a 8 rangs à conquérir : Bronze, Argent, Or, Émeraude, Diamant, Maître, Challenger, et Grand Challenger. Pour passer d’un rang à un autre (ex. : Bronze 1 à Argent 3), vous devez compléter les 3 sous-rangs du rang actuel.</li>
                <li><strong>Déclassement hebdomadaire :</strong> Chaque lundi à minuit, votre compteur de candidatures diminue de 30. Si votre compteur tombe en dessous du minimum requis pour votre sous-rang, vous descendez d’un sous-rang (mais jamais en dessous de Bronze 3).</li>
                <li><strong>Comparez-vous aux autres :</strong> Consultez le classement global pour voir votre position par rapport aux autres utilisateurs et visez le top 100 !</li>
            </ul>
            <p class="text-gray-600 mt-4">
                Prêt à commencer ? Ajoutez votre première candidature et commencez votre ascension dans les rangs !
            </p>
            <div class="text-center mt-6">
                <a href="/dashboard" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700">
                    Aller au tableau de bord
                </a>
            </div>
        </div>
    </div>
</body>
</html>