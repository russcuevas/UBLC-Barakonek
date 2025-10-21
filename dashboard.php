<?php
// session with database connection
include 'database/connection.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// clean welcome sweetalert
$fullname = $_SESSION['fullname'] ?? 'Student';
$gender = $_SESSION['gender'] ?? 'male';
$show_welcome = false;
if (empty($_SESSION['welcome_shown'])) {
    $show_welcome = true;
    $_SESSION['welcome_shown'] = true;
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

if (isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $stmt = $conn->prepare("SELECT password FROM tbl_students WHERE id = ?");
    $stmt->execute([$student_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $old_password === $row['password']) {
        $updateStmt = $conn->prepare("UPDATE tbl_students SET password = ?, updated_at = NOW() WHERE id = ?");
        $updateStmt->execute([$new_password, $student_id]);

        $_SESSION['success'] = "Password updated successfully.";
    } else {
        $_SESSION['error'] = "Old password is incorrect.";
    }


    header('Location: dashboard.php');
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BARAKONEK - Web</title>
    <link rel="shortcut icon" href="assets/dashboard/images/ub-logo.png" type="image/png">
    <link rel="stylesheet" href="./assets/dashboard/compiled/css/app.css">
    <link rel="stylesheet" href="./assets/dashboard/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./assets/dashboard/compiled/css/iconly.css">
</head>

<body>
    <script src="assets/dashboard/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
                <div class="sidebar-header position-relative">
                    <div class="d-flex justify-content-between align-items-center">

                        <!-- Left: Logo -->
                        <div class="logo d-flex align-items-center">
                            <a href="dashboard.php">
                                <img src="assets/dashboard/images/ub-logo.png" alt="Logo" style="height:40px;">
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

                        <li class="sidebar-item active ">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-house-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-file-text-fill"></i>
                                <span>Inquiry</span>
                            </a>

                            <ul class="submenu ">

                                <li class="submenu-item  ">
                                    <a href="appointment.php" class="submenu-link">Appointment</a>
                                </li>

                                <li class="submenu-item  ">
                                    <a href="results.php" class="submenu-link">My Result</a>
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
                            <ul class="navbar-nav ms-auto mb-lg-0">


                            </ul>
                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-menu d-flex">
                                        <div class="user-name text-end me-3">
                                            <h6 class="mb-0 text-gray-600" style="color: #752738 !important;"><?= htmlspecialchars($_SESSION['fullname']) ?></h6>
                                            <p class="mb-0 text-sm text-gray-600" style="color: #752738 !important;">
                                                Student</p>
                                        </div>
                                        <div class="user-img d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="./assets/dashboard/compiled/jpg/1.jpg">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"
                                    style="min-width: 11rem;">
                                    <li><a class="dropdown-item" href="logout.php"><i
                                                class="icon-mid bi bi-box-arrow-left me-2"></i> Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <div id="main-content">

                <div class="page-heading">
                    <h3>Dashboard</h3>
                </div>
                <div class="page-content">
                    <section class="row">
                        <!-- Left Column: My Profile -->
                        <div class="col-12 col-lg-6">
                            <div class="card p-4">
                                <div class="row align-items-center">
                                    <!-- Profile Image -->
                                    <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                                        <img src="assets/images/avatar.jpg" alt="Profile Image" class="rounded-circle shadow" width="120" height="120">
                                    </div>

                                    <!-- Profile Details -->
                                    <div class="col-12 col-md-8">
                                        <div class="mb-3">
                                            <h5 style="color: #752738 !important;" class="fw-bold mb-1">
                                                <?= htmlspecialchars($_SESSION['fullname']) ?>
                                            </h5>
                                            <p style="color: #752738 !important;">
                                                Student No: <strong><?= htmlspecialchars($_SESSION['student_no']) ?></strong>
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($_SESSION['email']) ?></p>
                                            <p class="mb-1"><strong>Phone Number:</strong> <?= htmlspecialchars($_SESSION['phone_number']) ?></p>
                                            <p class="mb-1"><strong>Gender:</strong> <?= htmlspecialchars($_SESSION['gender']) ?></p>
                                            <p class="mb-1"><strong>Department:</strong> <?= htmlspecialchars($department_name) ?></p>
                                            <p class="mb-1"><strong>Year-Course:</strong> <?= htmlspecialchars($_SESSION['year_level']) ?> - <?= htmlspecialchars($course_name) ?></p>
                                            <?php
                                            $createdAtRaw = $_SESSION['created_at'] ?? null;
                                            $createdAtFormatted = $createdAtRaw
                                                ? (new DateTime($createdAtRaw))->format('F j Y - g:ia')
                                                : 'N/A';
                                            ?>
                                            <p class="mb-1"><strong>Account Created:</strong> <?= $createdAtFormatted ?></p>
                                        </div>
                                        <div class="text-end">
                                            <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                                <i class="bi bi-shield-lock-fill"></i> Change Password
                                            </button>
                                        </div>

                                    </div>

                                </div>
                            </div>


                        </div>

                        <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="form" method="POST" action="" data-parsley-validate>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="form-group mandatory mb-3">
            <label for="old_password" class="form-label">Old Password</label>
            <input
              type="password"
              class="form-control"
              name="old_password"
              id="old_password"
              required
              data-parsley-required-message="Old password is required."
              data-parsley-minlength="6"
              data-parsley-minlength-message="Old password must be at least 6 characters."
            >
          </div>

          <div class="form-group mandatory mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input
              type="password"
              class="form-control"
              name="new_password"
              id="new_password"
              required
              data-parsley-required-message="New password is required."
              data-parsley-minlength="6"
              data-parsley-minlength-message="New password must be at least 6 characters."
            >
          </div>
        </div>

        <div class="modal-footer">
          <div class="row w-100">
            <div class="col-12 d-flex justify-content-end">
              <button type="submit" name="change_password" class="btn btn-success me-2">Update Password</button>
              <button type="reset" data-bs-dismiss="modal" class="btn btn-secondary">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>




                        <!-- Right Column: Placeholder -->
                        <div class="col-12 col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h4>DASS-42 Exam</h4>
                                </div>
                                <div class="card-body">
                                    <p>The Depression Anxiety Stress Scales – 42 (DASS-42) is a 42-item self-report scale designed to measure the negative emotional states of depression, anxiety and stress in adults and older adolescents (17 years +). It is the long version of the DASS-21. It is a useful tool for routine outcome monitoring and can be used to assess the level of treatment response. </p>
                                    <div class="text-end">
                                        <button id="takeExamBtn" class="btn btn-primary mt-2">
                                            <i class="bi bi-file-text-fill"></i> Take exam
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>

    <script src="assets/dashboard/static/js/components/dark.js"></script>
    <script src="assets/dashboard/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>


    <script src="assets/dashboard/compiled/js/app.js"></script>



    <!-- Need: Apexcharts -->
    <script src="assets/dashboard/extensions/jquery/jquery.min.js"></script>

    <script src="assets/dashboard/extensions/apexcharts/apexcharts.min.js"></script>
    <script src="assets/dashboard/static/js/pages/dashboard.js"></script>
    <script src="assets/dashboard/extensions/parsleyjs/parsley.min.js"></script>
    <script src="assets/dashboard/static/js/pages/parsley.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('takeExamBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to take this exam?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'examination_page.php';
                }
            });
        });
    </script>

    <?php if ($show_welcome): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "Welcome <?= htmlspecialchars("$fullname") ?>",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    <?php endif; ?>

    <?php if (isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: '<?= isset($_SESSION['success']) ? 'success' : 'error' ?>',
                    title: '<?= isset($_SESSION['success']) ? addslashes($_SESSION['success']) : addslashes($_SESSION['error']) ?>',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
        <?php unset($_SESSION['success'], $_SESSION['error']); ?>
    <?php endif; ?>

</body>

</html>