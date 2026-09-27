<?php
session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // 1. ตรวจสอบว่ามีอีเมลนี้ในระบบหรือยัง
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    
    if ($stmt->rowCount() > 0) {
        // ถ้ามีอีเมลนี้อยู่แล้ว ให้เด้งกลับไปบอกว่าซ้ำ
        echo "<script>alert('อีเมลนี้ถูกใช้งาน, โปรดใช้อีเมลอื่น'); window.location='index.php';</script>";
    } else {
        // 2. ถ้ายังไม่มี ให้บันทึกข้อมูลสมาชิกใหม่ลงฐานข้อมูล
        $insert_stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
        if ($insert_stmt->execute([':name' => $name, ':email' => $email])) {
            echo "<script>alert('สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง'); window.location='index.php';</script>";
        }
    }
}
?>
