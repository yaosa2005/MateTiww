<?php
session_start();
require_once 'includes/db_connect.php';

// เช็กว่ามีการส่งข้อมูลแบบ POST มา และได้เลือกรูปมาจริงๆ (ไม่เป็นค่าว่าง)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['profile_pic']) && !empty($_POST['profile_pic'])) {
    try {
        // อัปเดตข้อมูลรูปภาพในฐานข้อมูล
        $stmt = $conn->prepare("UPDATE users SET profile_pic = :profile_pic WHERE user_id = :user_id");
        $stmt->execute([
            ':profile_pic' => $_POST['profile_pic'],
            ':user_id' => $_SESSION['user_id']
        ]);
    } catch (PDOException $e) {
        // เผื่อมี Error จะได้แจ้งเตือนให้รู้
        echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกรูป: " . $e->getMessage() . "');</script>";
    }
}

// บันทึกเสร็จแล้วให้เด้งกลับไปหน้าโปรไฟล์อัตโนมัติ
header("Location: profile.php");
exit();
?>
