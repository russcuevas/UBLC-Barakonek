<?php
include 'database/connection.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$answers = $_POST['answers'] ?? [];

if (count($answers) < 42) {
    $_SESSION['error'] = "Please answer all 42 questions before submitting.";
    header("Location: examination_page.php");
    exit();
}

date_default_timezone_set('Asia/Manila');
$taken_at = date('Y-m-d H:i:s');


// Initialize category score storage
$scoreData = [
    'Depression' => 0,
    'Anxiety'    => 0,
    'Stress'     => 0
];

try {
    $conn->beginTransaction();

    // Fetch all questions with their category names
    $questionsStmt = $conn->query("
        SELECT q.id, c.category_name
        FROM tbl_questions q
        LEFT JOIN tbl_categories c ON q.category_id = c.id
    ");

    $questionCategories = [];
    foreach ($questionsStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $questionCategories[$row['id']] = $row['category_name'];
    }

    // Prepare insert into tbl_answers
    $insertAnswer = $conn->prepare("
        INSERT INTO tbl_answers (student_id, question_id, taken_at, answer_value)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($answers as $question_id => $value) {
        $question_id = (int) $question_id;
        $value = (int) $value;

        // Save answer
        $insertAnswer->execute([$student_id, $question_id, $taken_at, $value]);

        // Add to the corresponding category total
        $category = $questionCategories[$question_id] ?? null;
        if ($category && isset($scoreData[$category])) {
            $scoreData[$category] += $value;
        }
    }

    // Scoring level ranges
    function getLevel($score, $type) {
        $levels = [
            'Depression' => [
                'Normal' => [0, 9],
                'Mild' => [10, 13],
                'Moderate' => [14, 20],
                'Severe' => [21, 27],
                'Extremely Severe' => [28, PHP_INT_MAX]
            ],
            'Anxiety' => [
                'Normal' => [0, 7],
                'Mild' => [8, 9],
                'Moderate' => [10, 14],
                'Severe' => [15, 19],
                'Extremely Severe' => [20, PHP_INT_MAX]
            ],
            'Stress' => [
                'Normal' => [0, 14],
                'Mild' => [15, 18],
                'Moderate' => [19, 25],
                'Severe' => [26, 33],
                'Extremely Severe' => [34, PHP_INT_MAX]
            ]
        ];

        foreach ($levels[$type] as $level => [$min, $max]) {
            if ($score >= $min && $score <= $max) {
                return $level;
            }
        }

        return 'Unknown';
    }

    // Final scores
    $depression_score = $scoreData['Depression'];
    $anxiety_score    = $scoreData['Anxiety'];
    $stress_score     = $scoreData['Stress'];

    $depression_level = getLevel($depression_score, 'Depression');
    $anxiety_level    = getLevel($anxiety_score, 'Anxiety');
    $stress_level     = getLevel($stress_score, 'Stress');

    // Insert result into tbl_results
    $insertResult = $conn->prepare("
        INSERT INTO tbl_results (
            student_id, taken_at,
            depression_score, anxiety_score, stress_score,
            depression_level, anxiety_level, stress_level
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $insertResult->execute([
        $student_id,
        $taken_at,
        $depression_score,
        $anxiety_score,
        $stress_score,
        $depression_level,
        $anxiety_level,
        $stress_level
    ]);

    $conn->commit();

    $_SESSION['success'] = "Your exam was successfully submitted please check at inquiry to see the results";
    header("Location: dashboard.php");
    exit();

} catch (Exception $e) {
    $conn->rollBack();
    error_log("Error in submit_answers.php: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while submitting your answers. Please try again.";
    header("Location: examination_page.php");
    exit();
}
?>
