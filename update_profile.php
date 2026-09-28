<?php
session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profile_pic'])) {
    $new_pic = $_POST['profile_pic'];
    $user_id = $_SESSION['user_id'];

    // อัปเดตรูปใหม่ลงฐานข้อมูล
    $stmt = $conn->prepare("UPDATE users SET profile_pic = :profile_pic WHERE user_id = :user_id");
    $stmt->execute([
        ':profile_pic' => $new_pic,
        ':user_id' => $user_id
    ]);
}

// กลับไปหน้าโปรไฟล์
header("Location: profile.php");
exit();
?>
