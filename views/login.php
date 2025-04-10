<?php
$page_title = 'Connexion';
include __DIR__ . '/../utils/header/header_auth.php';
?>

<!-- Main Content -->
<main class="flex items-center justify-center min-h-[calc(100vh-64px)] p-4">
    <div class="bg-white p-6 sm:p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6 text-center flex items-center justify-center">
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
            </svg>
            Connexion
        </h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-6">
                <p class="text-sm"><?php echo htmlspecialchars($_GET['success']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul class="list-disc pl-5 space-y-1">
                    <?php foreach ($errors as $error): ?>
                        <li class="text-sm"><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Adresse e-mail</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition duration-150 ease-in-out" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition duration-150 ease-in-out" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white p-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out">Se connecter</button>
        </form>
        <p class="mt-6 text-center text-sm text-gray-600">
            Pas encore de compte ? <a href="/register" class="text-blue-600 hover:underline">S'inscrire</a>
        </p>
    </div>
</main>
</body>
</html>