<?php
include '../database/connection.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $category_id = $_POST['category_id'] ?? null;
    $question = $_POST['question'] ?? '';

    if ($id && $category_id && $question) {
        $stmt = $conn->prepare("UPDATE tbl_questions SET category_id = ?, question = ? WHERE id = ?");
        $stmt->execute([$category_id, $question, $id]);
        $_SESSION['success'] = "Question updated successfully.";
    }else {
        $_SESSION['error'] = "Invalid data.";
    }
}

header("Location: questions_management.php");
exit();
