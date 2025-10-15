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

$get_admin = $conn->query("SELECT * FROM tbl_admin");
$admins = $get_admin->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone_number = trim($_POST['phone_number'] ?? '');

    $checkEmailStmt = $conn->prepare("SELECT id FROM tbl_admin WHERE email = ?");
    $checkEmailStmt->execute([$email]);

    if ($checkEmailStmt->rowCount() > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different one.";
        header("Location: admin_management.php");
        exit();
    }


    $profile_picture_path = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'profile/';
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = basename($_FILES['profile_picture']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExt, $allowedExt)) {
            $_SESSION['error'] = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            header("Location: admin_management.php");
            exit();
        }
        $newFileName = uniqid('admin_', true) . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;
        if (!move_uploaded_file($fileTmpPath, $destPath)) {
            $_SESSION['error'] = "Failed to upload profile picture.";
            header("Location: admin_management.php");
            exit();
        }

        $profile_picture_path = 'profile/' . $newFileName;
    }

    $stmt = $conn->prepare("INSERT INTO tbl_admin (fullname, gender, email, password, phone_number, profile_picture) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fullname, $gender, $email, $password, $phone_number, $profile_picture_path]);

    $_SESSION['success'] = "Admin added successfully.";
    header("Location: admin_management.php");
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

                        <li class="sidebar-item active">
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
                                                <input type="file" name="profile_picture" class="form-control" onchange="previewEditImage(event)">
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
                                <h3>Admin Management</h3>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Admin Management</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="card">
                            <div class="card-header">
                                <button type="button" style="float: right;" class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#addAdminModal">
                                    + Add Admin
                                </button>
                            </div>

                            <div class="modal fade text-left" id="addAdminModal" tabindex="-1" role="dialog"
                                aria-labelledby="myAddAdminModal" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="myAddAdminModal">Add Admin</h5>
                                            <button type="button" class="close rounded-pill" data-bs-dismiss="modal"
                                                aria-label="Close">
                                                <i data-feather="x"></i>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form class="form" data-parsley-validate enctype="multipart/form-data" method="POST">
                                                <div class="row">
                                                    <!-- Left Column -->
                                                    <div class="col-md-6 col-12">
                                                        <!-- Profile Picture Upload -->
                                                        <div class="form-group mandatory">
                                                            <label for="profile-picture" class="form-label">Profile Picture</label>
                                                            <input type="file" id="profile-picture" class="form-control" accept="image/*"
                                                                name="profile_picture" data-parsley-required="true" onchange="previewImage(event)" />

                                                            <!-- Image Preview Box -->
                                                            <div id="image-preview-box" style="position: relative; margin-top: 10px; display: none;">
                                                                <img id="preview-image" src="" alt="Preview" style="max-width: 100%; border: 1px solid #ccc; padding: 5px;">
                                                                <button type="button" onclick="removeImage()" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; padding: 2px 6px; cursor: pointer;">×</button>
                                                            </div>
                                                        </div>

                                                        <!-- Fullname -->
                                                        <div class="form-group mandatory">
                                                            <label for="fullname-column" class="form-label">Fullname</label>
                                                            <input type="text" id="fullname-column" class="form-control" name="fullname" placeholder="Fullname" data-parsley-required="true" />
                                                        </div>

                                                        <!-- Gender -->
                                                        <div class="form-group mandatory">
                                                            <label class="form-label">Gender</label>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="gender" id="genderMale" value="male" data-parsley-required="true" />
                                                                <label class="form-check-label" for="genderMale">Male</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="female" />
                                                                <label class="form-check-label" for="genderFemale">Female</label>
                                                            </div>
                                                        </div>

                                                        <!-- Phone Number -->
                                                        <div class="form-group mandatory">
                                                            <label for="phone-number" class="form-label">Phone Number</label>
                                                            <input type="text" id="phone-number" class="form-control" name="phone_number" placeholder="Phone Number" data-parsley-required="true" />
                                                        </div>
                                                    </div>

                                                    <!-- Right Column -->
                                                    <div class="col-md-6 col-12">
                                                        <!-- Email -->
                                                        <div class="form-group mandatory">
                                                            <label for="email-id-column" class="form-label">Email</label>
                                                            <input type="email" id="email-id-column" class="form-control" name="email" placeholder="Email" data-parsley-required="true" />
                                                        </div>

                                                        <!-- Password -->
                                                        <div class="form-group mandatory">
                                                            <label for="password" class="form-label">Password</label>
                                                            <input type="password" id="password" class="form-control" name="password" placeholder="Password" data-parsley-required="true" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Submit and Close Buttons -->
                                                <div class="row mt-4">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                                        <button type="reset" data-bs-dismiss="modal" class="btn btn-light-secondary me-1 mb-1">Close</button>
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
                                                <th>Profile Picture</th>
                                                <th>Fullname</th>
                                                <th>Gender</th>
                                                <th>Email</th>
                                                <th>Phone Number</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($admins as $admin): ?>
                                                <?php if ($admin['id'] == $_SESSION['admin_id']) continue; ?>
                                                <tr>
                                                    <td>
                                                        <?php if (!empty($admin['profile_picture'])): ?>
                                                            <img src="<?= htmlspecialchars($admin['profile_picture']) ?>" alt="Profile Picture" width="50" height="50">
                                                        <?php else: ?>
                                                            <span>No Image</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($admin['fullname']) ?></td>
                                                    <td><?= htmlspecialchars($admin['gender']) ?></td>
                                                    <td><?= htmlspecialchars($admin['email']) ?></td>
                                                    <td><?= htmlspecialchars($admin['phone_number']) ?></td>
                                                    <td>
                                                        <!-- Edit Button -->
                                                        <button type="button"
                                                            class="btn btn-sm btn-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#editModal<?= $admin['id'] ?>">
                                                            Edit
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <a href="delete_admin.php?id=<?= $admin['id'] ?>" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Are you sure you want to delete this admin?');">
                                                            Delete
                                                        </a>
                                                    </td>
                                                </tr>

                                                <!-- Modal for this admin -->
                                                <div class="modal fade" id="editModal<?= $admin['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $admin['id'] ?>" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <form method="POST" action="update_admin.php">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="editModalLabel<?= $admin['id'] ?>"><?= htmlspecialchars($admin['fullname']) ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>

                                                                <div class="modal-body">
                                                                    <input type="hidden" name="id" value="<?= $admin['id'] ?>">

                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <!-- Fullname -->
                                                                            <div class="form-group">
                                                                                <label>Fullname</label>
                                                                                <input type="text" class="form-control" name="fullname" value="<?= htmlspecialchars($admin['fullname']) ?>" required>
                                                                            </div>

                                                                            <!-- Gender -->
                                                                            <div class="form-group mt-2">
                                                                                <label>Gender</label><br>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input" type="radio" name="gender" id="genderMale<?= $admin['id'] ?>" value="male" <?= $admin['gender'] === 'male' ? 'checked' : '' ?>>
                                                                                    <label class="form-check-label" for="genderMale<?= $admin['id'] ?>">Male</label>
                                                                                </div>
                                                                                <div class="form-check form-check-inline">
                                                                                    <input class="form-check-input" type="radio" name="gender" id="genderFemale<?= $admin['id'] ?>" value="female" <?= $admin['gender'] === 'female' ? 'checked' : '' ?>>
                                                                                    <label class="form-check-label" for="genderFemale<?= $admin['id'] ?>">Female</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <!-- Email -->
                                                                            <div class="form-group">
                                                                                <label>Email</label>
                                                                                <input style="background-color: gray;" readonly type="email" class="form-control" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                                                                            </div>

                                                                            <!-- Phone -->
                                                                            <div class="form-group mt-2">
                                                                                <label>Phone Number</label>
                                                                                <input type="text" class="form-control" name="phone_number" value="<?= htmlspecialchars($admin['phone_number']) ?>" required>
                                                                            </div>
                                                                        </div>
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
        function previewImage(event) {
            const previewBox = document.getElementById('image-preview-box');
            const previewImage = document.getElementById('preview-image');
            const fileInput = event.target;

            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewBox.style.display = 'block';
                };

                reader.readAsDataURL(fileInput.files[0]);
            }
        }

        function removeImage() {
            const previewBox = document.getElementById('image-preview-box');
            const previewImage = document.getElementById('preview-image');
            const fileInput = document.getElementById('profile-picture');

            fileInput.value = '';
            previewImage.src = '';
            previewBox.style.display = 'none';
        }
    </script>

    <script>
        function toggleEdit(editMode) {
            document.getElementById('viewProfile').style.display = editMode ? 'none' : 'block';
            document.getElementById('editProfile').style.display = editMode ? 'block' : 'none';
            document.getElementById('editBtn').style.display = editMode ? 'none' : 'inline-block';
            document.getElementById('saveBtn').style.display = editMode ? 'inline-block' : 'none';
            document.getElementById('cancelBtn').style.display = editMode ? 'inline-block' : 'none';
        }

        function previewEditImage(event) {
            const output = document.getElementById('preview');
            output.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>

</body>

</html>