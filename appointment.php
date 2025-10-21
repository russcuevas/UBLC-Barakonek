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
// $prefix = strtolower($gender) === 'female' ? 'Ms,' : 'Mr,';
// unset welcome sweetalert
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


$stmtCounselors = $conn->prepare("SELECT id, fullname FROM tbl_admin ORDER BY fullname ASC");
$stmtCounselors->execute();
$counselors = $stmtCounselors->fetchAll(PDO::FETCH_ASSOC);


$stmtAppointments = $conn->prepare("
    SELECT 
        a.*, 
        ad.fullname AS counselor_name
    FROM tbl_appointments a
    JOIN tbl_admin ad ON a.admin_id = ad.id
    WHERE a.student_id = ?
    ORDER BY a.requested_at DESC
");
$stmtAppointments->execute([$student_id]);
$appointments = $stmtAppointments->fetchAll(PDO::FETCH_ASSOC);



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $requested_at = $_POST['requested_at'] ?? '';
    $counselor_id = $_POST['counselor'] ?? '';
    $appointment_type = $_POST['appointment_type'] ?? '';
    $remarks = $_POST['remarks'] ?? '';


    // Prepare the query to insert a new appointment request
    $stmt = $conn->prepare("
            INSERT INTO tbl_appointments (student_id, admin_id, requested_at, scheduled_date, type, status, student_remarks)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

    $scheduled_date = date('Y-m-d H:i:s', strtotime($requested_at));
    $status = 'Pending';

    // Bind the parameters and execute
    $stmt->execute([
        $student_id,
        $counselor_id,
        $requested_at,  // or scheduled_date depending on how you're handling date
        $scheduled_date,
        $appointment_type,
        $status,
        $remarks
    ]);

    // Check if the insertion was successful
    if ($stmt->rowCount()) {
        $_SESSION['success'] = "Appointment successfully requested.";
    } else {
        $_SESSION['error'] = "Failed to request appointment. Please try again.";
    }

    header("Location: appointment.php");
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
    <link rel="stylesheet" href="assets/dashboard/extensions/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="./assets/dashboard/compiled/css/table-datatable-jquery.css">
    <link rel="stylesheet" href="./assets/dashboard/compiled/css/app.css">
    <link rel="stylesheet" href="./assets/dashboard/compiled/css/app-dark.css">
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
                            <a href="index.html">
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

                        <li class="sidebar-item">
                            <a href="dashboard.php" class='sidebar-link'>
                                <i class="bi bi-house-fill"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="sidebar-item active has-sub">
                            <a href="#" class='sidebar-link'>
                                <i class="bi bi-file-text-fill"></i>
                                <span>Inquiry</span>
                            </a>

                            <ul class="submenu active">

                                <li class="submenu-item active">
                                    <a href="appointment.php" class="submenu-link">Appointment</a>
                                </li>

                                <li class="submenu-item">
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
                    <div class="page-title">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>Appointments</h3>
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
                            <!-- LEFT: Appointment Request Form -->
                            <div class="col-lg-4 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Request Appointment</h4>
                                    </div>
                                    <div class="card-body">
                                        <form class="form" method="POST" action="" data-parsley-validate>
                                            <div class="modal-body">
                                                <!-- Date & Time -->
                                                <div class="form-group mandatory mb-3">
                                                    <label for="datetime" class="form-label">Date & Time</label>
                                                    <input type="datetime-local"
                                                        id="datetime"
                                                        name="requested_at"
                                                        class="form-control"
                                                        data-parsley-required="true"
                                                        required />
                                                </div>

                                                <!-- Counselor -->
                                                <div class="form-group mandatory mb-3">
                                                    <label for="counselor" class="form-label">Counselor</label>
                                                    <select id="counselor"
                                                        name="counselor"
                                                        class="form-select"
                                                        data-parsley-required="true"
                                                        required>
                                                        <option value="">-- Select Counselor --</option>
                                                        <?php foreach ($counselors as $counselor): ?>
                                                            <option value="<?= htmlspecialchars($counselor['id']) ?>">
                                                                <?= htmlspecialchars($counselor['fullname']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>


                                                <!-- Appointment Type -->
                                                <div class="form-group mandatory mb-3">
                                                    <label for="appointment_type" class="form-label">Appointment type</label>
                                                    <select id="appointment_type"
                                                        name="appointment_type"
                                                        class="form-select"
                                                        data-parsley-required="true"
                                                        required>
                                                        <option value="">-- Select Type --</option>
                                                        <option value="online">Online</option>
                                                        <option value="f2f">Face to face</option>
                                                    </select>
                                                </div>

                                                <!-- Remarks -->
                                                <div class="form-group mandatory mb-3">
                                                    <label for="remarks" class="form-label">Why need counselling?</label>
                                                    <textarea id="remarks"
                                                        name="remarks"
                                                        class="form-control"
                                                        rows="3"
                                                        data-parsley-required="true"
                                                        required></textarea>
                                                </div>

                                                <!-- Action Buttons -->
                                                <div class="row mt-4">
                                                    <div class="col-12 d-flex justify-content-end">
                                                        <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>

                            <!-- RIGHT: Appointment Table -->
                            <div class="col-lg-8 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>My Appointments</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
  <?php date_default_timezone_set('Asia/Manila'); ?>

<table class="table table-striped" id="table1">
    <thead>
        <tr>
            <th>Scheduled Date & Time</th>
            <th>Type</th>
            <th>Counselor</th>
            <th>Remarks</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($appointments as $appointment): ?>
            <?php
            // Prepare data
            $status = strtolower($appointment['status']);
            $type = strtolower($appointment['type']);
            $scheduled = new DateTime($appointment['scheduled_date'], new DateTimeZone('Asia/Manila'));
            $now = new DateTime('now', new DateTimeZone('Asia/Manila'));

            // Badge color
            switch ($status) {
                case 'pending': $badgeClass = 'bg-warning'; break;
                case 'rejected': $badgeClass = 'bg-danger'; break;
                case 'scheduled': $badgeClass = 'bg-info'; break;
                case 'completed': $badgeClass = 'bg-success'; break;
                default: $badgeClass = 'bg-secondary';
            }
            ?>
            
            <tr>
                <!-- 🗓 Scheduled Date -->
                <td><?= htmlspecialchars($scheduled->format('M d, Y - g:ia')) ?></td>

                <!-- 📝 Type -->
                <td>
                    <span style="color: green;">
                        <?= $type === 'online' ? 'Online' : 'Face-to-Face' ?>
                    </span>
                </td>

                <!-- 👨‍🏫 Counselor -->
                <td><?= htmlspecialchars($appointment['counselor_name']) ?></td>

                <!-- 💬 Remarks -->
                <td><?= htmlspecialchars($appointment['student_remarks']) ?></td>

                <!-- 🏷 Status -->
                <td>
                    <span class="badge <?= $badgeClass ?>">
                        <?= htmlspecialchars(ucfirst($appointment['status'])) ?>
                    </span>
                </td>

                <!-- ⚙️ Action -->
                <td>
                    <?php if ($status === 'pending'): ?>
                        <!-- Cancel Button -->
                        <a href="cancel_appointment.php?id=<?= $appointment['id'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure you want to cancel this appointment?');">
                           Cancel
                        </a>

                    <?php elseif ($status === 'rejected'): ?>
                        <!-- Rejected Modal -->
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#remarksModal<?= $appointment['id'] ?>">
                            View Counselor Remarks
                        </button>

                        <div class="modal fade" id="remarksModal<?= $appointment['id'] ?>" tabindex="-1" aria-labelledby="remarksLabel<?= $appointment['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header text-white">
                                        <h5 class="modal-title" id="remarksLabel<?= $appointment['id'] ?>">Counselor Remarks</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="text-secondary">
                                            <?= htmlspecialchars($appointment['admin_remarks'] ?? 'No remarks provided.') ?>
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <form method="POST" action="delete_appointment.php">
                                            <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>">
                                            <button type="submit" class="btn btn-danger"
                                                onclick="return confirm('Delete this appointment?');">
                                                Delete
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php elseif ($status === 'scheduled' || $status === 'completed'): ?>
                        <!-- View Schedule Modal -->
                        <button class="btn btn-sm btn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#scheduleModal<?= $appointment['id'] ?>">
                            View Schedule
                        </button>

                        <div class="modal fade" id="scheduleModal<?= $appointment['id'] ?>" tabindex="-1" aria-labelledby="scheduleLabel<?= $appointment['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- Modal Header -->
                                    <div class="modal-header text-white">
                                        <h5 class="modal-title" id="scheduleLabel<?= $appointment['id'] ?>">Appointment Details</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <strong>📅 Scheduled Date & Time:</strong><br>
                                            <p class="text-muted">
                                                <?= $scheduled->format('F d, Y - g:i A') ?><br>
                                                <span style="color: green;">Approved by <?= htmlspecialchars($appointment['counselor_name']) ?></span>
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <strong>📝 Type:</strong><br>
                                            <p class="text-muted">
                                                <?= $type === 'online' ? 'Online Counseling' : 'Face-to-Face Counseling' ?>
                                            </p>
                                        </div>

                                        <div class="mb-3">
                                            <strong>👨‍🏫 Counselor:</strong><br>
                                            <p class="text-muted"><?= htmlspecialchars($appointment['counselor_name']) ?></p>
                                        </div>

                                        <div class="mb-3">
                                            <strong>🗨️ Your Remarks:</strong><br>
                                            <p class="text-muted"><?= htmlspecialchars($appointment['student_remarks']) ?></p>
                                        </div>

                                        <?php if ($type === 'online'): ?>
    <?php
    // Get chat session status (latest)
    $stmt = $conn->prepare("SELECT chat_status FROM tbl_chat_sessions WHERE appointment_id = ? ORDER BY sent_at DESC LIMIT 1");
    $stmt->execute([$appointment['id']]);
    $last_chat = $stmt->fetch();
    ?>

    <div class="alert alert-primary text-center mt-4">
        <?php if ($now >= $scheduled): ?>
            <?php
            // 🧠 Logic breakdown:
            // 1. If chat exists and status is active → Chat ongoing
            // 2. If chat exists and status ended → Session completed
            // 3. If no chat exists yet:
            //    - If status is completed → show “please wait for counselor to start”
            //    - Otherwise → normal “upcoming” message
            ?>
            <?php if ($last_chat && $last_chat['chat_status'] === 'active'): ?>
                <strong>💬 Chat session is ongoing</strong><br>
                <a href="user_session_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-success mt-2">Enter Chat</a>
            <?php elseif ($last_chat && $last_chat['chat_status'] === 'ended'): ?>
                <strong>✅ Your online session is completed</strong><br>
                <a href="user_session_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-secondary mt-2">Check History</a>
            <?php else: ?>
                <?php if ($status === 'completed'): ?>
                    <strong>💬 Your online session is now ready.</strong><br>
                    Please wait for the counselor to start.<br>
                    <a href="user_session_appointment.php?id=<?= $appointment['id'] ?>" class="btn btn-sm btn-success mt-2">Go to Session</a>
                <?php else: ?>
                    <strong>⏳ Your online counseling session is now.</strong><br>
                    Please wait for the counselor to start the session.
                <?php endif; ?>
            <?php endif; ?>
        <?php else: ?>
            <strong>⏳ Your online counseling session is upcoming.</strong><br>
            Please return at the scheduled time and wait for your counselor to start.
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="alert alert-primary text-center mt-4">
        <?php if ($status === 'scheduled'): ?>
            <strong>⏳ Please go to the COPWELL Office on your scheduled date and time.</strong><br>
            <span>Your counselor will assist you upon arrival.</span>
        <?php elseif ($status === 'completed'): ?>
            <strong>✅ Your Face-to-Face appointment is completed.</strong>
        <?php else: ?>
            <strong>ℹ️ Your Face-to-Face appointment status is <?= htmlspecialchars($status) ?>.</strong>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const datetimeInput = document.getElementById('datetime');

            const errorMsg = document.createElement('div');
            errorMsg.classList.add('text-danger', 'mt-1');
            errorMsg.style.fontSize = '0.875rem';
            datetimeInput.parentNode.appendChild(errorMsg);

            form.addEventListener('submit', function(e) {
                const selectedDateTime = new Date(datetimeInput.value);
                if (!datetimeInput.value) return;

                const options = {
                    timeZone: 'Asia/Manila',
                    hour12: false,
                    hour: '2-digit',
                    minute: '2-digit'
                };
                const formatter = new Intl.DateTimeFormat('en-PH', options);
                const parts = formatter.formatToParts(selectedDateTime);

                const hour = parseInt(parts.find(p => p.type === 'hour').value);
                const minute = parseInt(parts.find(p => p.type === 'minute').value);

                const isValidTime = (hour > 8 || (hour === 8 && minute >= 0)) &&
                    (hour < 17 || (hour === 17 && minute === 0));

                if (!isValidTime) {
                    e.preventDefault();
                    errorMsg.textContent = 'Schedule time must be between 8:00 AM - 5:00 PM.';
                    datetimeInput.classList.add('is-invalid');
                } else {
                    errorMsg.textContent = '';
                    datetimeInput.classList.remove('is-invalid');
                }
            });
        });
    </script>


</body>

</html>