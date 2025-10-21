<?php
include 'database/connection.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$appointment_id = $_POST['appointment_id'] ?? null;
$sender_id = $_POST['sender_id'] ?? null;
$sender_type = $_POST['sender_type'] ?? 'student';
$message = trim($_POST['message']);

if (!$appointment_id || empty($message)) {
    header("Location: user_session_appointment.php?id=$appointment_id&error=empty");
    exit();
}

// ✅ Save message with chat_status = 'active'
$stmt = $conn->prepare("
    INSERT INTO tbl_chat_sessions 
    (appointment_id, sender_id, message, sender_type, chat_status)
    VALUES (?, ?, ?, ?, 'active')
");
$stmt->execute([$appointment_id, $sender_id, $message, $sender_type]);

header("Location: user_session_appointment.php?id=$appointment_id");
exit;
?>
