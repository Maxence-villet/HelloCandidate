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
    <?php if (isset($_SESSION['rank_up_message'])): ?>
        <div class="bg-blue-600 text-white p-4 rounded-lg shadow-lg mx-auto max-w-lg text-center animate-bounce">
            <p class="font-semibold"><?php echo htmlspecialchars($_SESSION['rank_up_message']); ?></p>
        </div>
        <?php unset($_SESSION['rank_up_message']); // Supprimer après affichage ?>
    <?php endif; ?>
    <div class="container mx-auto py-8 px-4">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Mes candidatures</h1>
            <a href="/applications/add" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                + Ajouter une candidature
            </a>
        </div>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
                <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                <?php unset($_SESSION['success_message']); // Supprimer le message après affichage ?>
            </div>
        <?php endif; ?>
        
        <div class="mb-6 flex space-x-2">
            <!-- Les liens de filtrage rapide soumettent le formulaire avec un statut spécifique -->
            <form id="filter-form" action="/applications" method="POST" class="flex space-x-2">
                <input type="hidden" name="status" id="status-hidden">
                <button type="submit" onclick="document.getElementById('status-hidden').value=''" class="px-4 py-2 <?php echo !isset($_POST['status']) ? 'bg-blue-600 text-white' : 'bg-gray-200'; ?> rounded-md">
                    Toutes
                </button>
                <button type="submit" onclick="document.getElementById('status-hidden').value='pending'" class="px-4 py-2 <?php echo ($_POST['status'] ?? '') === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-200'; ?> rounded-md">
                    En attente
                </button>
                <button type="submit" onclick="document.getElementById('status-hidden').value='interview'" class="px-4 py-2 <?php echo ($_POST['status'] ?? '') === 'interview' ? 'bg-blue-600 text-white' : 'bg-gray-200'; ?> rounded-md">
                    Entretien
                </button>
                <button type="submit" onclick="document.getElementById('status-hidden').value='rejected'" class="px-4 py-2 <?php echo ($_POST['status'] ?? '') === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-200'; ?> rounded-md">
                    Refusées
                </button>
                <button type="submit" onclick="document.getElementById('status-hidden').value='accepted'" class="px-4 py-2 <?php echo ($_POST['status'] ?? '') === 'accepted' ? 'bg-green-600 text-white' : 'bg-gray-200'; ?> rounded-md">
                    Acceptées
                </button>
            </form>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form action="/applications" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Barre de recherche -->
                    <div class="md:col-span-2">
                        <label for="search" class="sr-only">Rechercher</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="<?= htmlspecialchars($_POST['search'] ?? '') ?>" 
                                placeholder="Entreprise, poste ou description..." 
                                class="block w-full pl-4 pr-10 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <button type="submit" class="absolute right-2 top-2 text-gray-400 hover:text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Filtre par statut -->
                    <div>
                        <label for="status" class="sr-only">Statut</label>
                        <select name="status" id="status" class="block w-full py-2 px-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Tous statuts</option>
                            <option value="pending" <?= ($_POST['status'] ?? '') === 'pending' ? 'selected' : '' ?>>En attente</option>
                            <option value="interview" <?= ($_POST['status'] ?? '') === 'interview' ? 'selected' : '' ?>>Entretien</option>
                            <option value="rejected" <?= ($_POST['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Refusé</option>
                            <option value="accepted" <?= ($_POST['status'] ?? '') === 'accepted' ? 'selected' : '' ?>>Accepté</option>
                        </select>
                    </div>

                    <!-- Bouton de recherche -->
                    <div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Filtrer
                        </button>
                    </div>
                </div>

                <!-- Filtres avancés (plage de dates) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">À partir du</label>
                        <input type="date" name="date_from" id="date_from" value="<?= htmlspecialchars($_POST['date_from'] ?? '') ?>"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">Jusqu'au</label>
                        <input type="date" name="date_to" id="date_to" value="<?= htmlspecialchars($_POST['date_to'] ?? '') ?>"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <button type="submit" name="reset" value="1" class="inline-block w-full bg-gray-200 text-gray-800 py-2 px-4 rounded-md hover:bg-gray-300 text-center">
                            Réinitialiser
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <?php if (!empty($_POST) && !isset($_POST['reset'])): ?>
            <div class="mb-4 text-sm text-gray-500">
                <?php
                $filters = [];
                if (!empty($_POST['search'])) $filters[] = 'recherche : "' . htmlspecialchars($_POST['search']) . '"';
                if (!empty($_POST['status'])) $filters[] = 'statut : ' . htmlspecialchars($_POST['status']);
                if (!empty($_POST['date_from'])) $filters[] = 'à partir du ' . htmlspecialchars($_POST['date_from']);
                if (!empty($_POST['date_to'])) $filters[] = 'jusqu\'au ' . htmlspecialchars($_POST['date_to']);
                
                if (!empty($filters)) {
                    echo count($applications) . ' candidatures trouvées avec ' . implode(', ', $filters);
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <?php if (empty($applications)): ?>
                <div class="p-8 text-center empty-state">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune candidature trouvée</h3>
                    <p class="mt-1 text-sm text-gray-500">Essayez d'ajuster vos critères de recherche.</p>
                    <div class="mt-6">
                        <a href="/applications/add" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Ajouter une candidature
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entreprise</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poste</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($applications as $app): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($app['company_name']); ?></div>
                                <?php if ($app['address']): ?>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($app['address']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-gray-900"><?php echo htmlspecialchars($app['position']); ?></div>
                                <?php if ($app['offer_link']): ?>
                                    <a href="<?php echo htmlspecialchars($app['offer_link']); ?>" target="_blank" class="text-sm text-blue-600 hover:underline">Voir l'offre</a>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($app['submission_date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'interview' => 'bg-blue-100 text-blue-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'accepted' => 'bg-green-100 text-green-800'
                                    ];
                                    $statusText = [
                                        'pending' => 'En attente',
                                        'interview' => 'Entretien',
                                        'rejected' => 'Refusé',
                                        'accepted' => 'Accepté'
                                    ];
                                ?>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusClasses[$app['status']]; ?>">
                                    <?php echo $statusText[$app['status']]; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="/applications/view/<?php echo $app['application_id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Voir</a>
                                <?php if ($app['cover_letter_path']): ?>
                                    <a href="<?php echo "public" . $app['cover_letter_path']; ?>" target="_blank" class="text-blue-600 hover:text-blue-900">Lettre</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>