<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['error'] = "Invalid department ID.";
        header('Location: department_management.php');
        exit();
    }

    $stmt = $conn->prepare("DELETE FROM tbl_department WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Department deleted successfully.";
    } else {
        $_SESSION['error'] = "Department not found or already deleted.";
    }

    header('Location: department_management.php');
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header('Location: department_management.php');
    exit();
}
