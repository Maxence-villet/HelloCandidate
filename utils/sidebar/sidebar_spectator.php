<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if username is set in the session
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';

// Fetch the spectator's groups using the Database class
try {
    require_once __DIR__ . '/../../utils/database.php'; // Adjust path as needed
    $db = new Database();
    $conn = $db->getConnection();

    if ($conn === null) {
        throw new Exception("Failed to connect to the database.");
    }

    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

    if ($user_id === 0) {
        throw new Exception("User ID not found in session.");
    }

    // Prepare and execute the query using MySQLi
    $query = "SELECT group_id, group_name FROM groups WHERE created_by = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $groups = $result->fetch_all(MYSQLI_ASSOC);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    // Log the error (in a real application, you'd log this to a file or error tracking system)
    error_log("Error fetching groups: " . $e->getMessage());
    $groups = []; // Fallback to empty array to prevent breaking the UI
}
?>

<aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg p-4 transform md:transform-none transition-transform duration-300 z-20">
    <div class="flex items-center space-x-3 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-800"><?php echo $username; ?></h2>
            <p class="text-sm text-gray-500"><?php echo $_SESSION["user_type"]; ?></p>
        </div>
    </div>
    <nav>
        <ul class="space-y-2">
            <li>
                <a href="/spectator/dashboard" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded <?php echo ($current_page === 'spectator_dashboard') ? 'bg-blue-50' : ''; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Spectator</span>
                </a>
            </li>
            <li>
                <a href="/rankings" class="flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded <?php echo ($current_page === 'rankings') ? 'bg-blue-50' : ''; ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 11l7-7 7 7M5 19l7-7 7 7"></path>
                    </svg>
                    <span>Classement</span>
                </a>
            </li>
            <!-- Groups Category with Dropdown -->
            <li>
                <button id="groups-toggle" class="w-full flex items-center justify-between text-blue-600 p-2 hover:bg-gray-100 rounded focus:outline-none">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Groupes</span>
                    </div>
                    <svg id="groups-arrow" class="w-4 h-4 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <ul id="groups-list" class="pl-6 space-y-1 hidden overflow-hidden transition-all duration-300">
                    <?php if (empty($groups)): ?>
                        <li class="text-sm text-gray-500 p-2">Aucun groupe</li>
                    <?php else: ?>
                        <?php foreach ($groups as $group): ?>
                            <li>
                                <form action="/group/manage" method="POST" class="flex items-center">
                                    <input type="hidden" name="group_id" value="<?php echo htmlspecialchars($group['group_id']); ?>">
                                    <button type="submit" class="w-full text-left flex items-center space-x-2 text-blue-600 hover:bg-blue-50 p-2 rounded">
                                        <span class="text-sm"><?php echo htmlspecialchars($group['group_name']); ?></span>
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </li>
            <li>
                <a href="/logout" class="flex items-center space-x-2 text-red-600 hover:bg-red-50 p-2 rounded">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Déconnexion</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>

<!-- Include the JavaScript file -->
<script src="/utils/scripts/sidebar_spectator.js"></script>