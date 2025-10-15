<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $department_name = trim($_POST['department_name'] ?? '');

    if ($id <= 0 || $department_name === '') {
        $_SESSION['error'] = "Invalid input.";
        header('Location: department_management.php');
        exit();
    }

    $stmt = $conn->prepare("UPDATE tbl_department SET department_name = ?, updated_at = NOW() WHERE id = ?");
    $stmt->execute([$department_name, $id]);

    $_SESSION['success'] = "Department updated successfully.";
    header('Location: department_management.php');
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header('Location: department_management.php');
    exit();
}
