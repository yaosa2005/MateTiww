<?php
session_start();
require_once 'includes/db_connect.php';

// เช็กการล็อกอิน
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ถ้ามีการส่งข้อมูลเปลี่ยนรูปโปรไฟล์มา
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profile_pic'])) {
    $user_id = $_SESSION['user_id'];
    $profile_pic = $_POST['profile_pic'];

    try {
        // อัปเดตรูปโปรไฟล์ในตาราง users
        $stmt = $conn->prepare("UPDATE users SET profile_pic = :profile_pic WHERE user_id = :user_id");
        $stmt->execute([
            ':profile_pic' => $profile_pic,
            ':user_id' => $user_id
        ]);

        echo "<script>alert('เปลี่ยนรูปโปรไฟล์เรียบร้อยแล้ว!'); window.location.href='profile.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "'); window.history.back();</script>";
        exit();
    }
} else {
    // ถ้าเข้ามาหน้านี้ตรงๆ โดยไม่กดปุ่ม ให้เด้งกลับ
    header("Location: profile.php");
    exit();
}
?>
