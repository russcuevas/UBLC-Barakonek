<?php
// session with database connection
include 'database/connection.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$stmt = $conn->prepare("
    SELECT s.*, d.department_name, c.course_name
    FROM tbl_students s
    LEFT JOIN tbl_department d ON s.department_id = d.id
    LEFT JOIN tbl_course c ON s.course_id = c.id
    WHERE s.id = ?
");
$stmt->execute([$student_id]);
$studentData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($studentData) {
    $department_name = $studentData['department_name'];
    $course_name     = $studentData['course_name'];
} else {
    $department_name = 'N/A';
    $course_name     = 'N/A';
}

$categories = $conn->query("SELECT * FROM tbl_categories")->fetchAll(PDO::FETCH_ASSOC);
$sql = "SELECT q.*, c.category_name 
        FROM tbl_questions q 
        LEFT JOIN tbl_categories c ON q.category_id = c.id";
$questions = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$currentYear = date('Y');

$stmtCheck = $conn->prepare("
    SELECT COUNT(*) 
    FROM tbl_results 
    WHERE student_id = ? AND YEAR(taken_at) = ?
");
$stmtCheck->execute([$student_id, $currentYear]);
$examCountThisYear = $stmtCheck->fetchColumn();

if ($examCountThisYear > 0) {
    $_SESSION['error'] = "You have already taken the exam for the year $currentYear. You can take it again next year.";
    header("Location: dashboard.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>BARAKONEK - Web</title>
        <link rel="shortcut icon" href="assets/dashboard/images/ub-logo.png" type="image/png">
        <link rel="stylesheet" href="assets/dashboard/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="assets/dashboard/compiled/css/table-datatable-jquery.css">
        <link rel="stylesheet" href="assets/dashboard/compiled/css/app.css">
        <link rel="stylesheet" href="assets/dashboard/compiled/css/app-dark.css">

        <!-- Bootstrap CSS CDN -->

        <!-- Custom Styles -->
        <style>
            body {
                background-color: #f8f9fa;
            }

            .card-header {
                background-color: #752738;
                color: #fff;
                font-weight: 600;
                font-size: 1.3rem;
                letter-spacing: 0.05em;
            }

            .instructions {
                font-size: 0.95rem;
                line-height: 1.4;
            }

            .table thead th {
                vertical-align: middle;
            }

            .table-hover tbody tr:hover {
                background-color: #f1e6eb;
                cursor: pointer;
            }

            .form-check-input {
                width: 1.3em;
                height: 1.3em;
                cursor: pointer;
            }

            .question-number {
                font-weight: 600;
                color: #752738;
            }

            .info-line em {
                font-style: normal;
                font-weight: 500;
                color: #555;
            }
        </style>
    </head>

<body>
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-header d-flex align-items-center gap-3">
                <img src="assets/dashboard/images/ub-logo.png" alt="UB Logo" style="height: 45px;" />
                <span>Depression Anxiety Stress Scales (DASS-42)</span>
            </div>
            <div class="card-body">
                <div class="mb-4 instructions p-4">
                    <strong>Instructions:</strong><br />
                    Please read each statement and select a response indicating how much the statement applied to you over the past week. <br> There are no right or wrong answers. Do not spend too much time on any statement.<br /><br />
                    <ul class="mb-0">
                        <li><strong>0 - NEVER</strong> - Did not apply to me at all</li>
                        <li><strong>1 - SOMETIMES</strong> - Applied to me to some degree, or some of the time</li>
                        <li><strong>2 - OFTEN</strong> - Applied to me to a considerable degree, or a good part of time</li>
                        <li><strong>3 - ALMOST ALWAYS</strong> - Applied to me very much, or most of the time</li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Student #:</strong> <?= htmlspecialchars($_SESSION['student_no']) ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-1"><strong>Date:</strong> <?= date('F j, Y') ?></p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($_SESSION['fullname']) ?></p>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-12">
                        <p class="mb-0"><strong>Year-Course:</strong> <?= htmlspecialchars($_SESSION['year_level']) ?> - <?= htmlspecialchars($course_name) ?></p>
                    </div>
                </div>


                <form action="submit_answers.php" method="POST" class="needs-validation" novalidate>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" style="font-size: 0.95rem;">
                            <thead style="background-color: #752738;">
                                <tr>
                                    <th style="width: 5%; color: white !important" class="text-center">#</th>
                                    <th style="width: 60%; color: white !important">QUESTIONS</th>
                                    <th colspan="4" class="text-center" style="color: white !important">SCALE</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th style="width: 5%; color: white !important" class="text-center">0</th>
                                    <th style="width: 5%; color: white !important" class="text-center">1</th>
                                    <th style="width: 5%; color: white !important" class="text-center">2</th>
                                    <th style="width: 5%; color: white !important" class="text-center">3</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($questions as $index => $q): ?>
                                    <tr>
                                        <td class="text-center question-number"><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($q['question']) ?></td>
                                        <?php for ($score = 0; $score <= 3; $score++): ?>
                                            <td class="text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                        name="answers[<?= $q['id'] ?>]"
                                                        id="q<?= $q['id'] ?>_score<?= $score ?>"
                                                        value="<?= $score ?>" required>
                                                    <label class="form-check-label" for="q<?= $q['id'] ?>_score<?= $score ?>"></label>
                                                </div>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                            Submit Answers
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/dashboard/static/js/components/dark.js"></script>
    <script src="assets/dashboard/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/dashboard/compiled/js/app.js"></script>
    <script src="assets/dashboard/extensions/jquery/jquery.min.js"></script>
    <script src="assets/dashboard/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/dashboard/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/dashboard/static/js/pages/datatables.js"></script>
    <script src="assets/dashboard/extensions/parsleyjs/parsley.min.js"></script>
    <script src="assets/dashboard/static/js/pages/parsley.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- SWEETALERT SUCCESS -->
    <script>
        const form = document.querySelector('.needs-validation');
        const submitBtn = form.querySelector('button[type="submit"]');

        submitBtn.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent default form submit

            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "Are you sure you want to submit your answers? You won't be able to change them later.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
    <script>
        (function() {
            'use strict'
            const forms = document.querySelectorAll('.needs-validation')
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>

</html>