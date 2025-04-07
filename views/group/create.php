<?php require __DIR__ . '/../layout.php'; ?>

<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Créer un groupe</h2>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="rounded-md bg-red-50 p-4">
                <ul class="list-disc pl-5 space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="bg-white py-8 px-6 shadow-sm rounded-lg border border-gray-200">
            <form class="space-y-6" action="/group/create" method="POST">
                <div>
                    <label for="group_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du groupe</label>
                    <input type="text" name="group_name" id="group_name" required 
                        value="<?php echo htmlspecialchars($groupName ?? ''); ?>"
                        class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Créer le groupe
                    </button>
                </div>
            </form>

            <div class="mt-4 text-center">
                <a href="/spectator/dashboard" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                    ← Retour au tableau de bord
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>