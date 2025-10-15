<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $department_id = $_POST['department_id'] ?? null;
    $course_name = trim($_POST['course_name'] ?? '');

    if (!$id || !$department_id || empty($course_name)) {
        $_SESSION['error'] = "Please fill all required fields.";
        header('Location: course_management.php');
        exit();
    }

    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_department WHERE id = ?");
    $stmt->execute([$department_id]);
    if ($stmt->fetchColumn() == 0) {
        $_SESSION['error'] = "Selected department does not exist.";
        header('Location: course_management.php');
        exit();
    }

    try {
        $stmt = $conn->prepare("UPDATE tbl_course SET department_id = ?, course_name = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$department_id, $course_name, $id]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Course updated successfully.";
        } else {
            $_SESSION['error'] = "No changes made or course not found.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating course: " . $e->getMessage();
    }

    header('Location: course_management.php');
    exit();
} else {
    $_SESSION['error'] = "Invalid request method.";
    header('Location: course_management.php');
    exit();
}
