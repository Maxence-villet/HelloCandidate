<?php
$page_title = 'Spectator Dashboard';
$current_page = 'spectator_dashboard';
include __DIR__ . '/../../utils/header/header_spectator.php';
?>
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-sm">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Tableau de bord Spectateur</h2>
            <p class="mt-2 text-sm text-gray-600">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="rounded-md bg-green-50 p-4">
                <p class="text-sm font-medium text-green-800"><?php echo htmlspecialchars($_GET['success']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="rounded-md bg-red-50 p-4">
                <p class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($_GET['error']); ?></p>
            </div>
        <?php endif; ?>

        <div class="space-y-6">
            <div class="rounded-lg bg-white border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Vos groupes</h3>
                    <a href="/group/create" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Créer un groupe
                    </a>
                </div>

                <?php if (empty($groups)): ?>
                    <p class="text-sm text-gray-500 text-center py-4">Vous n'avez créé aucun groupe pour le moment.</p>
                <?php else: ?>
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($groups as $group): ?>
                            <li class="py-4 flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($group['group_name']); ?></span>
                                <form action="/group/manage" method="POST">
                                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                    <button type="submit" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                                        Gérer
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="text-center">
                <a href="/logout" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                    Se déconnecter
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>