<?php
session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // ค้นหาอีเมลในฐานข้อมูล
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // ถ้าเจออีเมล ให้บันทึก Session แล้วพาเข้าหน้า home.php ทันที
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        header("Location: home.php");
        exit();
    } else {
        echo "<script>alert('ไม่พบอีเมลนี้ในระบบ โปรดใช้อีเมลมหาวิทยาลัยเท่านั้น '); window.location='index.php';</script>";
    }
}
?>
