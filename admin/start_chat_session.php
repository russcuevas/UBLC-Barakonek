<?php
include '../database/connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$appointment_id = $_GET['id'] ?? null;

if (!$appointment_id) {
    die("Missing appointment ID.");
}

$stmt = $conn->prepare("SELECT chat_status FROM tbl_chat_sessions WHERE appointment_id = ? ORDER BY sent_at DESC LIMIT 1");
$stmt->execute([$appointment_id]);
$last_chat = $stmt->fetch();

if (!$last_chat || $last_chat['chat_status'] !== 'ended') {
    $stmt = $conn->prepare("
        INSERT INTO tbl_chat_sessions (appointment_id, sender_id, message, sender_type, chat_status)
        VALUES (?, ?, ?, ?, 'active')
    ");
    $stmt->execute([
        $appointment_id,
        $_SESSION['admin_id'],
        'Hi',
        'admin'
    ]);
}

header("Location: session_appointment.php?id=$appointment_id");
exit;
?>
