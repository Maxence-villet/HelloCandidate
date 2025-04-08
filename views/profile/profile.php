<?php require __DIR__ . '/../layout.php'; ?>
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Profil de <?php echo htmlspecialchars($user['username']); ?></h2>
        </div>

        <!-- Messages d'alerte -->
        <?php if (isset($success)): ?>
            <div class="rounded-md bg-green-50 p-4">
                <p class="text-sm font-medium text-green-800"><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="rounded-md bg-red-50 p-4">
                <ul class="list-disc pl-5 space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Side: Rank, Progress, Badges -->
            <div class="col-span-1 bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200 p-6">
                <!-- Rank Image -->
                <div class="flex justify-center mb-4">
                    <?php
                    // Normalize the rank_name to match the image filename
                    $rankImageName = strtolower($user['rank_name']);
                    $rankImageName = str_replace(' ', '-', $rankImageName); // e.g., "Grand Master" -> "grand-master"
                    $rankImagePath = "/public/rank/{$rankImageName}.png";
                    ?>
                    <img src="<?php echo htmlspecialchars($rankImagePath); ?>" alt="<?php echo htmlspecialchars($user['rank_name']); ?>" class="w-24 h-24">
                </div>

                <!-- Rank and Sub-Rank Info -->
                <h3 class="text-lg font-medium text-gray-900 text-center"><?php echo htmlspecialchars($user['rank_name'] . ' ' . $user['sub_rank']); ?></h3>
                <p class="text-sm text-gray-500 text-center">Candidatures : <?php echo htmlspecialchars($user['candidature_count']); ?></p>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <p class="text-sm text-gray-600">Progrès vers le prochain rang :</p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                </div>

                <!-- Badges -->
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

            <!-- Right Side: Bio -->
            <div class="col-span-2 bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bio</h3>
                <?php if ($isOwnProfile): ?>
                    <form action="/profile" method="POST">
                        <input type="hidden" name="action" value="update_bio">
                        <div class="mb-4">
                            <textarea name="bio" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Mettre à jour
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-gray-600"><?php echo htmlspecialchars($user['bio'] ?? 'Aucune bio définie.'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center pt-4">
            <a href="/welcome" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                ← Retour au tableau de bord
            </a>
        </div>
    </div>
</div>
</body>
</html>