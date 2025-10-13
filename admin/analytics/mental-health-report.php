<?php
header('Content-Type: application/json');
include_once '../../database/connection.php';

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

try {
    $year = $_GET['year'] ?? date('Y');

    $months = range(1, 12);
    $monthLabels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    $severityLevels = ['Normal', 'Mild', 'Moderate', 'Severe', 'Extremely Severe'];
    $categories = ['Anxiety' => 'anxiety_level', 'Stress' => 'stress_level', 'Depression' => 'depression_level'];

    $resultData = [];

    foreach ($categories as $catName => $dbField) {
    $dataStructure = [];
    $totalCounts = array_fill_keys($severityLevels, 0); // New: total counts per level

    foreach ($severityLevels as $level) {
        $dataStructure[$level] = array_fill(0, 12, 0);
    }

    foreach ($months as $m) {
        $sql = "
            SELECT {$dbField} AS severity
            FROM tbl_results
            WHERE YEAR(taken_at) = :year AND MONTH(taken_at) = :month
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute(['year' => $year, 'month' => $m]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $level = $row['severity'];
            if (isset($dataStructure[$level])) {
                $dataStructure[$level][$m - 1]++;
                $totalCounts[$level]++; // Track total count
            }
        }
    }

    $resultData[$catName] = [
        'categories' => $monthLabels,
        'series' => [],
        'counts' => $totalCounts // ✅ Add this
    ];

    foreach ($dataStructure as $level => $data) {
        $resultData[$catName]['series'][] = [
            'name' => $level,
            'data' => $data
        ];
    }
}


    echo json_encode($resultData);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
