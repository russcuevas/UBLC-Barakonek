<?php
header('Content-Type: application/json');
include '../../database/connection.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

if ($student_id <= 0) {
    echo json_encode(['error' => 'Invalid student ID']);
    exit();
}

// Query to get average scores
$stmt = $conn->prepare("
    SELECT
        AVG(depression_score) AS avg_depression,
        AVG(anxiety_score) AS avg_anxiety,
        AVG(stress_score) AS avg_stress
    FROM tbl_results
    WHERE student_id = :sid AND YEAR(taken_at) = :yr
");
$stmt->execute(['sid' => $student_id, 'yr' => $year]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo json_encode(['error' => 'No data found']);
    exit();
}

echo json_encode([
    'labels' => ['Depression', 'Anxiety', 'Stress'],
    'series' => [
        round($data['avg_depression'], 2),
        round($data['avg_anxiety'], 2),
        round($data['avg_stress'], 2)
    ]
]);
