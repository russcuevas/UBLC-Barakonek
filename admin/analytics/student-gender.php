<?php
header('Content-Type: application/json');
include_once '../../database/connection.php';

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

try {
    $sql = "SELECT gender, COUNT(*) as total FROM tbl_students GROUP BY gender";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $series = [];

    foreach ($results as $row) {
        $gender = ucfirst(strtolower($row['gender']));
        $labels[] = $gender;
        $series[] = (int)$row['total'];
    }

    echo json_encode([
        'labels' => $labels,
        'series' => $series
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
