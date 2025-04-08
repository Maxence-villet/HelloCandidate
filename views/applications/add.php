<?php 
    
    session_start();

    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'spectator') {
        header('Location: /spectator/dashboard');
        exit;
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    include __DIR__ . '/../../utils/header.php'; 
    
?>
    
    <div class="container mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Ajouter une candidature</h1>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/applications/add" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700">Nom de l'entreprise</label>
                    <input type="text" name="company_name" id="company_name" value="<?php echo htmlspecialchars($companyName ?? ''); ?>" 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="position" class="block text-sm font-medium text-gray-700">Poste</label>
                    <input type="text" name="position" id="position" value="<?php echo htmlspecialchars($position ?? ''); ?>" 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="submission_date" class="block text-sm font-medium text-gray-700">Date de soumission</label>
                    <input type="date" name="submission_date" id="submission_date" value="<?php echo htmlspecialchars($submissionDate ?? date('Y-m-d')); ?>" 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                    <select name="status" id="status" class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="pending" <?php echo ($status ?? 'pending') === 'pending' ? 'selected' : ''; ?>>En attente</option>
                        <option value="interview" <?php echo ($status ?? '') === 'interview' ? 'selected' : ''; ?>>Entretien</option>
                        <option value="rejected" <?php echo ($status ?? '') === 'rejected' ? 'selected' : ''; ?>>Refusé</option>
                        <option value="accepted" <?php echo ($status ?? '') === 'accepted' ? 'selected' : ''; ?>>Accepté</option>
                    </select>
                </div>
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Adresse (optionnel)</label>
                    <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($address ?? ''); ?>" 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="offer_link" class="block text-sm font-medium text-gray-700">Lien de l'offre (optionnel)</label>
                    <input type="url" name="offer_link" id="offer_link" value="<?php echo htmlspecialchars($offer_link ?? ''); ?>" 
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="mt-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description (optionnel)</label>
                <textarea name="description" id="description" rows="4" 
                    class="mt-1 block w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
            </div>
            <div class="mt-4">
                <label for="cover_letter" class="block text-sm font-medium text-gray-700">Lettre de motivation (PDF, max 5MB, optionnel)</label>
                <input type="file" name="cover_letter" id="cover_letter" accept="application/pdf" 
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100">
            </div>
            <div class="mt-6">
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Ajouter la candidature
                </button>
            </div>
        </form>
    </div>
</body>
</html>