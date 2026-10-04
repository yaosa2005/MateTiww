<?php
session_start();
require_once 'includes/db_connect.php';

// เช็กว่าล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ตรวจสอบสิทธิ์ Admin จากฐานข้อมูลแบบสดๆ เพื่อความแม่นยำสูงสุด
$stmt_role = $conn->prepare("SELECT role FROM users WHERE user_id = :user_id");
$stmt_role->execute([':user_id' => $user_id]);
$user_info = $stmt_role->fetch(PDO::FETCH_ASSOC);
$role = $user_info ? $user_info['role'] : 'user';

// เช็กว่ามีการส่ง post_id มาหรือไม่
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];

    if ($role === 'admin') {
        // ถ้าเป็นแอดมิน: สิทธิ์ขาดลบได้ทุกโพสต์ทันที
        $del_post = $conn->prepare("DELETE FROM posts WHERE post_id = :post_id");
        $del_post->execute([':post_id' => $post_id]);
    } else {
        // ถ้าเป็นผู้ใช้ทั่วไป: ลบได้เฉพาะโพสต์ของตัวเองเท่านั้น
        $del_post = $conn->prepare("DELETE FROM posts WHERE post_id = :post_id AND user_id = :user_id");
        $del_post->execute([
            ':post_id' => $post_id,
            ':user_id' => $user_id
        ]);
    }

    // ถ้าลบโพสต์สำเร็จ ให้ลบคอมเมนต์ที่เกี่ยวข้องทิ้งด้วย
    if ($del_post->rowCount() > 0) {
        $del_comment = $conn->prepare("DELETE FROM comments WHERE post_id = :post_id");
        $del_comment->execute([':post_id' => $post_id]);
    }
}

// พาเด้งกลับไปหน้าที่เหมาะสมตามสถานะ
if ($role === 'admin') {
    header("Location: admin_dashboard.php");
} else {
    header("Location: home.php");
}
exit();
?>
