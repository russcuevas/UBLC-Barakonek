<?php
include '../database/connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];
    $admin_id = $_SESSION['admin_id'];

    $admin_remarks = "Appointment confirmed.";
    $stmt = $conn->prepare("
        UPDATE tbl_appointments 
        SET status = 'Scheduled', admin_remarks = ? 
        WHERE id = ? AND admin_id = ?
    ");
    $stmt->execute([$admin_remarks, $appointment_id, $admin_id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Appointment approved successfully.";
    } else {
        $_SESSION['error'] = "Unable to approve appointment.";
    }
}

header("Location: appointments_management.php");
exit();
?>
