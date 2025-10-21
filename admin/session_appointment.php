<?php
include '../database/connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$appointment_id = $_GET['id'] ?? null;

if (!$appointment_id) {
    die("Invalid appointment ID.");
}

// ✅ Get appointment info
$stmt = $conn->prepare("SELECT a.*, s.fullname AS student_name 
                        FROM tbl_appointments a 
                        JOIN tbl_students s ON a.student_id = s.id 
                        WHERE a.id = ?");
$stmt->execute([$appointment_id]);
$appointment = $stmt->fetch();

if (!$appointment) {
    die("Appointment not found.");
}

// ✅ Get last chat status
$stmt = $conn->prepare("SELECT chat_status FROM tbl_chat_sessions WHERE appointment_id = ? ORDER BY sent_at DESC LIMIT 1");
$stmt->execute([$appointment_id]);
$last_status = $stmt->fetchColumn();



?>

<!DOCTYPE html>
<html>

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

<body class="container py-5">

    <h2>🧑‍💬 COUNSELING SESSION</h2>
    <div class="mb-3">
        <a href="appointments_management.php" class="btn btn-outline-secondary">
            ⬅️ Go Back
        </a>
    </div>
    <p><strong>Student:</strong> <?= htmlspecialchars($appointment['student_name']) ?></p>

    <!-- 🗨️ Chat Box -->
    <div id="chatBox" class="border p-3 mb-3" style="height: 500px; overflow-y: scroll; background: #f9f9f9;">
        <!-- Messages will load here via AJAX -->
    </div>

    <?php if ($last_status !== 'ended'): ?>
        <!-- ✅ Send Message Form -->
        <form id="chatForm" class="mt-3">
            <input type="hidden" name="appointment_id" value="<?= $appointment_id ?>">
            <input type="hidden" name="sender_id" value="<?= $_SESSION['admin_id'] ?>">
            <input type="hidden" name="sender_type" value="admin">
            <textarea name="message" class="form-control" placeholder="Type your message..." required></textarea>
            <button type="submit" class="btn btn-primary mt-2">Send Message</button>
        </form>

        <!-- 🔚 End Chat Button -->
        <form method="POST" action="end_chat.php" class="mt-2">
            <input type="hidden" name="appointment_id" value="<?= $appointment_id ?>">
            <button type="submit" class="btn btn-danger">End Chat</button>
        </form>
    <?php else: ?>
        <!-- ⚠️ Chat Ended -->
        <div class="alert alert-danger mt-3 text-center">
            <strong>⚠️ This chat session has ended. You can view the message history but can no longer send messages.</strong>
        </div>
    <?php endif; ?>

    <script src="assets/static/js/components/dark.js"></script>
    <script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/compiled/js/app.js"></script>
    <script src="assets/extensions/jquery/jquery.min.js"></script>
    <script src="assets/extensions/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/extensions/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/static/js/pages/datatables.js"></script>
    <script src="assets/extensions/parsleyjs/parsley.min.js"></script>
    <script src="assets/static/js/pages/parsley.js"></script>
    <script>
        const chatBox = document.getElementById('chatBox');
        const appointmentId = "<?= $appointment_id ?>";

        function loadMessages() {
            // Check if the user is already near the bottom before loading new messages
            const isNearBottom = chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight < 100;

            fetch('load_messages.php?appointment_id=' + appointmentId)
                .then(res => res.text())
                .then(data => {
                    chatBox.innerHTML = data;

                    // Only auto-scroll if the user was already near the bottom
                    if (isNearBottom) {
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }

                    // ✅ Check chat status
                    const statusDiv = chatBox.querySelector('#chatStatus');
                    const status = statusDiv?.dataset.status || 'active';
                    const form = document.getElementById('chatForm');
                    const existingAlert = document.querySelector('.alert-danger');

                    if (status === 'ended') {
                        if (form) form.remove();
                        if (!existingAlert) {
                            const alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-danger text-center mt-3';
                            alertDiv.innerHTML = `
                        <strong>⚠️ This chat session has ended. You can view messages but can no longer send.</strong>
                    `;
                            document.body.appendChild(alertDiv);
                        }
                    }
                });
        }

        // Auto-refresh every 1 second
        setInterval(loadMessages, 1000);
        loadMessages();

        // 📤 Send message via AJAX
        document.getElementById('chatForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('send_message.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                this.reset();
                loadMessages();
            });
        });
    </script>

</body>

</html>