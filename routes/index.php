<?php
// Fetch ranks for the rank progression preview (similar to the Help page)
require_once __DIR__ . '/../utils/database.php';

$db = new Database();
$conn = $db->getConnection();

// Fetch all ranks (without sub-ranks, just the main rank names)
$stmt = $conn->prepare("
    SELECT DISTINCT rank_name
    FROM ranks
    ORDER BY min_applications ASC
");
$stmt->execute();
$result = $stmt->get_result();
$ranks = [];
while ($row = $result->fetch_assoc()) {
    $ranks[] = $row['rank_name'];
}
$stmt->close();

// Include the view
include __DIR__ . '/../views/index.php';