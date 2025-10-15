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

$get_departments = $conn->query("SELECT * FROM tbl_department");
$departments = $get_departments->fetchAll(PDO::FETCH_ASSOC);

$get_course = $conn->query("SELECT * FROM tbl_course");
$course = $get_course->fetchAll(PDO::FETCH_ASSOC);


$get_students = $conn->query("
    SELECT s.*, d.department_name, c.course_name
    FROM tbl_students s
    LEFT JOIN tbl_department d ON s.department_id = d.id
    LEFT JOIN tbl_course c ON s.course_id = c.id
");
$students = $get_students->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $department_id = intval($_POST['department_id'] ?? 0);
    $year_level = trim($_POST['year_level'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);
    $gender = trim($_POST['gender'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    $student_no = trim($_POST['student_no'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (
        empty($fullname) || empty($department_id) || empty($year_level) ||
        empty($course_id) || empty($gender) || empty($phone_number) ||
        empty($student_no) || empty($email) || empty($password)
    ) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: student_management.php');
        exit();
    }

    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM tbl_students WHERE email = ? OR student_no = ?");
    $checkStmt->execute([$email, $student_no]);
    $exists = $checkStmt->fetchColumn();

    if ($exists > 0) {
        $_SESSION['error'] = "Email or Student # already exists.";
        header('Location: student_management.php');
        exit();
    }

    $insertStmt = $conn->prepare("
        INSERT INTO tbl_students 
        (fullname, department_id, year_level, course_id, gender, phone_number, email, student_no, password, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");

    $success = $insertStmt->execute([
        $fullname,
        $department_id,
        $year_level,
        $course_id,
        $gender,
        $phone_number,
        $email,
        $student_no,
        $password
    ]);

    if ($success) {
        $_SESSION['success'] = "Student added successfully.";
    } else {
        $_SESSION['error'] = "Something went wrong. Try again.";
    }

    header('Location: student_management.php');
    exit();
}

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

                        <li class="sidebar-item active has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-collection-fill"></i>
                                <span>Academic</span>
                            </a>

                            <ul class="submenu active">

                                <li class="submenu-item">
                                    <a href="department_management.php" class="submenu-link">Department</a>
                                </li>

                                <li class="submenu-item">
                                    <a href="course_management.php" class="submenu-link">Course</a>

                                </li>

                                <li class="submenu-item active">
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
                    <div class="page-title">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>Student Management</h3>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Student Management</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="card">
                            <div class="card-header">
                                <button type="button" style="float: right;" class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#addStudentModal">
                                    + Add Student
                                </button>
                            </div>

                            <div class="modal fade text-left" id="addStudentModal" tabindex="-1" role="dialog"
                                aria-labelledby="myAddStudentModal" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="myAddStudentModal">Add Student</h5>
                                            <button type="button" class="close rounded-pill" data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <i data-feather="x"></i>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="form" action="" method="POST" data-parsley-validate enctype="multipart/form-data">
                                                <div class="row">
                                                    <!-- Left Column -->
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group mandatory">
                                                            <label for="full-name" class="form-label">Fullname
                                                            </label>
                                                            <input type="text" id="full-name" class="form-control"
                                                                name="fullname" placeholder="Fullname"
                                                                data-parsley-required="true" />
                                                        </div>

                                                        <div class="form-group mandatory mb-3">
                                                            <label for="department-id" class="form-label">Select
                                                                Department</label>
                                                            <select id="department-id" name="department_id"
                                                                class="form-select" data-parsley-required="true">
                                                                <option value="">-- Select Department --</option>
                                                                <?php foreach ($departments as $department): ?>
                                                                    <option value="<?= htmlspecialchars($department['id']) ?>">
                                                                        <?= htmlspecialchars($department['department_name']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mandatory mb-3">
                                                            <label for="year-level-id" class="form-label">Select
                                                                Year Level</label>
                                                            <select id="year-level-id" name="year_level"
                                                                class="form-select" data-parsley-required="true">
                                                                <option value="">-- Select Year Level --</option>
                                                                <option value="I">I</option>
                                                                <option value="II">II</option>
                                                                <option value="III">III</option>
                                                                <option value="IV">IV</option>

                                                            </select>
                                                        </div>
                                                        <div class="form-group mandatory mb-3">
                                                            <label for="course-id" class="form-label">Select
                                                                Course</label>
                                                            <select id="course-id" name="course_id" class="form-select"
                                                                data-parsley-required="true">
                                                                <option value="">-- Select Department --</option>
                                                                <?php foreach ($course as $courses): ?>
                                                                    <option value="<?= htmlspecialchars($courses['id']) ?>">
                                                                        <?= htmlspecialchars($courses['course_name']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>

                                                        <!-- Gender -->
                                                        <div class="form-group mandatory">
                                                            <label class="form-label">Gender</label>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    name="gender" id="genderMale" value="male"
                                                                    data-parsley-required="true" />
                                                                <label class="form-check-label"
                                                                    for="genderMale">Male</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio"
                                                                    name="gender" id="genderFemale" value="female" />
                                                                <label class="form-check-label"
                                                                    for="genderFemale">Female</label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mandatory">
                                                            <label for="phone-number" class="form-label">Phone Number
                                                            </label>
                                                            <input type="text" id="phone-number" class="form-control"
                                                                name="phone_number" placeholder="Phone Number"
                                                                data-parsley-required="true" />
                                                        </div>
                                                    </div>

                                                    <!-- Right Column -->
                                                    <div class="col-md-6 col-12">
                                                        <div class="form-group mandatory">
                                                            <label for="student-id-column"
                                                                class="form-label">Student #</label>
                                                            <input type="text" id="student-id-column"
                                                                class="form-control" name="student_no" placeholder="Student No."
                                                                data-parsley-required="true" />
                                                        </div>

                                                        <div class="form-group mandatory">
                                                            <label for="email-id-column"
                                                                class="form-label">Email</label>
                                                            <input type="email" id="email-id-column"
                                                                class="form-control" name="email" placeholder="Email"
                                                                data-parsley-required="true" />
                                                        </div>

                                                        <!-- Password -->
                                                        <div class="form-group mandatory">
                                                            <label for="password" class="form-label">Password</label>
                                                            <input type="password" id="password" class="form-control"
                                                                name="password" placeholder="Password"
                                                                data-parsley-required="true" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Submit and Close Buttons -->
                                                <div class="row mt-4">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="submit"
                                                            class="btn btn-primary me-1 mb-1">Submit</button>
                                                        <button type="reset" data-bs-dismiss="modal"
                                                            class="btn btn-light-secondary me-1 mb-1">Close</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table" id="table1">
                                        <thead>
                                            <tr>
                                                <th>Profile</th>
                                                <th>Student #</th>
                                                <th>Fullname</th>
                                                <th>Email</th>
                                                <th>Department</th>
                                                <th>Year-Level & Course</th>
                                                <th>Gender</th>
                                                <th>Phone Number</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($students as $student): ?>
                                                <tr>
                                                    <td><img style="height: 50px;" src="assets/images/avatar.jpg" alt="avatar"></td>
                                                    <td><?= htmlspecialchars($student['student_no']) ?></td>
                                                    <td><?= htmlspecialchars($student['fullname']) ?></td>
                                                    <td><?= htmlspecialchars($student['email']) ?></td>
                                                    <td><?= htmlspecialchars($student['department_name']) ?></td>
                                                    <td><?= $student['year_level'] . ' - ' . htmlspecialchars($student['course_name']) ?></td>
                                                    <td style="text-transform: capitalize;"><?= htmlspecialchars($student['gender']) ?></td>
                                                    <td><?= htmlspecialchars($student['phone_number']) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editStudentModal<?= $student['id'] ?>">
                                                            Edit
                                                        </button>

                                                        <a href="delete_student.php?id=<?= $student['id'] ?>" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this student?');">
                                                            Delete
                                                        </a>
                                                    </td>
                                                </tr>

                                                <!-- Update Modal -->
                                                <div class="modal fade" id="editStudentModal<?= $student['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $student['id'] ?>" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <form method="POST" action="update_student.php">
                                                            <input type="hidden" name="id" value="<?= $student['id'] ?>">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="editModalLabel<?= $student['id'] ?>"><?= $student['fullname'] ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>

                                                                <div class="modal-body row">
                                                                    <!-- Fullname -->
                                                                    <div class="form-group col-md-6 mb-3">
                                                                        <label>Fullname</label>
                                                                        <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($student['fullname']) ?>" required>
                                                                    </div>

                                                                    <!-- Student No -->
                                                                    <div class="form-group col-md-6 mb-3">
                                                                        <label>Student #</label>
                                                                        <input style="background-color: gray;" type="text" name="student_no" class="form-control" value="<?= htmlspecialchars($student['student_no']) ?>" required readonly>
                                                                    </div>
                                                                    <!-- Phone -->
                                                                    <div class="form-group col-md-6 mb-3">
                                                                        <label>Phone Number</label>
                                                                        <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($student['phone_number']) ?>" required>
                                                                    </div>

                                                                    <!-- Email -->
                                                                    <div class="form-group col-md-6 mb-3">
                                                                        <label>Email</label>
                                                                        <input style="background-color: gray;" type="email" name="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>" required readonly>
                                                                    </div>



                                                                    <!-- Gender -->
                                                                    <div class="form-group col-md-6 mb-3">
                                                                        <label>Gender</label><br>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="gender" value="male" <?= $student['gender'] === 'male' ? 'checked' : '' ?>>
                                                                            <label class="form-check-label">Male</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio" name="gender" value="female" <?= $student['gender'] === 'female' ? 'checked' : '' ?>>
                                                                            <label class="form-check-label">Female</label>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Department -->
                                                                    <div class="form-group col-md-6 mb-3">
                                                                        <label>Department</label>
                                                                        <select name="department_id" class="form-select" required>
                                                                            <?php foreach ($departments as $department): ?>
                                                                                <option value="<?= $department['id'] ?>" <?= $student['department_id'] == $department['id'] ? 'selected' : '' ?>>
                                                                                    <?= htmlspecialchars($department['department_name']) ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>

                                                                    <!-- Year Level -->
                                                                    <div class="form-group col-md-6 mb-3">
                                                                        <label>Year Level</label>
                                                                        <select name="year_level" class="form-select" required>
                                                                            <option value="I" <?= $student['year_level'] == 'I' ? 'selected' : '' ?>>I</option>
                                                                            <option value="II" <?= $student['year_level'] == 'II' ? 'selected' : '' ?>>II</option>
                                                                            <option value="III" <?= $student['year_level'] == 'III' ? 'selected' : '' ?>>III</option>
                                                                            <option value="IV" <?= $student['year_level'] == 'IV' ? 'selected' : '' ?>>IV</option>
                                                                        </select>
                                                                    </div>

                                                                    <!-- Course -->
                                                                    <div class="form-group col-md-6 mb-3">
                                                                        <label>Course</label>
                                                                        <select name="course_id" class="form-select" required>
                                                                            <?php foreach ($course as $c): ?>
                                                                                <option value="<?= $c['id'] ?>" <?= $student['course_id'] == $c['id'] ? 'selected' : '' ?>>
                                                                                    <?= htmlspecialchars($c['course_name']) ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="modal-footer">
                                                                    <button type="submit" class="btn btn-success">Update</button>
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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