<?php
session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $raw_password = $_POST['password']; // รับรหัสผ่านที่ผู้ใช้กรอก

    // เข้ารหัสรหัสผ่านก่อนบันทึก
    $hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, 'user')");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashed_password
        ]);

        echo "<script>alert('สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบได้เลยครับ'); window.location.href='index.php';</script>";
        
    } catch (PDOException $e) {
        echo "<script>alert('เกิดข้อผิดพลาด: อาจมีอีเมลนี้ในระบบแล้ว'); window.history.back();</script>";
    }
}
?>
