<?php
session_start();
require_once 'includes/db_connect.php';

// เช็กว่าล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reviewer_id = $_SESSION['user_id']; // ไอดีคนรีวิว (ตัวเรา)
    $target_user_id = $_POST['target_user_id']; // ไอดีคนที่ถูกรีวิว
    $rating = $_POST['rating']; // คะแนนดาว
    $comment = trim($_POST['comment']); // ข้อความรีวิว

    // ป้องกันการรีวิวโปรไฟล์ตัวเอง
    if ($reviewer_id == $target_user_id) {
        echo "<script>alert('คุณไม่สามารถรีวิวโปรไฟล์ตัวเองได้ครับ!'); window.history.back();</script>";
        exit();
    }

    try {
        // ⭐️ แก้ไขชื่อคอลัมน์ให้ตรงกับฐานข้อมูลเป๊ะๆ
        // ใช้ post_id เก็บคนที่ถูกรีวิว และ user_id เก็บคนพิมพ์รีวิว
        $stmt = $conn->prepare("INSERT INTO reviews (post_id, user_id, rating, comment) VALUES (:post_id, :user_id, :rating, :comment)");
        
        $stmt->execute([
            ':post_id' => $target_user_id,
            ':user_id' => $reviewer_id,
            ':rating' => $rating,
            ':comment' => $comment
        ]);

        echo "<script>alert('ส่งรีวิวเรียบร้อยแล้ว!'); window.location.href='view_Profile.php?id=" . $target_user_id . "';</script>";
    } catch (PDOException $e) {
        // แอบใส่โค้ดให้มันโชว์ Error จริงๆ ออกมาด้วย เผื่อมีปัญหาอื่นจะได้รู้ทันที
        echo "<script>alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}
?>