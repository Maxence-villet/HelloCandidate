<?php require 'layout.php'; ?>
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-bold text-center mb-6">Connexion</h2>

            <?php if (isset($_GET['success'])): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                    <?php echo htmlspecialchars($_GET['success']); ?>
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

            <form action="/login" method="POST" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Adresse e-mail</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                </div>
                <button type="submit" class="w-full bg-indigo-600 text-white p-2 rounded-md hover:bg-indigo-700">Se connecter</button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                Pas encore de compte ? <a href="/register" class="text-indigo-600 hover:underline">S'inscrire</a>
            </p>
        </div>
    </div>
</body>
</html>