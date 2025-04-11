<?php 
if($_SESSION["user_type"] == "student") {
    include __DIR__ . '/../../utils/header/header_student.php';
} else {
    header('Location: /');
}
?>    

<!-- Main Content -->
<main class="flex-1 p-4 sm:p-6 md:p-8 items-center">
    <div class="container mx-auto">
        <!-- Titre -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8 gap-4">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h1 class="text-2xl font-bold text-gray-900">Mes notifications</h1>
            </div>
        </div>

        <!-- Contenu des notifications -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <?php if (empty($notifications)): ?>
                <div class="p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune notification</h3>
                    <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore de notifications. Ajoutez une candidature pour en recevoir !</p>
                    <div class="mt-6">
                        <a href="/applications/add" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Ajouter une candidature
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($notifications as $notification): ?>
                        <li class="px-6 py-4 flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800"><?php echo htmlspecialchars($notification['message']); ?></p>
                                <p class="text-sm text-gray-500">
                                    <?php echo date('d/m/Y à H:i', strtotime($notification['created_at'])); ?>
                                </p>
                                <?php 
                                    // Vérifier si le message est une invitation (simplifié sans regex)
                                    $message = $notification['message'];
                                    if (strpos($message, "Vous avez été invité à rejoindre le groupe") !== false) {
                                        // Extraire l'ID du groupe (supposons que le message contient "group_id:X" à la fin)
                                        $groupId = 0;
                                        $parts = explode("group_id:", $message);
                                        if (count($parts) > 1) {
                                            $groupId = (int)$parts[1];
                                        }
                                        if ($groupId > 0):
                                ?>
                                    <div class="mt-2 flex space-x-4">
                                        <!-- Formulaire pour accepter -->
                                        <form action="/notifications/handle" method="POST">
                                            <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                                            <input type="hidden" name="action" value="accept">
                                            <input type="hidden" name="group_id" value="<?php echo $groupId; ?>">
                                            <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                                Accepter
                                            </button>
                                        </form>
                                        <!-- Formulaire pour refuser -->
                                        <form action="/notifications/handle" method="POST">
                                            <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                                            <input type="hidden" name="action" value="refuse">
                                            <input type="hidden" name="group_id" value="<?php echo $groupId; ?>">
                                            <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                                Refuser
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; } ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</main>
</body>
</html>