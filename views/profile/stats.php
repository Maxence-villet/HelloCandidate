<?php
$page_title = 'User Profile';
$current_page = 'user_profil';
include __DIR__ . '/../../utils/header/header_spectator.php';

// Inclure la connexion à la base de données
require_once __DIR__ . '/../../utils/database.php';

// Récupérer l'user_id depuis le POST
$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;

// Initialiser la connexion à la base de données
$db = new Database();
$conn = $db->getConnection();

// Récupérer le nom d'utilisateur
$query = "SELECT username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$username = $user ? $user['username'] : 'Utilisateur inconnu';

// Récupérer les stats des 30 derniers jours
$query = "SELECT DATE(submission_date) as date, COUNT(*) as count 
          FROM applications 
          WHERE user_id = ? 
          AND submission_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
          GROUP BY DATE(submission_date)
          ORDER BY submission_date ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$dailyStats = $result->fetch_all(MYSQLI_ASSOC);

// Calcul de la moyenne
$totalApplications = array_sum(array_column($dailyStats, 'count'));
$averagePerDay = $totalApplications > 0 ? round($totalApplications / 30, 2) : 0;

// Convertir les stats en JSON pour le JS
$dailyStatsJson = json_encode($dailyStats);
?>

<div class="min-h-screen bg-gray-50 py-6 px-4 sm:px-6 lg:px-8 flex justify-center">
    <div class="max-w-4xl w-full space-y-6">
        <!-- Titre centré -->
        <div class="text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Profil de l'étudiant : <?php echo htmlspecialchars($username); ?></h2>
        </div>

        <!-- Conteneur flex pour les deux blocs -->
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Liste des candidatures -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200 flex-1 md:mr-6">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Candidatures</h3>
                    <?php if (empty($applications)): ?>
                        <p class="text-sm text-gray-500 text-center py-4">Aucune candidature pour cet étudiant.</p>
                    <?php else: ?>
                        <div class="overflow-x-auto max-h-64 overflow-y-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0">
                                    <tr>
                                        <th scope="col" class="px-2 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entreprise</th>
                                        <th scope="col" class="px-2 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Poste</th>
                                        <th scope="col" class="px-2 sm:px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($applications as $application): ?>
                                    <tr>
                                        <td class="px-2 sm:px-4 py-3 text-sm font-medium text-gray-900 break-words"><?php echo htmlspecialchars($application['company_name']); ?></td>
                                        <td class="px-2 sm:px-4 py-3 text-sm text-gray-500 break-words"><?php echo htmlspecialchars($application['position']); ?></td>
                                        <td class="px-2 sm:px-4 py-3 text-sm text-gray-500 break-words"><?php echo htmlspecialchars($application['status']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Évolution des candidatures -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200 flex-1">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution des candidatures (30 derniers jours)</h3>
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Moyenne de candidatures par jour : <span class="font-medium"><?php echo $averagePerDay; ?></span></p>
                    </div>
                    <div>
                        <canvas id="applicationsChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const dailyStats = <?php echo $dailyStatsJson; ?>;
</script>
<script src="/utils/scripts/applicationsChart.js"></script>
</body>
</html>