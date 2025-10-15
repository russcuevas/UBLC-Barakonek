<?php
include '../database/connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$student_id = isset($_GET['student_id']) ? intval($_GET['student_id']) : 0;
if ($student_id <= 0) {
    die("Invalid student ID");
}

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$stmtStu = $conn->prepare("SELECT fullname, student_no FROM tbl_students WHERE id = :sid");
$stmtStu->execute(['sid' => $student_id]);
$student = $stmtStu->fetch(PDO::FETCH_ASSOC);
if (!$student) {
    die("Student not found");
}

$stmt = $conn->prepare("
    SELECT id, student_id, taken_at, depression_score, anxiety_score, stress_score,
           depression_level, anxiety_level, stress_level
    FROM tbl_results
    WHERE student_id = :sid AND YEAR(taken_at) = :yr
    ORDER BY taken_at ASC
");
$stmt->execute(['sid' => $student_id, 'yr' => $year]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtYears = $conn->prepare("
    SELECT DISTINCT YEAR(taken_at) AS yr
    FROM tbl_results
    WHERE student_id = :sid
    ORDER BY yr DESC
");
$stmtYears->execute(['sid' => $student_id]);
$yearsList = $stmtYears->fetchAll(PDO::FETCH_COLUMN);

$answersByTakenAt = [];
if ($results) {
    $stmtAns = $conn->prepare("
        SELECT a.taken_at, q.question, a.answer_value
        FROM tbl_answers a
        JOIN tbl_questions q ON q.id = a.question_id
        WHERE a.student_id = :sid
          AND DATE_FORMAT(a.taken_at, '%Y-%m-%d %H:%i:%s') = :taken_at
        ORDER BY q.id ASC
    ");

    foreach ($results as $result) {
        $stmtAns->execute([
            'sid' => $student_id,
            'taken_at' => $result['taken_at']
        ]);
        $answersByTakenAt[$result['taken_at']] = $stmtAns->fetchAll(PDO::FETCH_ASSOC);
    }
}

$answerLabels = [
    0 => '0-NEVER',
    1 => '1-SOMETIMES',
    2 => '2-OFTEN',
    3 => '3-ALMOST ALWAYS'
];
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BARAKONEK - Web</title>
    <link rel="shortcut icon" href="assets/images/ub-logo.png" type="image/png">
    <link rel="stylesheet" href="assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="./assets/compiled/css/table-datatable-jquery.css">
    <link rel="stylesheet" href="./assets/compiled/css/app.css">
    <link rel="stylesheet" href="./assets/compiled/css/app-dark.css">
</head>

<body>
    <script src="assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">

                        <!-- Left: Logo -->
                        <div class="logo d-flex align-items-center">
                            <a href="index.html">
                                <img src="assets/images/ub-logo.png" alt="Logo" style="height:40px;">
                            </a>
                        </div>
                        <!-- Middle: Title -->
                        <div class="flex-grow-1 text-center">
                            <span class="fw-bold fs-5" style="color: #752738">BARAKONEK</span>
                        </div>
                        <!-- Right: Theme Toggle -->
                        <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                            <div class="form-check form-switch fs-6">
                                <input class="form-check-input  me-0" type="checkbox" id="toggle-dark"
                                    style="cursor: pointer">
                                <label class="form-check-label"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sidebar-menu">
                    <ul class="menu">
                        <li class="sidebar-title">Modules</li>

                        <li class="sidebar-item ">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-house-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="appointments_management.php" class='sidebar-link'>
                                <i class="bi bi-telephone-fill"></i>
                                <span>Appointments</span>
                            </a>
                        </li>

                        <li class="sidebar-item">
                            <a href="admin_management.php" class='sidebar-link'>
                                <i class="bi bi-person-check-fill"></i>
                                <span>Admins</span>
                            </a>
                        </li>

                        <li class="sidebar-item has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-collection-fill"></i>
                                <span>Academic</span>
                            </a>

                            <ul class="submenu">

                                <li class="submenu-item">
                                    <a href="department_management.php" class="submenu-link">Department</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="course_management.php" class="submenu-link">Course</a>

                                </li>

                                <li class="submenu-item">
                                    <a href="student_management.php" class="submenu-link">Students</a>

                                </li>
                            </ul>
                        </li>


                        <li class="sidebar-item active has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-file-text-fill"></i>
                                <span>Assesstments</span>
                            </a>

                            <ul class="submenu active">

                                <li class="submenu-item  ">
                                    <a href="categories_management.php" class="submenu-link">Categories</a>

                                </li>

                                <li class="submenu-item  ">
                                    <a href="questions_management.php" class="submenu-link">Questions</a>

                                </li>

                                <li class="submenu-item active">
                                    <a href="result_management.php" class="submenu-link">Results</a>

                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="main" class='layout-navbar navbar-fixed'>
            <header>
                <nav class="navbar navbar-expand navbar-light navbar-top">
                    <div class="container-fluid">
                        <a href="#" class="burger-btn d-block d-xl-none">
                            <i class="bi bi-justify fs-3"></i>
                        </a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mb-lg-0"></ul>

                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-menu d-flex">
                                        <div class="user-name text-end me-3">
                                            <h6 class="mb-0 text-gray-600" style="color: #752738 !important;">
                                                <?= $_SESSION['fullname'] ?? 'Guest'; ?>
                                            </h6>
                                            <p class="mb-0 text-sm text-gray-600" style="color: #752738 !important;">
                                                Administrator
                                            </p>
                                        </div>
                                        <div class="user-img d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="<?= $_SESSION['profile_picture'] ?? 'assets/images/avatar.jpg'; ?>" alt="Profile Picture">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="min-width: 11rem;">
                                    <li>
                                        <a class="dropdown-item" href="#"><i class="icon-mid bi bi-person me-2"></i> My Profile</a>
                                    </li>
                                    <hr class="dropdown-divider">
                                    <li>
                                        <a class="dropdown-item" href="logout.php"><i class="icon-mid bi bi-box-arrow-left me-2"></i> Logout</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <div id="main-content">

                <div class="page-heading">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>Results — <?= htmlspecialchars($student['fullname']) ?> (<?= htmlspecialchars($student['student_no']) ?>)</h2>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="result_management.php">Results Management</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Results — <?= htmlspecialchars($student['fullname']) ?> (<?= htmlspecialchars($student['student_no']) ?>)</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="card">
                            <!-- <div class="card-header">
                                <button type="button" style="float: right;" class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                    Generate PDF
                                </button>
                            </div> -->

                            <div class="card-body">
                                <div class="table-responsive">

                                    <form method="get" action="view_results.php">
                                        <input type="hidden" name="student_id" value="<?= $student_id ?>">
                                        <label for="year-select">Filter by Year:</label>
                                        <select id="year-select" name="year" onchange="this.form.submit()">
                                            <?php
                                            $startYear = 2025;
                                            $endYear = 2030;
                                            for ($yr = $startYear; $yr <= $endYear; $yr++): ?>
                                                <option value="<?= $yr ?>" <?= $yr == $year ? 'selected' : '' ?>><?= $yr ?></option>
                                            <?php endfor; ?>

                                        </select>
                                    </form>
                                    <div class="col-12 col-md-12">
                                        <div class="card">
                                            <div class="card-body d-flex justify-content-center align-items-start gap-4 flex-wrap">
                                                <div id="chart-mental-health-reports" class="d-flex flex-column gap-4">
                                                </div>

                                                <div class="table-responsive" style="min-width: 300px;">
                                                    <table class="table table-bordered text-center">
                                                        <thead class="table-primary">
                                                            <tr>
                                                                <th>Condition</th>
                                                                <th>Depression</th>
                                                                <th>Anxiety</th>
                                                                <th>Stress</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>Normal</td>
                                                                <td>0-9</td>
                                                                <td>0-7</td>
                                                                <td>0-14</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Mild</td>
                                                                <td>10-13</td>
                                                                <td>8-9</td>
                                                                <td>15-18</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Moderate</td>
                                                                <td>14-20</td>
                                                                <td>10-14</td>
                                                                <td>19-25</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Severe</td>
                                                                <td>21-27</td>
                                                                <td>15-19</td>
                                                                <td>26-33</td>
                                                            </tr>
                                                            <tr>
                                                                <td>Extremely Severe</td>
                                                                <td>28+</td>
                                                                <td>20+</td>
                                                                <td>34+</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-bordered table-striped align-middle">


                                        <thead class="table-primary text-center">
                                            <tr>
                                                <th>Taken</th>
                                                <th>Depression</th>
                                                <th>Anxiety</th>
                                                <th>Stress</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($results) === 0): ?>
                                                <tr>
                                                    <td colspan="8" class="text-center fst-italic py-3">
                                                        No results for <?= htmlspecialchars($year) ?>
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($results as $row): ?>
                                                    <?php
                                                    $levelColors = [
                                                        'normal' => '#0a883eff',
                                                        'mild' => '#2ecc71',
                                                        'moderate' => '#f1c40f',
                                                        'severe' => '#e67e22',
                                                        'extremely severe' => '#e74c3c'
                                                    ];
                                                    ?>

                                                    <tr class="align-middle">
                                                        <td class="py-2"><?= date('M d - h:i:s A', strtotime($row['taken_at'])) ?></td>

                                                        <td class="py-2 text-center">
                                                            <span class="fw-semibold"><?= htmlspecialchars($row['depression_score']) ?></span><br>
                                                            <small class="fw-bold" style="color: <?= $levelColors[strtolower($row['depression_level'])] ?? 'inherit' ?>;">
                                                                <?= htmlspecialchars($row['depression_level']) ?>
                                                            </small>
                                                        </td>

                                                        <td class="py-2 text-center">
                                                            <span class="fw-semibold"><?= htmlspecialchars($row['anxiety_score']) ?></span><br>
                                                            <small class="fw-bold" style="color: <?= $levelColors[strtolower($row['anxiety_level'])] ?? 'inherit' ?>;">
                                                                <?= htmlspecialchars($row['anxiety_level']) ?>
                                                            </small>
                                                        </td>

                                                        <td class="py-2 text-center">
                                                            <span class="fw-semibold"><?= htmlspecialchars($row['stress_score']) ?></span><br>
                                                            <small class="fw-bold" style="color: <?= $levelColors[strtolower($row['stress_level'])] ?? 'inherit' ?>;">
                                                                <?= htmlspecialchars($row['stress_level']) ?>
                                                            </small>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="8" class="py-3">
                                                            <strong>Response:</strong>
                                                            <div class="table-responsive mt-2">
                                                                <table class="table table-sm table-bordered mb-0 nested-table" style="width:100%">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width: 5%;">#</th>
                                                                            <th>Question</th>
                                                                            <th style="width: 25%;">Answer</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        $answers = $answersByTakenAt[$row['taken_at']] ?? [];
                                                                        if (count($answers) === 0): ?>
                                                                            <tr>
                                                                                <td colspan="3" class="text-center fst-italic py-2">
                                                                                    No answers found.
                                                                                </td>
                                                                            </tr>
                                                                        <?php else: ?>
                                                                            <?php foreach ($answers as $index => $ans): ?>
                                                                                <tr>
                                                                                    <td class="text-center py-1"><?= $index + 1 ?></td>
                                                                                    <td class="py-1"><?= htmlspecialchars($ans['question']) ?></td>
                                                                                    <td class="text-center py-1"><?= htmlspecialchars($answerLabels[$ans['answer_value']] ?? 'Unknown') ?></td>
                                                                                </tr>
                                                                            <?php endforeach; ?>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>

                                    </table>

                                </div>
                            </div>
                        </div>
                    </section>
                    <!-- Basic Tables end -->
                </div>
            </div>
        </div>
    </div>

    <script src="assets/static/js/components/dark.js"></script>
    <script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/compiled/js/app.js"></script>
    <script src="assets/extensions/jquery/jquery.min.js"></script>
    <script src="assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/static/js/pages/datatables.js"></script>
    <script src="assets/extensions/parsleyjs/parsley.min.js"></script>
    <script src="assets/static/js/pages/parsley.js"></script>
    <script src="assets/extensions/apexcharts/apexcharts.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const studentId = <?= json_encode($student_id) ?>;
            const year = <?= json_encode($year) ?>;

            fetch(`analytics/results-chart.php?student_id=${studentId}&year=${year}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        document.querySelector("#chart-mental-health-reports").innerHTML = '<p class="text-danger">' + data.error + '</p>';
                        return;
                    }
                    const hasValidData = Array.isArray(data.series) && data.series.some(value => value > 0);

                    if (!hasValidData) {
                        return;
                    }

                    var options = {
                        series: data.series,
                        chart: {
                            width: 380,
                            type: 'pie'
                        },
                        labels: data.labels,
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 280
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }]
                    };

                    var chart = new ApexCharts(document.querySelector("#chart-mental-health-reports"), options);
                    chart.render();
                })
                .catch(error => {
                    console.error('Error fetching chart data:', error);
                    document.querySelector("#chart-mental-health-reports").innerHTML = '<p class="text-danger">Failed to load chart.</p>';
                });
        });
    </script>


</body>

</html>