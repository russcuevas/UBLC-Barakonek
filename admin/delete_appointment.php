<?php
include '../database/connection.php';
session_start();

// ✅ Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// ✅ Check if appointment ID is provided
if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];

    try {
        // ✅ Verify appointment exists and belongs to the logged-in admin
        $stmtCheck = $conn->prepare("SELECT * FROM tbl_appointments WHERE id = ? AND admin_id = ?");
        $stmtCheck->execute([$appointment_id, $admin_id]);

        if ($stmtCheck->rowCount() === 0) {
            $_SESSION['error'] = "Appointment not found or not authorized to delete.";
            header("Location: appointments_management.php");
            exit();
        }

        // ✅ Delete appointment
        $stmtDelete = $conn->prepare("DELETE FROM tbl_appointments WHERE id = ? AND admin_id = ?");
        $stmtDelete->execute([$appointment_id, $admin_id]);

        if ($stmtDelete->rowCount() > 0) {
            $_SESSION['success'] = "Appointment deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete appointment.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

} else {
    $_SESSION['error'] = "No appointment ID provided.";
}

// ✅ Redirect back
header("Location: appointments_management.php");
exit();
?>
