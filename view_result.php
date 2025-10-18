<?php
include 'database/connection.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$result_id = isset($_GET['result_id']) ? intval($_GET['result_id']) : 0;
if ($result_id <= 0) {
    die("Invalid result ID");
}

// Fetch result details for this student and this result ID
$stmt = $conn->prepare("
    SELECT taken_at, depression_score, anxiety_score, stress_score,
           depression_level, anxiety_level, stress_level
    FROM tbl_results
    WHERE id = :rid AND student_id = :sid
");
$stmt->execute(['rid' => $result_id, 'sid' => $student_id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    die("Result not found.");
}

// Fetch answers for this result (matching student_id and taken_at)
$stmtAns = $conn->prepare("
    SELECT q.question, a.answer_value
    FROM tbl_answers a
    JOIN tbl_questions q ON q.id = a.question_id
    WHERE a.student_id = :sid AND a.taken_at = :taken_at
    ORDER BY q.id ASC
");
$stmtAns->execute(['sid' => $student_id, 'taken_at' => $result['taken_at']]);
$answers = $stmtAns->fetchAll(PDO::FETCH_ASSOC);

$answerLabels = [
    0 => '0-NEVER',
    1 => '1-SOMETIMES',
    2 => '2-OFTEN',
    3 => '3-ALMOST ALWAYS'
];

$levelColors = [
    'normal' => '#0a883eff',
    'mild' => '#2ecc71',
    'moderate' => '#f1c40f',
    'severe' => '#e67e22',
    'extremely severe' => '#e74c3c'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>View Result - <?= htmlspecialchars($_SESSION['fullname']) ?></title>
    <link rel="stylesheet" href="assets/dashboard/compiled/css/app.css" />
</head>
<body>
<div class="container mt-4">
    <h2>Result Details</h2>
    <p><strong>Taken at:</strong> <?= htmlspecialchars((new DateTime($result['taken_at']))->format('F j, Y - g:ia')) ?></p>

    <table class="table table-bordered text-center" style="max-width: 600px;">
        <thead class="table-primary">
            <tr>
                <th>Condition</th>
                <th>Score</th>
                <th>Level</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Depression</td>
                <td><?= htmlspecialchars($result['depression_score']) ?></td>
                <td style="color: <?= $levelColors[strtolower($result['depression_level'])] ?? 'inherit' ?>;">
                    <?= htmlspecialchars($result['depression_level']) ?>
                </td>
            </tr>
            <tr>
                <td>Anxiety</td>
                <td><?= htmlspecialchars($result['anxiety_score']) ?></td>
                <td style="color: <?= $levelColors[strtolower($result['anxiety_level'])] ?? 'inherit' ?>;">
                    <?= htmlspecialchars($result['anxiety_level']) ?>
                </td>
            </tr>
            <tr>
                <td>Stress</td>
                <td><?= htmlspecialchars($result['stress_score']) ?></td>
                <td style="color: <?= $levelColors[strtolower($result['stress_level'])] ?? 'inherit' ?>;">
                    <?= htmlspecialchars($result['stress_level']) ?>
                </td>
            </tr>
        </tbody>
    </table>

    <h4>Responses</h4>
    <?php if (count($answers) === 0): ?>
        <p>No answers found for this result.</p>
    <?php else: ?>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Question</th>
                    <th>Answer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($answers as $index => $ans): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($ans['question']) ?></td>
                        <td><?= htmlspecialchars($answerLabels[$ans['answer_value']] ?? 'Unknown') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="results.php" class="btn btn-secondary mt-3">Back to My Results</a>
</div>
</body>
</html>
