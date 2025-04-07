<?php require __DIR__ . '/../layout.php'; ?>

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Créer un groupe</h2>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/group/create" method="POST">
            <div class="mb-4">
                <label for="group_name" class="block text-gray-700">Nom du groupe</label>
                <input type="text" name="group_name" id="group_name" value="<?php echo htmlspecialchars($groupName ?? ''); ?>" class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Créer le groupe</button>
        </form>

        <p class="mt-4 text-center">
            <a href="/spectator-dashboard" class="text-blue-500 hover:underline">Retour au tableau de bord</a>
        </p>
    </div>
</body>
</html>