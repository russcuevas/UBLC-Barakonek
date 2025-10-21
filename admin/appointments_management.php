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

$stmtCounselors = $conn->prepare("SELECT id, fullname FROM tbl_admin ORDER BY fullname ASC");
$stmtCounselors->execute();
$counselors = $stmtCounselors->fetchAll(PDO::FETCH_ASSOC);

$admin_id = $_SESSION['admin_id'];

$stmtAppointments = $conn->prepare("
    SELECT 
        a.*, 
        ad.fullname AS counselor_name,
        s.fullname AS student_name,
        s.year_level,
        d.department_name,
        c.course_name
    FROM tbl_appointments a
    JOIN tbl_admin ad ON a.admin_id = ad.id
    JOIN tbl_students s ON a.student_id = s.id
    LEFT JOIN tbl_department d ON s.department_id = d.id
    LEFT JOIN tbl_course c ON s.course_id = c.id
    WHERE a.admin_id = ?
    ORDER BY a.requested_at DESC
");
$stmtAppointments->execute([$admin_id]);
$appointments = $stmtAppointments->fetchAll(PDO::FETCH_ASSOC);




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

                        <li class="sidebar-item active">
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
                                <h3>My appointments</h3>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Appointment</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="row">

                            <!-- RIGHT: Appointment Table -->
                            <div class="col-lg-12 col-12">
                                <div class="card">
                                    <div class="card-header">
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="table1">
                                                <thead>
                                                    <tr>
                                                        <th>Student</th>
                                                        <th>Scheduled At</th>
                                                        <th>Type</th>
                                                        <th>Remarks</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>

                                                </thead>
                                                <tbody>
                                                    <?php foreach ($appointments as $appointment): ?>
                                                        <tr>
                                                            <td>
                                                                <?= htmlspecialchars($appointment['student_name']) ?> <br>
                                                                <?= htmlspecialchars($appointment['department_name']) ?> <br>
                                                                <?= htmlspecialchars($appointment['year_level']) ?> -
                                                                <?= htmlspecialchars($appointment['course_name']) ?>
                                                            </td>
                                                            <td><?= htmlspecialchars(date('M d Y - g:ia', strtotime($appointment['scheduled_date']))) ?></td>
                                                            <td><?= $appointment['type'] === 'online' ? '<span style="color: green;">Online</span>' : '<span style="color: green;">Face to face</span>' ?></td>
                                                            <td><?= htmlspecialchars($appointment['student_remarks']) ?></td>
                                                            <?php
                                                            $status = strtolower($appointment['status']);
                                                            switch ($status) {
                                                                case 'pending':
                                                                    $badgeClass = 'bg-warning';
                                                                    break;
                                                                case 'rejected':
                                                                    $badgeClass = 'bg-danger';
                                                                    break;
                                                                case 'scheduled':
                                                                    $badgeClass = 'bg-info';
                                                                    break;
                                                                case 'completed':
                                                                    $badgeClass = 'bg-success';
                                                                    break;
                                                                default:
                                                                    $badgeClass = 'bg-secondary';
                                                            }
                                                            ?>
                                                            <td>
                                                                <span class="badge <?= $badgeClass ?>">
                                                                    <?= htmlspecialchars(ucfirst($appointment['status'])) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php if ($appointment['status'] === 'Pending'): ?>
                                                                    <a href="approved_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-success mb-2">Approve</a>
                                                                    <br>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger"
                                                                        onclick="rejectAppointment(<?= $appointment['id'] ?>)">Reject</button>
                                                                <?php elseif ($appointment['status'] === 'Rejected'): ?>
                                                                    <a href="delete_appointment.php?id=<?= $appointment['id'] ?>"
                                                                        class="btn btn-sm btn-danger"
                                                                        onclick="return confirm('Are you sure you want to delete this appointment? This action cannot be undone.');">
                                                                        Delete
                                                                    </a>
                                                                <?php else: ?>
                                                                    <a href="#"
                                                                        class="btn btn-sm btn-warning"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#viewScheduleModal<?= $appointment['id'] ?>">
                                                                        View schedule & Chat
                                                                    </a>
                                                                    <div class="modal fade" id="viewScheduleModal<?= $appointment['id'] ?>" tabindex="-1" aria-labelledby="scheduleLabel<?= $appointment['id'] ?>" aria-hidden="true">
                                                                        <div class="modal-dialog">
                                                                            <div class="modal-content">
                                                                                <!-- Modal Header -->
                                                                                <div class="modal-header text-white">
                                                                                    <h5 class="modal-title" id="scheduleLabel<?= $appointment['id'] ?>">Appointment Details</h5>
                                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                </div>

                                                                                <!-- Modal Body -->
                                                                                <div class="modal-body">
                                                                                    <div class="mb-3">
                                                                                        <p class="mb-1"><strong>📅 Scheduled Date & Time:</strong></p>
                                                                                        <p class="text-muted"><?= htmlspecialchars(date('F d, Y - g:i A', strtotime($appointment['scheduled_date']))) ?></p>
                                                                                    </div>

                                                                                    <div class="mb-3">
                                                                                        <p class="mb-1"><strong>📝 Type:</strong></p>
                                                                                        <p class="text-muted"><?= $appointment['type'] === 'online' ? 'Online Counseling' : 'Face-to-Face Counseling' ?></p>
                                                                                    </div>

                                                                                    <div class="mb-3">
                                                                                        <p class="mb-1"><strong>👨‍🏫 Counselor:</strong></p>
                                                                                        <p class="text-muted"><?= htmlspecialchars($appointment['counselor_name']) ?></p>
                                                                                    </div>

                                                                                    <div class="mb-3">
                                                                                        <p class="mb-1"><strong>🗨️ Why need counseling:</strong></p>
                                                                                        <p class="text-muted"><?= htmlspecialchars($appointment['student_remarks']) ?></p>
                                                                                    </div>

                                                                                    <?php
                                                                                    date_default_timezone_set('Asia/Manila');
                                                                                    $now = new DateTime();
                                                                                    $scheduled = new DateTime($appointment['scheduled_date']);
                                                                                    ?>


                                                                                    <!-- Conditional Message Based on Type -->
                                                                                    <?php if ($appointment['type'] === 'online'): ?>
                                                                                        <?php
                                                                                        $appointment_id = $appointment['id'];
                                                                                        $stmt = $conn->prepare("SELECT chat_status FROM tbl_chat_sessions WHERE appointment_id = ? ORDER BY sent_at DESC LIMIT 1");
                                                                                        $stmt->execute([$appointment_id]);
                                                                                        $last_chat = $stmt->fetch();

                                                                                        $last_chat_status = $last_chat['chat_status'] ?? null;
                                                                                        ?>
                                                                                        <div class="alert alert-primary text-center mt-4">
                                                                                            <?php if ($now >= $scheduled): ?>
                                                                                                <strong>✅ Your online session is scheduled now.</strong><br>

                                                                                                <div class="d-grid mt-3">
                                                                                                    <?php if ($last_chat_status === 'ended'): ?>
                                                                                                        <!-- Chat ended - show Chat History -->
                                                                                                        <a href="session_appointment.php?id=<?= $appointment_id ?>" class="btn btn-secondary btn-lg">
                                                                                                            Chat History
                                                                                                        </a>
                                                                                                    <?php else: ?>
                                                                                                        <!-- Chat active or no chat yet - show Start Session -->
                                                                                                        <a href="start_chat_session.php?id=<?= $appointment_id ?>" class="btn btn-success btn-lg">
                                                                                                            Start Session
                                                                                                        </a>
                                                                                                    <?php endif; ?>
                                                                                                </div>

                                                                                            <?php else: ?>
                                                                                                <strong>⏳ Your online counseling session is upcoming.</strong><br>
                                                                                                Please return at the scheduled time and wait for the counselor to start.
                                                                                            <?php endif; ?>
                                                                                        </div>



                                                                                    <?php else: ?>
                                                                                        <div class="alert alert-primary text-center mt-4">
                                                                                            <strong>📍 Face-to-Face Appointment</strong><br>
                                                                                            <?php if ($now >= $scheduled): ?>
                                                                                                <?php if ($appointment['status'] === 'Completed'): ?>
                                                                                                    <div class="d-grid mt-3">
                                                                                                        <span class="badge bg-success fs-6">✅ Completed</span>
                                                                                                    </div>
                                                                                                <?php else: ?>
                                                                                                    <div class="d-grid mt-3">
                                                                                                        <a href="completed_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-success btn-lg">
                                                                                                            Mark as Completed
                                                                                                        </a>
                                                                                                    </div>
                                                                                                <?php endif; ?>
                                                                                            <?php endif; ?>
                                                                                        </div>


                                                                                    <?php endif; ?>

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
    <script src="assets/extensions/jquery/jquery.min.js"></script>
    <script src="assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/static/js/pages/datatables.js"></script>
    <script src="assets/extensions/parsleyjs/parsley.min.js"></script>
    <script src="assets/static/js/pages/parsley.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function rejectAppointment(appointmentId) {
            Swal.fire({
                title: 'Reject Appointment',
                input: 'textarea',
                inputLabel: 'Reason for rejection',
                inputPlaceholder: 'Enter your remarks here...',
                inputAttributes: {
                    'aria-label': 'Type your remarks here'
                },
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                showLoaderOnConfirm: true,
                preConfirm: (remarks) => {
                    if (!remarks.trim()) {
                        Swal.showValidationMessage('Remarks are required before rejecting');
                        return false;
                    }

                    // Send POST request to reject_appointment.php
                    return fetch('reject_appointment.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `appointment_id=${appointmentId}&remarks=${encodeURIComponent(remarks)}`
                        })
                        .then(response => response.text())
                        .then(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Appointment Rejected',
                                text: 'The appointment has been marked as rejected.',
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                window.location.reload();
                            });
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong. Please try again.'
                            });
                        });
                }
            });
        }
    </script>

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