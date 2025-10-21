<?php
include 'database/connection.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$appointment_id = $_POST['appointment_id'] ?? null;

if (!$appointment_id) {
    $_SESSION['error'] = 'Appointment ID is required.';
    header("Location: appointment.php");
    exit();
}

try {
    $stmtCheck = $conn->prepare("SELECT * FROM tbl_appointments WHERE id = ? AND student_id = ?");
    $stmtCheck->execute([$appointment_id, $student_id]);

    if ($stmtCheck->rowCount() === 0) {
        $_SESSION['error'] = "Appointment not found or not authorized.";
        header("Location: appointment.php");
        exit();
    }

    $stmtDelete = $conn->prepare("DELETE FROM tbl_appointments WHERE id = ? AND student_id = ?");
    $stmtDelete->execute([$appointment_id, $student_id]);

    if ($stmtDelete->rowCount() > 0) {
        $_SESSION['success'] = "Appointment deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete appointment.";
    }

} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header("Location: appointment.php");
exit();
?>
