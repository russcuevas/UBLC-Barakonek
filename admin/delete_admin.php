<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        $_SESSION['error'] = "Invalid admin ID.";
        header("Location: admin_management.php");
        exit();
    }

    // Prevent deleting yourself
    if ($_SESSION['admin_id'] == $id) {
        $_SESSION['error'] = "You cannot delete your own account.";
        header("Location: admin_management.php");
        exit();
    }

    try {
        // 1. Get the profile picture path
        $stmt = $conn->prepare("SELECT profile_picture FROM tbl_admin WHERE id = ?");
        $stmt->execute([$id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin) {
            $_SESSION['error'] = "Admin not found.";
            header("Location: admin_management.php");
            exit();
        }

        // 2. Delete profile picture file if exists
        if (!empty($admin['profile_picture'])) {
            $filePath = $admin['profile_picture'];  // e.g. "profile/admin_abc.jpg"
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }


        // 3. Delete admin record
        $stmt = $conn->prepare("DELETE FROM tbl_admin WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Admin deleted successfully.";
        } else {
            $_SESSION['error'] = "Admin could not be deleted.";
        }

    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting admin.";
    }

    header("Location: admin_management.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: admin_management.php");
    exit();
}
