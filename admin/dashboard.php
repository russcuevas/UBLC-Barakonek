<?php
// session with database connection
include '../database/connection.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
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

// recent accounts
$sql = "SELECT fullname, email, created_at FROM tbl_students WHERE created_at >= DATE_SUB(NOW(), INTERVAL 3 DAY) ORDER BY created_at DESC LIMIT 5";
$stmt = $conn->prepare($sql);
$stmt->execute();
$recent_students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// get total count of admins
$sql_admins = "SELECT COUNT(*) AS total_admins FROM tbl_admin";
$stmt_admins = $conn->prepare($sql_admins);
$stmt_admins->execute();
$total_admins = $stmt_admins->fetch(PDO::FETCH_ASSOC)['total_admins'];

// get total count of students
$sql_students = "SELECT COUNT(*) AS total_students FROM tbl_students";
$stmt_students = $conn->prepare($sql_students);
$stmt_students->execute();
$total_students = $stmt_students->fetch(PDO::FETCH_ASSOC)['total_students'];

// get total count of departments
$sql_departments = "SELECT COUNT(*) AS total_departments FROM tbl_department";
$stmt_departments = $conn->prepare($sql_departments);
$stmt_departments->execute();
$total_departments = $stmt_departments->fetch(PDO::FETCH_ASSOC)['total_departments'];

