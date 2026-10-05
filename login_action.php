<?php
session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบความถูกต้องของอีเมลและรหัสผ่านที่แฮชไว้
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role']; 
        
        // แยกสิทธิ์ของผู้ใช้งาน
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: home.php");
        }
        exit();
    } else {
        echo "<script>alert('อีเมลหรือรหัสผ่านไม่ถูกต้อง!'); window.history.back();</script>";
    }
}
?>
