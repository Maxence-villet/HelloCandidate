<?php require 'layout.php'; ?>
    <div class="container mx-auto p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Gérer le groupe : <?php echo htmlspecialchars($groupName); ?></h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form to add a student -->
        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h3 class="text-xl font-semibold mb-4">Ajouter un étudiant</h3>
            <form action="/group/add/<?php echo $groupId; ?>" method="POST">
                <div class="mb-4">
                    <label for="identifier" class="block text-gray-700">Nom d'utilisateur ou e-mail de l'étudiant</label>
                    <input type="text" name="identifier" id="identifier" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Ajouter</button>
            </form>
        </div>

        <!-- List of group members -->
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-semibold mb-4">Membres du groupe</h3>
            <?php if (empty($members)): ?>
                <p class="text-gray-600">Aucun étudiant dans ce groupe pour le moment.</p>
            <?php else: ?>
                <table class="w-full text-left">
                    <thead>
                        <tr>
                            <th class="p-2 border-b">Nom d'utilisateur</th>
                            <th class="p-2 border-b">Rang</th>
                            <th class="p-2 border-b">Nombre de candidatures</th>
                            <th class="p-2 border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $member): ?>
                            <tr>
                                <td class="p-2 border-b"><?php echo htmlspecialchars($member['username']); ?></td>
                                <td class="p-2 border-b">
                                    <?php echo $member['rank_name'] ? htmlspecialchars($member['rank_name'] . ' ' . $member['sub_rank']) : 'Non classé'; ?>
                                </td>
                                <td class="p-2 border-b"><?php echo htmlspecialchars($member['candidature_count']); ?></td>
                                <td class="p-2 border-b">
                                    <a href="/remove-student-from-group/<?php echo $groupId; ?>/<?php echo $member['user_id']; ?>" class="text-red-500 hover:underline">Retirer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <p class="mt-6 text-center">
            <a href="/spectator-dashboard" class="text-blue-500 hover:underline">Retour au tableau de bord</a>
        </p>
    </div>
</body>
</html>