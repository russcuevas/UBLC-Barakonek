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

$categories = $conn->query("SELECT * FROM tbl_categories")->fetchAll(PDO::FETCH_ASSOC);
$sql = "SELECT q.*, c.category_name 
        FROM tbl_questions q 
        LEFT JOIN tbl_categories c ON q.category_id = c.id";
$questions = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? null;
    $question = $_POST['question'] ?? '';

    $stmt = $conn->prepare("INSERT INTO tbl_questions (category_id, question) VALUES (?, ?)");
    $stmt->execute([$category_id, $question]);
    $_SESSION['success'] = "Question added successfully.";
    header('Location: questions_management.php');
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

                                <li class="submenu-item">
                                    <a href="categories_management.php" class="submenu-link">Categories</a>

                                </li>

                                <li class="submenu-item active">
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
                                <h3>DASS-42</h3>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">DASS-42
                                        </li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="row">
                            <!-- Left column: Questions table -->
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header">
                                        <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                                            + Add Questions
                                        </button>
                                    </div>

                                    <!-- Add Question Modal (as you already have) -->
                                    <div class="modal fade text-left" id="addQuestionModal" tabindex="-1" role="dialog" aria-labelledby="myAddQuestionsModal" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="myAddQuestionsModal">Add Questions</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="form" action="" method="POST" data-parsley-validate enctype="multipart/form-data">
                                                        <div class="form-group mandatory mb-3">
                                                            <label for="category-id" class="form-label">Related Categories</label>
                                                            <select id="category-id" name="category_id" class="form-select" data-parsley-required="true">
                                                                <option value="">-- Select Category --</option>
                                                                <?php foreach ($categories as $cat): ?>
                                                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mandatory">
                                                            <label for="question-column" class="form-label">Question</label>
                                                            <input type="text" id="question-column" class="form-control" name="question" placeholder="Question" data-parsley-required="true" />
                                                        </div>
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

                                    <div class="card-body table-responsive">
                                        <table class="table" id="table1">
                                            <thead>
                                                <tr>
                                                    <th>Category</th>
                                                    <th>Question</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($questions as $q): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($q['category_name']) ?></td>
                                                        <td><?= htmlspecialchars($q['question']) ?></td>
                                                        <td>
                                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#updateModal<?= $q['id'] ?>">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>

                                                            <a href="delete_question.php?id=<?= $q['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this question?')">
                                                                <i class="bi bi-trash"></i>
                                                            </a>
                                                        </td>

                                                    </tr>

                                                    <!-- Update Modal -->
                                                    <div class="modal fade" id="updateModal<?= $q['id'] ?>" tabindex="-1" aria-labelledby="updateModalLabel<?= $q['id'] ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <form method="POST" action="update_question.php">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="updateModalLabel<?= $q['id'] ?>">Update Question</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>

                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="id" value="<?= $q['id'] ?>">

                                                                        <div class="form-group mandatory mb-3">
                                                                            <label for="category-id-<?= $q['id'] ?>" class="form-label">Related Category</label>
                                                                            <select id="category-id-<?= $q['id'] ?>" name="category_id" class="form-select" required>
                                                                                <option value="">-- Select Category --</option>
                                                                                <?php foreach ($categories as $cat): ?>
                                                                                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $q['category_id'] ? 'selected' : '' ?>>
                                                                                        <?= htmlspecialchars($cat['category_name']) ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>

                                                                        <div class="form-group mandatory">
                                                                            <label for="question-<?= $q['id'] ?>" class="form-label">Question</label>
                                                                            <input type="text" id="question-<?= $q['id'] ?>" name="question" class="form-control" value="<?= htmlspecialchars($q['question']) ?>" required />
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

                            <!-- Right column: Exam Layout or Preview -->
                            <div class="col-md-7">
    <div class="card">
        <div class="card-header position-relative">
            <a href="print/print_questionnaire.php" target="_blank" class="btn btn-primary btn-sm float-end">
                Print
            </a>

            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="logo d-flex align-items-center">
                    <a href="index.html" class="d-flex align-items-center text-decoration-none text-dark">
                        <img src="assets/images/ub-logo.png" alt="Logo" style="height:40px;" class="me-2">
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