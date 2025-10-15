<?php
include '../database/connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM tbl_categories WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Category deleted successfully.";
    } else {
        $_SESSION['error'] = "Category not found.";
    }
}

header("Location: categories_management.php");
exit();
