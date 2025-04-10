<?php
require_once __DIR__ . '/../../utils/database.php';

class DashboardController {
    private $db;
    private $conn;
    private $user_id;

    public function __construct() {
        // Start session to access user data
        session_start();

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit();
        }

        $this->user_id = $_SESSION['user_id'];
        $this->db = new Database();
        $this->conn = $this->db->getConnection();

        if (!$this->conn) {
            die("Database connection failed.");
        }
    }

    public function getDashboardData() {
        $data = [];

        // Username for display
        $data['username'] = htmlspecialchars($_SESSION['username']);

        // 1. Fetch Total Applications and Percentage Change
        $stmt = $this->conn->prepare("SELECT candidature_count FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $data['total_applications'] = $user['candidature_count'] ?? 0;
        $stmt->close();

        // Current month applications
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as current_month_count
            FROM applications
            WHERE user_id = ?
            AND YEAR(submission_date) = YEAR(CURRENT_DATE())
            AND MONTH(submission_date) = MONTH(CURRENT_DATE())
        ");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_month = $result->fetch_assoc()['current_month_count'] ?? 0;
        $stmt->close();

        // Previous month applications
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as previous_month_count
            FROM applications
            WHERE user_id = ?
            AND YEAR(submission_date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
            AND MONTH(submission_date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
        ");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $previous_month = $result->fetch_assoc()['previous_month_count'] ?? 0;
        $stmt->close();

        // Calculate percentage change
        $applications_change = ($previous_month > 0) ? (($current_month - $previous_month) / $previous_month * 100) : 0;
        $data['applications_change_text'] = $applications_change >= 0 ? "Augmentation de " . round($applications_change, 2) . "%" : "Diminution de " . round(abs($applications_change), 2) . "%";
        $data['applications_change_color'] = $applications_change >= 0 ? "text-green-600" : "text-red-600";

        // 2. Fetch Pending Applications and Percentage Change
        $stmt = $this->conn->prepare("SELECT COUNT(*) as pending_count FROM applications WHERE user_id = ? AND status = 'pending'");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data['pending_applications'] = $result->fetch_assoc()['pending_count'] ?? 0;
        $stmt->close();

        // Current month pending applications
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as current_pending_count
            FROM applications
            WHERE user_id = ?
            AND status = 'pending'
            AND YEAR(submission_date) = YEAR(CURRENT_DATE())
            AND MONTH(submission_date) = MONTH(CURRENT_DATE())
        ");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_pending = $result->fetch_assoc()['current_pending_count'] ?? 0;
        $stmt->close();

        // Previous month pending applications
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) as previous_pending_count
            FROM applications
            WHERE user_id = ?
            AND status = 'pending'
            AND YEAR(submission_date) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
            AND MONTH(submission_date) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)
        ");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $previous_pending = $result->fetch_assoc()['previous_pending_count'] ?? 0;
        $stmt->close();

        // Calculate percentage change
        $pending_change = ($previous_pending > 0) ? (($current_pending - $previous_pending) / $previous_pending * 100) : 0;
        $data['pending_change_text'] = $pending_change >= 0 ? "Augmentation de " . round($pending_change, 2) . "%" : "Diminution de " . round(abs($pending_change), 2) . "%";
        $data['pending_change_color'] = $pending_change >= 0 ? "text-green-600" : "text-red-600";

        // 3. Fetch Rank Progress
        $stmt = $this->conn->prepare("
            SELECT r.rank_name, r.sub_rank, r.min_applications
            FROM users u
            JOIN ranks r ON u.rank_id = r.rank_id
            WHERE u.user_id = ?
        ");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $current_rank = $result->fetch_assoc();
        $stmt->close();

        $stmt = $this->conn->prepare("
            SELECT rank_name, sub_rank, min_applications
            FROM ranks
            WHERE min_applications > ?
            ORDER BY min_applications ASC
            LIMIT 1
        ");
        $stmt->bind_param("i", $current_rank['min_applications']);
        $stmt->execute();
        $result = $stmt->get_result();
        $next_rank = $result->fetch_assoc();
        $stmt->close();

        // Calculate progress to next rank
        $applications_needed = $next_rank ? ($next_rank['min_applications'] - $data['total_applications']) : 0;
        $progress_percentage = $next_rank ? (($data['total_applications'] - $current_rank['min_applications']) / ($next_rank['min_applications'] - $current_rank['min_applications']) * 100) : 100;
        $data['current_rank'] = $current_rank;
        $data['progress_text'] = $next_rank ? round($progress_percentage, 2) . "% atteint pour le prochain rang ($applications_needed candidatures restantes)" : "Dernier rang atteint!";

        // 4. Fetch Application Status Over Time (for the bar chart)
        $stmt = $this->conn->prepare("
            SELECT 
                DATE_FORMAT(submission_date, '%b') as month,
                status,
                COUNT(*) as count
            FROM applications
            WHERE user_id = ?
            AND submission_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 5 MONTH)
            GROUP BY DATE_FORMAT(submission_date, '%b'), status
            ORDER BY submission_date
        ");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $months = [];
        $statuses = ['pending' => [], 'interview' => [], 'rejected' => [], 'accepted' => []];
        $last_5_months = [];
        for ($i = 4; $i >= 0; $i--) {
            $last_5_months[] = date('M', strtotime("-$i months"));
        }

        foreach ($last_5_months as $month) {
            $months[] = $month;
            $statuses['pending'][$month] = 0;
            $statuses['interview'][$month] = 0;
            $statuses['rejected'][$month] = 0;
            $statuses['accepted'][$month] = 0;
        }

        while ($row = $result->fetch_assoc()) {
            $month = $row['month'];
            $status = $row['status'];
            $count = $row['count'];
            if (in_array($month, $months)) {
                $statuses[$status][$month] = $count;
            }
        }
        $stmt->close();

        $data['months'] = $months;
        $data['statuses'] = $statuses;

        // 5. Fetch Application Sources (for the pie chart)
        $stmt = $this->conn->prepare("
            SELECT 
                CASE 
                    WHEN offer_link LIKE '%linkedin.com%' THEN 'LinkedIn'
                    WHEN offer_link LIKE '%indeed.com%' THEN 'Indeed'
                    ELSE 'Other'
                END as source,
                COUNT(*) as count
            FROM applications
            WHERE user_id = ?
            GROUP BY 
                CASE 
                    WHEN offer_link LIKE '%linkedin.com%' THEN 'LinkedIn'
                    WHEN offer_link LIKE '%indeed.com%' THEN 'Indeed'
                    ELSE 'Other'
                END
        ");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $sources = [];
        $source_counts = [];
        while ($row = $result->fetch_assoc()) {
            $sources[] = $row['source'];
            $source_counts[] = $row['count'];
        }
        $stmt->close();

        // If no sources are found, provide a default
        if (empty($sources)) {
            $sources = ['No Data'];
            $source_counts = [1];
        }

        $data['sources'] = $sources;
        $data['source_counts'] = $source_counts;

        return $data;
    }

    public function showDashboardPage() {
        // Fetch the data
        $data = $this->getDashboardData();

        // Extract data for the view
        $username = $data['username'];
        $total_applications = $data['total_applications'];
        $applications_change_text = $data['applications_change_text'];
        $applications_change_color = $data['applications_change_color'];
        $pending_applications = $data['pending_applications'];
        $pending_change_text = $data['pending_change_text'];
        $pending_change_color = $data['pending_change_color'];
        $current_rank = $data['current_rank'];
        $progress_text = $data['progress_text'];
        $months = $data['months'];
        $statuses = $data['statuses'];
        $sources = $data['sources'];
        $source_counts = $data['source_counts'];

        // Include the view
        require_once __DIR__ . '/../../views/student/dashboard.php';
    }

    public function __destruct() {
        // Ensure the database connection is closed
        if ($this->conn) {
            $this->conn->close();
        }
    }
}