<?php
$page_title = 'Profil de ' . htmlspecialchars($user['username']);
include __DIR__ . '/../../utils/header.php';
?>

<!-- Main Content -->
<main class="flex-1 p-4 sm:p-6 md:p-8">
    <div class="max-w-4xl mx-auto space-y-6">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center mb-6">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            Profil de <?php echo htmlspecialchars($user['username']); ?>
        </h2>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
                <p class="text-sm font-medium"><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc pl-5 space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li class="text-sm font-medium"><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Rank Card -->
            <div class="col-span-1 bg-white shadow-sm rounded-lg border border-gray-200 p-4 sm:p-6">
                <?php
                $rankImageName = strtolower($user['rank_name']);
                $rankImageName = str_replace(' ', '-', $rankImageName);
                $rankImagePath = "/public/rank/{$rankImageName}.png";
                ?>
                <div class="flex justify-center mb-4">
                    <img src="<?php echo htmlspecialchars($rankImagePath); ?>" alt="<?php echo htmlspecialchars($user['rank_name']); ?>" class="w-20 h-20 sm:w-24 sm:h-24">
                </div>
                <h3 class="text-lg font-medium text-gray-900 text-center"><?php echo htmlspecialchars($user['rank_name'] . ' ' . $user['sub_rank']); ?></h3>
                <p class="text-sm text-gray-500 text-center">Candidatures : <?php echo htmlspecialchars($user['candidature_count']); ?></p>
                <div class="mt-4">
                    <p class="text-sm text-gray-600">Progrès vers le prochain rang :</p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                </div>
                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-2">Badges</h4>
                    <?php if (empty($badges)): ?>
                        <p class="text-sm text-gray-500">Aucun badge débloqué pour le moment.</p>
                    <?php else: ?>
                        <div class="space-y-2">
                            <?php foreach ($badges as $badge): ?>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-block bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded"><?php echo htmlspecialchars($badge['badge_name']); ?></span>
                                    <span class="text-sm text-gray-600"><?php echo htmlspecialchars($badge['description']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bio and Activity Section -->
            <div class="col-span-1 md:col-span-2 bg-white shadow-sm rounded-lg border border-gray-200 p-4 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bio</h3>
                <?php if ($isOwnProfile): ?>
                    <form action="/profile" method="POST">
                        <input type="hidden" name="action" value="update_bio">
                        <div class="mb-4">
                            <textarea name="bio" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Mettre à jour
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($user['bio'] ?? 'Aucune bio définie.'); ?></p>
                <?php endif; ?>

                <!-- Tableau d'activité -->
                <div class="mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Activité des candidatures (6 derniers mois)</h3>
                    <div class="flex">
                        <!-- Étiquettes des jours de la semaine (Lun à Dim) - Masquées sur mobile -->
                        <div class="hidden sm:flex flex-col justify-between mr-2 text-xs text-gray-600" style="height: 84px;">
                            <span>Lun</span>
                            <span>Mar</span>
                            <span>Mer</span>
                            <span>Jeu</span>
                            <span>Ven</span>
                            <span>Sam</span>
                            <span>Dim</span>
                        </div>
                        <!-- Grille -->
                        <?php
                        // Calculer le nombre de semaines sur 6 mois
                        $startDate = new DateTime('-6 months');
                        $endDate = new DateTime();
                        $interval = new DateInterval('P1D');
                        $dateRange = new DatePeriod($startDate, $interval, $endDate->modify('+1 day')); // Inclure aujourd'hui

                        // Calculer le nombre total de jours
                        $totalDays = iterator_count($dateRange);
                        $numberOfWeeks = ceil($totalDays / 7); // Nombre de semaines nécessaires

                        // Réinitialiser le dateRange pour l'utiliser dans la boucle
                        $dateRange = new DatePeriod($startDate, $interval, $endDate);

                        // Définir la date actuelle pour comparaison
                        $today = (new DateTime())->format('Y-m-d');
                        ?>
                        <div class="grid gap-0.5 sm:gap-1" style="grid-template-rows: repeat(7, minmax(0, 1fr)); grid-template-columns: repeat(<?php echo $numberOfWeeks; ?>, minmax(0, 1fr)); width: 100%; max-width: calc(100% - 0px); sm:max-width: calc(100% - 40px);">
                            <?php
                            $dayCounter = 0;

                            foreach ($dateRange as $date) {
                                $dateStr = $date->format('Y-m-d');
                                // Ajuster le jour de la semaine pour commencer par lundi (0 = lundi, ..., 6 = dimanche)
                                $dayOfWeek = (int)$date->format('w'); // 0 (dimanche) à 6 (samedi)
                                $adjustedDayOfWeek = ($dayOfWeek + 6) % 7; // Décale pour que lundi = 0, dimanche = 6

                                // Calculer la position dans la grille
                                $row = $adjustedDayOfWeek; // La ligne correspond au jour de la semaine ajusté (0 à 6)
                                $col = floor($dayCounter / 7); // La colonne correspond à la semaine

                                // Récupérer les données d'activité
                                $intensity = $activityData[$dateStr]['intensity'] ?? 0;
                                $count = $activityData[$dateStr]['count'] ?? 0;

                                // Déterminer la couleur
                                switch ($intensity) {
                                    case 0:
                                        $colorClass = 'bg-gray-100';
                                        break;
                                    case 1:
                                        $colorClass = 'bg-green-100';
                                        break;
                                    case 2:
                                        $colorClass = 'bg-green-300';
                                        break;
                                    case 3:
                                        $colorClass = 'bg-green-500';
                                        break;
                                    case 4:
                                        $colorClass = 'bg-green-700';
                                        break;
                                    default:
                                        $colorClass = 'bg-gray-100';
                                        break;
                                }

                                // Ajouter un contour blanc si c'est le jour actuel
                                $borderClass = ($dateStr === $today) ? 'border border-white border-2' : '';

                                // Ajuster la taille des cases : 2x2 sur mobile, 3x3 sur PC
                                ?>
                                <div class="w-2 h-2 sm:w-3 sm:h-3 <?php echo $colorClass; ?> <?php echo $borderClass; ?> rounded-sm" style="grid-row: <?php echo $row + 1; ?>; grid-column: <?php echo $col + 1; ?>;" title="<?php echo htmlspecialchars($date->format('d/m/Y') . " : $count candidature(s)"); ?>"></div>
                                <?php
                                $dayCounter++;
                            }
                            ?>
                        </div>
                    </div>
                    <!-- Légende -->
                    <div class="mt-2 flex items-center space-x-2 text-sm text-gray-600">
                        <span>Moins</span>
                        <div class="w-3 h-3 bg-gray-100 rounded-sm"></div>
                        <div class="w-3 h-3 bg-green-100 rounded-sm"></div>
                        <div class="w-3 h-3 bg-green-300 rounded-sm"></div>
                        <div class="w-3 h-3 bg-green-500 rounded-sm"></div>
                        <div class="w-3 h-3 bg-green-700 rounded-sm"></div>
                        <span>Plus</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center pt-4">
            <a href="/welcome" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                ← Retour au tableau de bord
            </a>
        </div>
    </div>
</main>
</div>
</body>
</html>