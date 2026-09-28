<?php
session_start();
require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // เช็กแค่ว่ามีอีเมลนี้ในระบบไหม (ไม่ต้องเช็กรหัสผ่าน)
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // ถ้าเจออีเมล ก็ให้ล็อกอินผ่านเลย
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: home.php");
        exit();
    } else {
        // ถ้าไม่เจอ
        echo "<script>alert('ไม่พบอีเมลนี้ในระบบ กรุณาสมัครสมาชิกก่อนครับ!'); window.history.back();</script>";
    }
}
?>