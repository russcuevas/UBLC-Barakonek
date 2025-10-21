<?php
// session with database connection
include '../database/connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$appointment_id = $_POST['appointment_id'] ?? null;

if (!$appointment_id) {
    die("Missing appointment ID.");
}

// Insert the chat ended message
$stmt = $conn->prepare("
    INSERT INTO tbl_chat_sessions (appointment_id, sender_id, message, sender_type, chat_status)
    VALUES (?, ?, ?, ?, 'ended')
");
$stmt->execute([
    $appointment_id,
    $_SESSION['admin_id'],
    '🔒 Chat session has been ended by the counselor.',
    'admin'
]);

// Update appointment status to 'completed'
$update = $conn->prepare("UPDATE tbl_appointments SET status = 'completed' WHERE id = ?");
$update->execute([$appointment_id]);

header("Location: session_appointment.php?id=$appointment_id");
exit;
?>
