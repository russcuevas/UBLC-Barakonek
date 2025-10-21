<?php
include '../database/connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'] ?? '';
    $remarks = trim($_POST['remarks'] ?? '');
    $admin_id = $_SESSION['admin_id'];

    if (!empty($appointment_id) && !empty($remarks)) {
        $stmt = $conn->prepare("UPDATE tbl_appointments SET status = 'Rejected', admin_remarks = ? WHERE id = ? AND admin_id = ?");
        $stmt->execute([$remarks, $appointment_id, $admin_id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Appointment rejected successfully.";
        } else {
            $_SESSION['error'] = "Failed to reject appointment.";
        }
    } else {
        $_SESSION['error'] = "Remarks are required.";
    }
}
?>
