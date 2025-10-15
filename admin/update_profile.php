<?php
session_start();
include '../database/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$id = $_SESSION['admin_id'];
$fullname = $_POST['fullname'];
$email = $_POST['email'];
$phone_number = $_POST['phone_number'];
$gender = $_POST['gender'];
$password = $_POST['password'];

$profile_picture = $_SESSION['profile_picture'];

$old_profile_picture = $_SESSION['profile_picture'];
if (!empty($_FILES['profile_picture']['name'])) {
    $target_dir = "profile/";
    $filename = basename($_FILES["profile_picture"]["name"]);
    $target_file = $target_dir . time() . "_" . $filename;

    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        if ($old_profile_picture && file_exists($old_profile_picture) && $old_profile_picture !== 'profile/default.png') {
            unlink($old_profile_picture);
        }

        $profile_picture = $target_file;
    }
}

// Check if the email is used by another admin
$stmt = $conn->prepare("SELECT id FROM tbl_admin WHERE email = ? AND id != ?");
$stmt->execute([$email, $id]);
$existing = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    $_SESSION['error'] = "Email already in use by another admin.";
    header("Location: dashboard.php");
    exit();
}

// Update current admin
if (trim($password) !== '') {
    $stmt = $conn->prepare("UPDATE tbl_admin SET fullname = ?, email = ?, phone_number = ?, gender = ?, profile_picture = ?, password = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$fullname, $email, $phone_number, $gender, $profile_picture, $password, $id]);
} else {
    $stmt = $conn->prepare("UPDATE tbl_admin SET fullname = ?, email = ?, phone_number = ?, gender = ?, profile_picture = ?, updated_at = NOW() WHERE id = ?");
    $result = $stmt->execute([$fullname, $email, $phone_number, $gender, $profile_picture, $id]);
}


if ($result) {
    $_SESSION['fullname'] = $fullname;
    $_SESSION['email'] = $email;
    $_SESSION['phone_number'] = $phone_number;
    $_SESSION['gender'] = $gender;
    $_SESSION['profile_picture'] = $profile_picture;
    $_SESSION['updated_at'] = date("Y-m-d H:i:s");

    $_SESSION['success'] = "Profile updated successfully.";
} else {
    $_SESSION['error'] = "Failed to update profile.";
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
