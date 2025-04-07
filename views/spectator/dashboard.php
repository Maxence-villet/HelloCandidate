<?php require __DIR__ . '/../layout.php'; ?>
    <div class="container mx-auto p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Tableau de bord Spectateur</h2>
        <p class="text-center mb-4">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !</p>

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

        <div class="bg-white p-6 rounded-lg shadow-lg mb-6">
            <h3 class="text-xl font-semibold mb-4">Vos groupes</h3>
            <a href="/group/create" class="inline-block bg-blue-500 text-white p-2 rounded hover:bg-blue-600 mb-4">Créer un nouveau groupe</a>
            <?php if (empty($groups)): ?>
                <p class="text-gray-600">Vous n'avez créé aucun groupe pour le moment.</p>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($groups as $group): ?>
                        <li class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span><?php echo htmlspecialchars($group['group_name']); ?></span>
                            <a href="/manage-group/<?php echo $group['group_id']; ?>" class="text-blue-500 hover:underline">Gérer</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>