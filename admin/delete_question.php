<?php
include '../database/connection.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $conn->prepare("DELETE FROM tbl_questions WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['success'] = "Question updated successfully.";
} else {
    $_SESSION['error'] = "Invalid data.";
}

header("Location: questions_management.php");
exit();
