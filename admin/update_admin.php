<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $fullname = $_POST['fullname'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    if (empty($id) || empty($fullname) || empty($gender) || empty($email) || empty($phone_number)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: admin_management.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("UPDATE tbl_admin SET fullname = ?, gender = ?, email = ?, phone_number = ? WHERE id = ?");
        $stmt->execute([$fullname, $gender, $email, $phone_number, $id]);

        $_SESSION['success'] = "Admin updated successfully.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Failed to update admin.";
    }

    header("Location: admin_management.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: admin_management.php");
    exit();
}
