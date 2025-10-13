<?php
header('Content-Type: application/json');
include_once '../../database/connection.php';

session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

try {
    $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

    $months = range(1, 12);
    $maleData = array_fill(1, 12, 0);
    $femaleData = array_fill(1, 12, 0);

    $sql = "
        SELECT 
            MONTH(r.taken_at) AS month,
            s.gender,
            COUNT(DISTINCT r.student_id) AS total
        FROM tbl_results r
        JOIN tbl_students s ON r.student_id = s.id
        WHERE 
            (r.depression_level IN ('Severe', 'Extremely Severe') 
            OR r.anxiety_level IN ('Severe', 'Extremely Severe') 
            OR r.stress_level IN ('Severe', 'Extremely Severe'))
            AND YEAR(r.taken_at) = :year
        GROUP BY month, s.gender
        ORDER BY month
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['year' => $year]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $month = (int)$row['month'];
        $gender = strtolower($row['gender']);
        $count = (int)$row['total'];

        if ($gender === 'male') {
            $maleData[$month] = $count;
        } elseif ($gender === 'female') {
            $femaleData[$month] = $count;
        }
    }

    $categories = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                   "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    $totalMale = array_sum($maleData);
    $totalFemale = array_sum($femaleData);

    echo json_encode([
        'categories' => $categories,
        'series' => [
            [
                'name' => "Male ({$totalMale})",
                'data' => array_values($maleData)
            ],
            [
                'name' => "Female ({$totalFemale})",
                'data' => array_values($femaleData)
            ]
        ]
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
