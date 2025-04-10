<?php
$page_title = 'Dashboard';
$current_page = 'dashboard'; // For sidebar navigation highlighting
include __DIR__ . '/../../utils/header/header_student.php';
?>

<!-- Main Content -->
<main class="flex-1 p-4 sm:p-6 md:p-8">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6 flex items-center">
        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
        </svg>
        Dashboard
    </h2>

    <!-- Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
        <!-- Total Applications -->
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800">Total des Candidatures</h3>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2"><?php echo $total_applications; ?></p>
            <p class="text-sm <?php echo $applications_change_color; ?> mt-2"><?php echo $applications_change_text; ?></p>
        </div>
        <!-- Pending Applications -->
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800">Applications en Attentes</h3>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2"><?php echo $pending_applications; ?></p>
            <p class="text-sm <?php echo $pending_change_color; ?> mt-2"><?php echo $pending_change_text; ?></p>
        </div>
        <!-- Rank Progress -->
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800">Progression du rang</h3>
            <p class="text-lg sm:text-xl font-bold text-gray-900 mt-2"><?php echo htmlspecialchars($current_rank['rank_name'] . ' ' . $current_rank['sub_rank']); ?></p>
            <p class="text-sm text-blue-600 mt-2"><?php echo $progress_text; ?></p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Application Status Over Time -->
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Statut des candidatures au fil du temps</h3>
            <div class="w-full h-64 sm:h-80">
                <canvas id="applicationStatusChart"></canvas>
            </div>
        </div>
        <!-- Application Sources (Renamed to Nombre de Candidatures) -->
        <div class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Nombre de Candidatures</h3>
            <div class="w-full h-64 sm:h-80 flex justify-center">
                <canvas id="applicationSourcesChart" class="max-w-full max-h-full"></canvas>
            </div>
        </div>
    </div>
</main>

<!-- Pass PHP data to JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/utils/scripts/dashboardCharts.js"></script>
<script>
    window.dashboardData = {
        months: <?php echo json_encode($months); ?>,
        statuses: {
            pending: <?php echo json_encode(array_values($statuses['pending'])); ?>,
            interview: <?php echo json_encode(array_values($statuses['interview'])); ?>,
            rejected: <?php echo json_encode(array_values($statuses['rejected'])); ?>,
            accepted: <?php echo json_encode(array_values($statuses['accepted'])); ?>
        },
        sources: <?php echo json_encode($sources); ?>,
        source_counts: <?php echo json_encode($source_counts); ?>
    };
</script>
</div>
</body>
</html>