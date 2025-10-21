<?php
// Include database connection
include 'database/connection.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
$appointment_id = $_GET['id'] ?? null;  // Get the appointment ID from the query string

if (!$appointment_id) {
    $_SESSION['error'] = 'Appointment ID is required.';
    header("Location: appointment.php");
    exit();
}

// Check if the appointment exists and belongs to the logged-in student
$stmt = $conn->prepare("SELECT * FROM tbl_appointments WHERE id = ? AND student_id = ?");
$stmt->execute([$appointment_id, $student_id]);
$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    $_SESSION['error'] = 'Appointment not found or you do not have permission to cancel this appointment.';
    header("Location: appointment.php");
    exit();
}

// Only allow cancellation if the appointment is "Pending"
if ($appointment['status'] != 'Pending') {
    $_SESSION['error'] = 'You can only cancel appointments that are in "Pending" status.';
    header("Location: appointment.php");
    exit();
}

// Delete the appointment from the database
$stmt = $conn->prepare("DELETE FROM tbl_appointments WHERE id = ?");
$stmt->execute([$appointment_id]);

// Check if the delete operation was successful
if ($stmt->rowCount() > 0) {
    $_SESSION['success'] = 'You cancel your appointment';
} else {
    $_SESSION['error'] = 'Failed to cancel your appointment. Please try again.';
}

header("Location: appointment.php"); 
exit();
?>
