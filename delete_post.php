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

    // 1. ลบความคิดเห็นทั้งหมดที่ผูกกับโพสต์นี้ก่อน
    $del_comment = $conn->prepare("DELETE FROM comments WHERE post_id = :post_id");
    $del_comment->execute([':post_id' => $post_id]);

    // 2. ลบตัวโพสต์ทันที
    $del_post = $conn->prepare("DELETE FROM posts WHERE post_id = :post_id");
    $del_post->execute([':post_id' => $post_id]);
}

// เด้งกลับมาหน้าหลักทันที
header("Location: home.php");
exit();
?>
