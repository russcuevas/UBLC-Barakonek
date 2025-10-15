<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;

    if (!$id) {
        $_SESSION['error'] = "Invalid student ID.";
        header("Location: student_management.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("DELETE FROM tbl_students WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Student deleted successfully.";
        } else {
            $_SESSION['error'] = "Student not found or already deleted.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting student: " . $e->getMessage();
    }

    header("Location: student_management.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: student_management.php");
    exit();
}
