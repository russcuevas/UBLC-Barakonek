<?php
include '../database/connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$appointment_id = $_POST['appointment_id'];
$sender_id = $_POST['sender_id'];
$sender_type = $_POST['sender_type'];
$message = trim($_POST['message']);

if (empty($message)) {
    header("Location: session_appointment.php?id=$appointment_id&error=empty");
    exit();
}

// ✅ Save message with chat_status = 'active'
$stmt = $conn->prepare("
    INSERT INTO tbl_chat_sessions 
    (appointment_id, sender_id, message, sender_type, chat_status)
    VALUES (?, ?, ?, ?, 'active')
");
$stmt->execute([$appointment_id, $sender_id, $message, $sender_type]);

header("Location: session_appointment.php?id=$appointment_id");
exit;
?>
