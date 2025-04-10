<?php
$page_title = 'Inscription';
include __DIR__ . '/../utils/header_auth.php';
?>

<!-- Main Content -->
<main class="flex items-center justify-center min-h-[calc(100vh-64px)] p-4">
    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6 text-center flex items-center justify-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            Inscription
        </h2>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc pl-5 space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li class="text-sm"><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Nom d'utilisateur</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition duration-150 ease-in-out" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Adresse e-mail</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition duration-150 ease-in-out" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition duration-150 ease-in-out" required>
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe</label>
                <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition duration-150 ease-in-out" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out">S'inscrire</button>
        </form>
        <p class="mt-6 text-center text-sm text-gray-600">
            Déjà un compte ? <a href="/login" class="text-blue-600 hover:underline">Se connecter</a>
        </p>
    </div>
</main>
</body>
</html>