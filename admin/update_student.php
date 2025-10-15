<?php
session_start();
include '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student_management.php");
    exit();
}

// Get form data
$id             = $_POST['id'] ?? null;
$fullname       = trim($_POST['fullname'] ?? '');
$student_no     = trim($_POST['student_no'] ?? '');
$email          = trim($_POST['email'] ?? '');
$phone_number   = trim($_POST['phone_number'] ?? '');
$gender         = $_POST['gender'] ?? '';
$department_id  = $_POST['department_id'] ?? null;
$course_id      = $_POST['course_id'] ?? null;
$year_level     = $_POST['year_level'] ?? '';

if (
    empty($id) || empty($fullname) || empty($student_no) || empty($email) ||
    empty($phone_number) || empty($gender) || empty($department_id) ||
    empty($course_id) || empty($year_level)
) {
    $_SESSION['error'] = "All fields are required.";
    header("Location: student_management.php");
    exit();
}

try {
    $stmt = $conn->prepare("SELECT id FROM tbl_students WHERE (email = ? OR student_no = ?) AND id != ?");
    $stmt->execute([$email, $student_no, $id]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Email or Student # already exists.";
        header("Location: student_management.php");
        exit();
    }

    $stmt = $conn->prepare("
        UPDATE tbl_students
        SET fullname = ?, student_no = ?, email = ?, phone_number = ?, gender = ?, department_id = ?, course_id = ?, year_level = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([
        $fullname, $student_no, $email, $phone_number, $gender,
        $department_id, $course_id, $year_level, $id
    ]);

    $_SESSION['success'] = "Student updated successfully.";
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header("Location: student_management.php");
exit();
