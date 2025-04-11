<?php 
if($_SESSION["user_type"] == "spectator") {
    include __DIR__ . '/../utils/header/header_spectator.php';
} else {
    include __DIR__ . '/../utils/header/header_student.php';
}
?> 

<!-- Main Content -->
<main class="flex-1 p-4 sm:p-6 md:p-8 items-center">
    <div class="container mx-auto">
        <!-- Titre et position de l'utilisateur -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8 gap-4">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path>
                </svg>
                <h1 class="text-2xl font-bold text-gray-900">Classement Global</h1>
            </div>
            <?php if ($userPosition): ?>
                <p class="text-gray-700 text-sm font-medium">
                    Votre position : <span class="text-blue-600 font-semibold"><?php echo $userPosition; ?>e</span>
                </p>
            <?php endif; ?>
        </div>

        <!-- Formulaire de filtrage -->
        <form method="POST" class="mb-8 flex flex-col sm:flex-row gap-4">
            <!-- Filtre par rang -->
            <div class="flex-1">
                <label for="rank_filter" class="block text-sm font-medium text-gray-700 mb-1.5">Filtrer par rang</label>
                <select 
                    name="rank_filter" 
                    id="rank_filter" 
                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                >
                    <option value="">Tous les rangs</option>
                    <?php foreach ($sortedRankGroups as $rankName => $subRanks): ?>
                        <option value="<?php echo htmlspecialchars($rankName); ?>" <?php echo ($rankFilter === $rankName) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($rankName); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filtre par sous-rang -->
            <div class="flex-1">
                <label for="sub_rank_filter" class="block text-sm font-medium text-gray-700 mb-1.5">Filtrer par sous-rang</label>
                <select 
                    name="sub_rank_filter" 
                    id="sub_rank_filter" 
                    class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                >
                    <option value="">Tous les sous-rangs</option>
                    <?php for ($subRank = 3; $subRank >= 1; $subRank--): ?>
                        <option value="<?php echo $subRank; ?>" <?php echo ($subRankFilter == $subRank) ? 'selected' : ''; ?>>
                            <?php echo $subRank; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Bouton de filtrage -->
            <div class="flex items-end">
                <button 
                    type="submit" 
                    class="w-full sm:w-auto px-4 py-2 bg-blue-600 text-white font-medium rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200"
                >
                    Filtrer
                </button>
            </div>
        </form>

        <!-- Liste des étudiants -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <?php if (empty($users)): ?>
                <div class="p-8 text-center">
                    <p class="text-gray-500">Aucun étudiant ne correspond aux critères sélectionnés.</p>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($users as $index => $user): ?>
                        <li class="px-6 py-4 flex justify-between items-center <?php echo $index % 2 === 0 ? 'bg-gray-50' : 'bg-white'; ?>">
                            <!-- Nom et rang -->
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-800 font-medium"><?php echo htmlspecialchars($user['username']); ?></span>
                                <span class="text-sm text-gray-500">
                                    (<?php echo htmlspecialchars($user['rank_name'] . ' ' . $user['sub_rank']); ?>)
                                </span>
                            </div>
                            <!-- Nombre de candidatures et icône -->
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-600"><?php echo $user['candidature_count']; ?></span>
                                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>