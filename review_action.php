<?php
session_start();
require_once 'includes/db_connect.php';

// ตรวจสอบว่าล็อกอินแล้วหรือยัง
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// ตรวจสอบว่ามีการส่งข้อมูลแบบ POST มาหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewer_id = $_SESSION['user_id'];
    $target_user_id = $_POST['target_user_id'] ?? null;
    $rating = $_POST['rating'] ?? null;
    $comment = $_POST['comment'] ?? '';

    // ตรวจสอบข้อมูลว่าครบถ้วนหรือไม่
    if (!$target_user_id || !$rating || empty($comment)) {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
        exit();
    }

    // ป้องกันการรีวิวตัวเอง
    if ($reviewer_id == $target_user_id) {
        echo "<script>alert('ไม่สามารถรีวิวตัวเองได้'); window.history.back();</script>";
        exit();
    }

    try {
        // คำสั่งบันทึกข้อมูลลงฐานข้อมูล
        $stmt = $conn->prepare("INSERT INTO reviews (target_user_id, reviewer_id, rating, comment) VALUES (:target_user_id, :reviewer_id, :rating, :comment)");
        
        $stmt->execute([
            ':target_user_id' => $target_user_id,
            ':reviewer_id' => $reviewer_id,
            ':rating' => $rating,
            ':comment' => $comment
        ]);

        // อัปเดตโค้ดแจ้งเตือนและเด้งกลับให้ชัดเจน ป้องกันลิงก์พัง
        echo "<script>";
        echo "alert('ส่งรีวิวเรียบร้อยแล้ว!');";
        echo "window.location.href = 'view_Profile.php?id={$target_user_id}';";
        echo "</script>";
        exit();

    } catch (PDOException $e) {
        // ดึง Error จากฐานข้อมูลมาแปลงให้ปลอดภัยก่อนแสดงผลใน JavaScript
        $errorMsg = addslashes($e->getMessage());
        
        echo "<script>";
        echo "alert('เกิดข้อผิดพลาดจากฐานข้อมูล: {$errorMsg}');";
        echo "window.history.back();";
        echo "</script>";
        exit();
    }
} else {
    // ถ้าไม่ได้เข้าหน้านี้ด้วยการส่งฟอร์ม (POST) ให้กลับไปหน้าแรก
    header("Location: home.php");
    exit();
}
?>

