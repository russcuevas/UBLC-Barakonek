<?php
// session with database connection
include '../../database/connection.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// clean welcome sweetalert
$fullname = $_SESSION['fullname'] ?? 'Counselor';
$gender = $_SESSION['gender'] ?? 'male';
$prefix = strtolower($gender) === 'female' ? 'Ms,' : 'Mr,';
// unset welcome sweetalert
$show_welcome = false;
if (empty($_SESSION['welcome_shown'])) {
    $show_welcome = true;
    $_SESSION['welcome_shown'] = true;
}

$categories = $conn->query("SELECT * FROM tbl_categories")->fetchAll(PDO::FETCH_ASSOC);
$sql = "SELECT q.*, c.category_name 
        FROM tbl_questions q 
        LEFT JOIN tbl_categories c ON q.category_id = c.id";
$questions = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BARAKONEK - Web</title>
    <link rel="shortcut icon" href="../assets/images/ub-logo.png" type="image/png">
    <link rel="stylesheet" href="../assets/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/compiled/css/table-datatable-jquery.css">
    <link rel="stylesheet" href="../assets/compiled/css/app.css">
    <link rel="stylesheet" href="../assets/compiled/css/app-dark.css">
    <style>
        .card-body table {
            font-size: 12px !important;
        }
    </style>

</head>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">

        <div id="main-content">
            <div class="page-heading">

                <section class="section">
                    <div class="row">



                        <!-- Right column: Exam Layout or Preview -->
                        <div class="col-md-12">
                            <div class="card">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="logo d-flex align-items-center">
                                        <a href="index.html" class="d-flex align-items-center text-decoration-none text-dark">
                                            <img src="../assets/images/ub-logo.png" alt="Logo" style="height:40px;" class="me-2">
                                        </a>
                                    </div>
                                    <h5 class="card-title mb-0">Depression Anxiety Stress Scales (DASS-42)</h5>
                                </div>

                                    <!-- Instructions below title -->
                                    <div style="font-size: 0.9rem; margin-bottom: 1rem;">
                                        <strong>Instructions:</strong><br>
                                        Please read each statement and press a response that indicates how much the statement applied to you over the past week. There are no right or wrong answers. Do not spend too much time on any statement.<br><br>
                                        <em>0 - NEVER</em> - Did not apply to me at all<br>
                                        <em>1 - SOMETIMES</em> - Applied to me to some degree, or some of the time<br>
                                        <em>2 - OFTEN</em> - Applied to me to a considerable degree, or a good part of time<br>
                                        <em>3 - ALMOST ALWAYS</em> - Applied to me very much, or most of the time
                                    </div>

                                    <!-- Name and Date on the same line -->
                                    <div class="d-flex justify-content-between mb-2">
                                        <div><em>Name</em>: _______________________</div>
                                        <div><em>Date</em>: _______________________</div>
                                    </div>

                                    <!-- Year-Course below -->
                                    <div><em>Year-Course</em>: _______________________</div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered" style="font-size: 0.9rem;">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">#</th>
                                                <th style="width: 60%;">QUESTIONS</th>
                                                <th colspan="4" style="text-align: center;">SCALE</th>
                                            </tr>
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th style="width: 5%; text-align: center;">0</th>
                                                <th style="width: 5%; text-align: center;">1</th>
                                                <th style="width: 5%; text-align: center;">2</th>
                                                <th style="width: 5%; text-align: center;">3</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($questions as $index => $q): ?>
                                                <tr>
                                                    <td><?= $index + 1 ?></td>
                                                    <td><?= htmlspecialchars($q['question']) ?></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div>
                </section>
            </div>
        </div>

        <script src="../assets/static/js/components/dark.js"></script>
        <script src="../assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
        <script src="../assets/compiled/js/app.js"></script>
        <script src="../assets/extensions/jquery/jquery.min.js"></script>
        <script src="../assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="../assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
        <script src="../assets/static/js/pages/datatables.js"></script>
        <script src="../assets/extensions/parsleyjs/parsley.min.js"></script>
        <script src="../assets/static/js/pages/parsley.js"></script>
        <script>
            window.addEventListener('load', function() {
                window.print();
            });

            window.onafterprint = function() {
                window.close();
            };
        </script>


</body>

</html>