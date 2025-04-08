<?php require __DIR__ . '/../layout.php'; ?>
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Gérer le groupe : <?php echo htmlspecialchars($groupName); ?></h2>
        </div>

        <!-- Messages d'alerte -->
        <?php if (isset($success)): ?>
            <div class="rounded-md bg-green-50 p-4">
                <p class="text-sm font-medium text-green-800"><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="rounded-md bg-red-50 p-4">
                <p class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($error); ?></p>
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

        <!-- Formulaire d'ajout d'étudiant -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ajouter un étudiant</h3>
                <form action="/group/add" method="POST">
                    <input type="hidden" name="group_id" value="<?php echo $groupId; ?>">
                    <div class="mb-4">
                        <label for="identifier" class="block text-sm font-medium text-gray-700 mb-1">Nom d'utilisateur ou adresse e-mail de l'étudiant</label>
                        <input type="text" name="identifier" id="identifier" required
                            placeholder="ex: etudiant123 ou etudiant@example.com"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Ajouter
                    </button>
                </form>
            </div>
        </div>

        <!-- Liste des membres du groupe -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Membres du groupe</h3>
                <?php if (empty($members)): ?>
                    <p class="text-sm text-gray-500 text-center py-4">Aucun étudiant dans ce groupe pour le moment.</p>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom d'utilisateur</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rang</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Candidatures</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($members as $member): ?>
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($member['username']); ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $member['rank_name'] ? htmlspecialchars($member['rank_name'] . ' ' . $member['sub_rank']) : 'Non classé'; ?>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($member['candidature_count']); ?></td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                        <form action="/group/remove" method="POST">
                                            <input type="hidden" name="group_id" value="<?php echo $groupId; ?>">
                                            <input type="hidden" name="student_id" value="<?php echo $member['user_id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900">Retirer</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center pt-4">
            <a href="/spectator/dashboard" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                ← Retour au tableau de bord
            </a>
        </div>
    </div>
</div>
</body>
</html>