// get total count of courses
$sql_courses = "SELECT COUNT(*) AS total_courses FROM tbl_course";
$stmt_courses = $conn->prepare($sql_courses);
$stmt_courses->execute();
$total_courses = $stmt_courses->fetch(PDO::FETCH_ASSOC)['total_courses'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BARAKONEK - Web</title>
    <link rel="shortcut icon" href="assets/images/ub-logo.png" type="image/png">
    <link rel="stylesheet" href="./assets/compiled/css/app.css">
    <link rel="stylesheet" href="./assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./assets/compiled/css/iconly.css">
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
                            <a href="dashboard.php">
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

                        <li class="sidebar-item active ">
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

                        <li class="sidebar-item ">
                            <a href="admin_management.php" class='sidebar-link'>
                                <i class="bi bi-person-check-fill"></i>
                                <span>Admins</span>
                            </a>
                        </li>

                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-collection-fill"></i>
                                <span>Academic</span>
                            </a>

                            <ul class="submenu ">

                                <li class="submenu-item  ">
                                    <a href="department_management.php" class="submenu-link">Department</a>

                                </li>

                                <li class="submenu-item  ">
                                    <a href="course_management.php" class="submenu-link">Course</a>

                                </li>

                                <li class="submenu-item  ">
                                    <a href="student_management.php" class="submenu-link">Students</a>

                                </li>
                            </ul>
                        </li>


                        <li class="sidebar-item  has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-file-text-fill"></i>
                                <span>Assesstments</span>
                            </a>

                            <ul class="submenu ">

                                <li class="submenu-item  ">
                                    <a href="categories_management.php" class="submenu-link">Categories</a>

                                </li>

                                <li class="submenu-item  ">
                                    <a href="questions_management.php" class="submenu-link">Questions</a>

                                </li>

                                <li class="submenu-item  ">
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
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                                            <i class="icon-mid bi bi-person me-2"></i> My Profile
                                        </a>
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

            <!-- Profile Modal -->
            <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="profileModalLabel">My Profile</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <?php if (isset($_SESSION['admin'])): ?>
                                    <div class="text-center mb-3">
                                        <img id="preview" src="<?php echo htmlspecialchars($_SESSION['profile_picture'] ?? 'default.png'); ?>"
                                            alt="Profile Picture" class="rounded-circle" width="120" height="120">
                                    </div>

                                    <!-- View Mode -->
                                    <div id="viewProfile">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Full Name</th>
                                                <td><?php echo htmlspecialchars($_SESSION['fullname']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Email</th>
                                                <td><?php echo htmlspecialchars($_SESSION['email']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Phone</th>
                                                <td><?php echo htmlspecialchars($_SESSION['phone_number']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Gender</th>
                                                <td><?php echo htmlspecialchars($_SESSION['gender']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Created At</th>
                                                <td><?php echo htmlspecialchars($_SESSION['created_at']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Last Updated</th>
                                                <td><?php echo htmlspecialchars($_SESSION['updated_at']); ?></td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Edit Mode -->
                                    <div id="editProfile" style="display: none;">
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label>Profile Picture</label>
                                                <input type="file" name="profile_picture" class="form-control" onchange="previewImage(event)">
                                            </div>
                                            <div class="col-md-6">
                                                <label>Full Name</label>
                                                <input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($_SESSION['fullname']); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label>New Password</label>
                                                <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                                            </div>

                                            <div class="col-md-6">
                                                <label>Phone Number</label>
                                                <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($_SESSION['phone_number']); ?>">
                                            </div>
                                            <div class="col-md-6">
                                                <label>Gender</label>
                                                <select name="gender" class="form-control" required>
                                                    <option value="Male" <?php if ($_SESSION['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                                    <option value="Female" <?php if ($_SESSION['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                <?php endif; ?>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                                <!-- Toggle buttons -->
                                <button type="button" class="btn btn-primary" id="editBtn" onclick="toggleEdit(true)">Edit</button>
                                <button type="submit" class="btn btn-success" id="saveBtn" style="display: none;">Save Changes</button>
                                <button type="button" class="btn btn-secondary" id="cancelBtn" style="display: none;" onclick="toggleEdit(false)">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



            <div id="main-content">

                <div class="page-heading">
                    <h3>Dashboard</h3>
                </div>
                <div class="page-content">
                    <section class="row">
                        <div class="col-12 col-lg-9">
                            <div class="row">
                                <div class="col-6 col-lg-3 col-md-6">
                                    <div class="card">
                                        <div class="card-body px-4 py-4-5">
                                            <div class="row">
                                                <div
                                                    class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                    <div class="stats-icon mb-2" style="background-color: #752738;">
                                                        <i style="color: white;" class="iconly-boldLock"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                    <h6 class="text-muted font-semibold">Admin</h6>
                                                    <h6 class="font-extrabold mb-0"><?= $total_admins ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3 col-md-6">
                                    <div class="card">
                                        <div class="card-body px-4 py-4-5">
                                            <div class="row">
                                                <div
                                                    class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                    <div class="stats-icon mb-2" style="background-color: #752738;">
                                                        <i style="color: white;" class="iconly-boldUser"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                    <h6 class="text-muted font-semibold">Students</h6>
                                                    <h6 class="font-extrabold mb-0"><?= $total_students ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3 col-md-6">
                                    <div class="card">
                                        <div class="card-body px-4 py-4-5">
                                            <div class="row">
                                                <div
                                                    class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                    <div class="stats-icon mb-2" style="background-color: #752738;">
                                                        <i style="color: white;" class="iconly-boldFolder"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                    <h6 class="text-muted font-semibold">Department</h6>
                                                    <h6 class="font-extrabold mb-0"><?= $total_departments ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3 col-md-6">
                                    <div class="card">
                                        <div class="card-body px-4 py-4-5">
                                            <div class="row">
                                                <div
                                                    class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                                    <div class="stats-icon mb-2" style="background-color: #752738;">
                                                        <i style="color: white;" class="iconly-boldPaper"></i>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                                    <h6 class="text-muted font-semibold">Course</h6>
                                                    <h6 class="font-extrabold mb-0"><?= $total_courses ?></h6>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="mb-0">Prone Gender</h4>
                                            <select id="year-selected-prone-gender" class="form-select form-select-sm" style="max-width: 120px;">
                                                <option value="2025">2025</option>
                                                <option value="2026">2026</option>
                                                <option value="2027">2027</option>
                                                <option value="2028">2028</option>
                                                <option value="2029">2029</option>
                                                <option value="2030">2030</option>
                                            </select>
                                        </div>
                                        <div class="card-body">
                                            <div id="chart-prone-gender"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="mb-0">Prone Gender</h4>
                                            <select id="year-selected-prone-gender" class="form-select form-select-sm" style="max-width: 120px;">
                                                <option value="2025">2025</option>
                                                <option value="2026">2026</option>
                                                <option value="2027">2027</option>
                                                <option value="2028">2028</option>
                                                <option value="2029">2029</option>
                                                <option value="2030">2030</option>
                                            </select>
                                        </div>
                                        <div class="card-body">
                                            <div id="chart-prone-gender"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h4 class="mb-0">Reports</h4>
                                            <select id="year-selected-mental-health" class="form-select form-select-sm" style="max-width: 120px;">
                                                <option value="2025" selected>2025</option>
                                                <option value="2026">2026</option>
                                                <option value="2027">2027</option>
                                                <option value="2028">2028</option>
                                                <option value="2029">2029</option>
                                                <option value="2030">2030</option>

                                            </select>
                                        </div>
                                        <div class="card-body">
                                            <div id="chart-mental-health-reports" class="d-flex flex-column gap-4">
                                                <div id="chart-anxiety" style="height: 250px;"></div>
                                                <div id="chart-stress" style="height: 250px;"></div>
                                                <div id="chart-depression" style="height: 250px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-12 col-lg-3">
                            <div class="card">
                                <div class="card-body py-4 px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="stats-icon mb-2" style="background-color: #752738; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                                            </div>
                                        </div>
                                        <div class="name">
                                            <h5 class="font-bold mb-1" id="manila-time">--:-- --</h5>
                                            <h6 class="text-muted mb-0" id="manila-date">Loading...</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4>Recent Accounts</h4>
                                </div>
                                <div class="card-content pb-4">

                                    <?php if (count($recent_students) > 0): ?>
                                        <?php foreach ($recent_students as $student): ?>
                                            <div class="recent-message d-flex px-4 py-3">
                                                <div class="avatar avatar-lg">
                                                    <img src="assets/images/avatar.jpg" alt="avatar">
                                                </div>
                                                <div class="name ms-4">
                                                    <h5 class="mb-1"><?= htmlspecialchars($student['fullname']) ?></h5>
                                                    <h6 class="text-muted mb-0"><?= htmlspecialchars($student['email']) ?></h6>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-center text-muted">No recent accounts found.</p>
                                    <?php endif; ?>

                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4>Gender</h4>
                                </div>
                                <div class="card-body">
                                    <div id="chart-student-gender-count"></div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/static/js/components/dark.js"></script>
    <script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/compiled/js/app.js"></script>
    <!-- Need: Apexcharts -->
    <script src="assets/extensions/apexcharts/apexcharts.min.js"></script>
    <script src="assets/static/js/pages/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if ($show_welcome): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "Welcome <?= htmlspecialchars("$prefix $fullname") ?>",
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


    <script>
        function updateManilaTime() {
            const now = new Date();
            const optionsTime = {
                timeZone: 'Asia/Manila',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            };
            const optionsDate = {
                timeZone: 'Asia/Manila',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };

            const timeFormatter = new Intl.DateTimeFormat('en-US', optionsTime);
            const dateFormatter = new Intl.DateTimeFormat('en-US', optionsDate);

            const formattedTime = timeFormatter.format(now).toLowerCase();
            const formattedDate = dateFormatter.format(now);
            document.getElementById('manila-time').textContent = formattedTime;
            document.getElementById('manila-date').textContent = formattedDate;
        }

        setInterval(updateManilaTime, 1000);
        updateManilaTime();
    </script>

    <!-- mental health report -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const select = document.getElementById('year-selected-mental-health');

            let charts = {
                Anxiety: null,
                Stress: null,
                Depression: null,
            };

            function fetchAndRender(year) {
                fetch(`analytics/mental-health-report.php?year=${year}`)
                    .then(res => res.json())
                    .then(data => {

                        const colors = {
                            Anxiety: ["#0a883eff", "#2ecc71", "#f1c40f", "#e67e22", "#e74c3c"],
                            Stress: ["#0a883eff", "#2ecc71", "#f1c40f", "#e67e22", "#e74c3c"],
                            Depression: ["#0a883eff", "#2ecc71", "#f1c40f", "#e67e22", "#e74c3c"]
                        };

                        function createOrUpdateChart(containerId, categoryName) {
                            const options = {
                                chart: {
                                    type: 'area',
                                    height: 450,
                                    zoom: {
                                        enabled: true
                                    }
                                },
                                series: data[categoryName].series.map(s => {
                                    const count = data[categoryName].counts[s.name] || 0;
                                    return {
                                        name: `${s.name} (${count})`,
                                        data: s.data
                                    };
                                }),
                                xaxis: {
                                    categories: data[categoryName].categories
                                },
                                yaxis: {
                                    title: {
                                        text: 'Number of Students Taken the DASS-42',
                                        style: {
                                            fontSize: '14px',
                                            fontWeight: 'bold',
                                            color: '#555'
                                        }
                                    },
                                    labels: {
                                        formatter: function(val) {
                                            return Math.round(val);
                                        }
                                    }
                                },
                                stroke: {
                                    curve: 'smooth'
                                },
                                fill: {
                                    type: "gradient",
                                    gradient: {
                                        shadeIntensity: 1,
                                        opacityFrom: 0.7,
                                        opacityTo: 0.2,
                                        stops: [0, 90, 100]
                                    }
                                },
                                dataLabels: {
                                    enabled: false
                                },
                                tooltip: {
                                    shared: true,
                                    intersect: false
                                },
                                colors: colors[categoryName],
                                legend: {
                                    position: "top",
                                    horizontalAlign: "left"
                                },
                                title: {
                                    text: categoryName,
                                    align: 'left',
                                    margin: 10,
                                    style: {
                                        fontSize: '16px',
                                        fontWeight: 'bold'
                                    }
                                }
                            };

                            if (charts[categoryName]) {
                                charts[categoryName].updateOptions(options);
                            } else {
                                charts[categoryName] = new ApexCharts(document.querySelector(containerId), options);
                                charts[categoryName].render();
                            }
                        }

                        createOrUpdateChart("#chart-anxiety", "Anxiety");
                        createOrUpdateChart("#chart-stress", "Stress");
                        createOrUpdateChart("#chart-depression", "Depression");
                    })
                    .catch(err => {
                        console.error("Error loading chart data:", err);
                    });
            }

            if (select) {
                fetchAndRender(select.value);
                select.addEventListener('change', () => {
                    fetchAndRender(select.value);
                });
            } else {
                fetchAndRender(new Date().getFullYear());
            }
        });
    </script>

    <script>
        //get prone gender by year
        document.addEventListener("DOMContentLoaded", function() {
            const yearSelect = document.getElementById('year-selected-prone-gender');
            let chartProneGender = null;
            const chartContainer = document.querySelector("#chart-prone-gender");

            function hasValidData(series) {
                return series.some(s => s.data.some(d => typeof d === 'number' && !isNaN(d) && d > 0));
            }

            function fetchAndRenderChart(year) {
                fetch(`analytics/prone-gender.php?year=${year}`)
                    .then(res => res.json())
                    .then(data => {
                        if (!data.series) {
                            data.series = [];
                        }

                        chartContainer.innerHTML = '';

                        const options = {
                            annotations: {
                                position: "back"
                            },
                            dataLabels: {
                                enabled: false
                            },
                            chart: {
                                type: "bar",
                                height: 350
                            },
                            fill: {
                                opacity: 1
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: "40%",
                                    endingShape: "rounded"
                                }
                            },
                            series: data.series,
                            colors: ["#752738", "#000"],
                            xaxis: {
                                categories: data.categories || []
                            },
                            yaxis: {
                                labels: {
                                    formatter: val => parseInt(val)
                                }
                            },
                            legend: {
                                position: "top",
                                horizontalAlign: "left"
                            }
                        };

                        if (chartProneGender) {
                            chartProneGender.updateOptions(options);
                        } else {
                            chartProneGender = new ApexCharts(chartContainer, options);
                            chartProneGender.render();
                        }
                    })

                    .catch(err => {
                        console.error(err);
                        if (chartProneGender) {
                            chartProneGender.destroy();
                            chartProneGender = null;
                        }
                        chartContainer.innerHTML = '<p style="text-align:center; padding: 50px; font-weight: bold; color: red;">Error loading data</p>';
                    });
            }

            fetchAndRenderChart(yearSelect.value);
            yearSelect.addEventListener('change', () => {
                fetchAndRenderChart(yearSelect.value);
            });
        });
    </script>



    <script>
        // get count gender students female and male
        document.addEventListener("DOMContentLoaded", function() {
            fetch('analytics/student-gender.php')
                .then(response => response.json())
                .then(data => {
                    let studentGenderCount = {
                        series: data.series,
                        labels: data.labels,
                        colors: ["#000", "#752738"],
                        chart: {
                            type: "donut",
                            width: "100%",
                            height: "350px",
                        },
                        legend: {
                            position: "bottom",
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: "30%",
                                },
                            },
                        },
                    };

                    var chartStudentGenderCount = new ApexCharts(
                        document.getElementById("chart-student-gender-count"),
                        studentGenderCount
                    );

                    chartStudentGenderCount.render();
                })
                .catch(error => {
                    console.error('Error loading student gender data:', error);
                });
        });
    </script>


    <script>
        function toggleEdit(editMode) {
            document.getElementById('viewProfile').style.display = editMode ? 'none' : 'block';
            document.getElementById('editProfile').style.display = editMode ? 'block' : 'none';
            document.getElementById('editBtn').style.display = editMode ? 'none' : 'inline-block';
            document.getElementById('saveBtn').style.display = editMode ? 'inline-block' : 'none';
            document.getElementById('cancelBtn').style.display = editMode ? 'inline-block' : 'none';
        }

        function previewImage(event) {
            const output = document.getElementById('preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>

</body>

</html>