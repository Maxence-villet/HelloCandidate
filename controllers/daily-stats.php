<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../database.php';

$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$days = isset($_GET['days']) ? (int)$_GET['days'] : 30;

$db = new Database();
$conn = $db->getConnection();

$query = "
    SELECT DATE(submission_date) as date, COUNT(*) as count
    FROM applications
    WHERE user_id = ? AND submission_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
    GROUP BY DATE(submission_date)
    ORDER BY submission_date ASC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $days);
$stmt->execute();
$result = $stmt->get_result();

$stats = [];
while ($row = $result->fetch_assoc()) {
    $stats[] = [
        'date' => $row['date'],
        'count' => (int)$row['count']
    ];
}

echo json_encode($stats);
$stmt->close();
$conn->close();