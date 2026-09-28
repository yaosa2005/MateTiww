<?php
session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = ""; // 👈 ทริค: ใส่ค่าว่างให้ฐานข้อมูล โดยที่ผู้ใช้ไม่ต้องกรอก

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password
        ]);

        echo "<script>alert('สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบได้เลยครับ'); window.location.href='index.php';</script>";
        
    } catch (PDOException $e) {
        echo "<script>alert('เกิดข้อผิดพลาด: อาจมีอีเมลนี้ในระบบแล้ว'); window.history.back();</script>";
    }
}
?>