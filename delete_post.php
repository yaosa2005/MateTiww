<?php
session_start();
require_once 'includes/db_connect.php';

// เช็กว่าล็อกอินหรือยัง
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// เช็กว่ามีการส่ง post_id มาหรือไม่
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $user_id = $_SESSION['user_id']; // ดึงไอดีคนที่ล็อกอินอยู่

    // 1. ลบตัวโพสต์ โดยต้องตรงกับไอดีของคนสร้างโพสต์เท่านั้น (ป้องกันคนอื่นแอบลบ)
    $del_post = $conn->prepare("DELETE FROM posts WHERE post_id = :post_id AND user_id = :user_id");
    $del_post->execute([
        ':post_id' => $post_id,
        ':user_id' => $user_id
    ]);

    // 2. เช็กว่าลบโพสต์สำเร็จจริงๆ (ลบได้ > 0 แถว) ถึงจะไปลบคอมเมนต์
    // ป้องกันกรณีคนอื่นยิง URL มาลบโพสต์เรา ตัวโพสต์จะไม่ถูกลบ และคอมเมนต์ก็จะปลอดภัย
    if ($del_post->rowCount() > 0) {
        $del_comment = $conn->prepare("DELETE FROM comments WHERE post_id = :post_id");
        $del_comment->execute([':post_id' => $post_id]);
    }
}

// เด้งกลับมาหน้าหลักทันที
header("Location: home.php");
exit();
?>
