<?php
include '../database/connection.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $category_name = trim($_POST['category_name'] ?? '');

    if ($id && $category_name) {
        $stmt = $conn->prepare("UPDATE tbl_categories SET category_name = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$category_name, $id]);
        $_SESSION['success'] = "Category updated successfully.";
    } else {
        $_SESSION['error'] = "Invalid data.";
    }
}

header("Location: categories_management.php");
exit();
