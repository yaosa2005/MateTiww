<?php
session_start();
require_once 'includes/db_connect.php';

// เช็คความปลอดภัย
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'];
    $content = trim($_POST['content']);

    // ตรวจสอบว่าไม่ได้ส่งข้อความว่างๆ มา
    if (!empty($content) && !empty($post_id)) {
        try {
            $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (:post_id, :user_id, :content)");
            $stmt->execute([
                ':post_id' => $post_id,
                ':user_id' => $user_id,
                ':content' => $content
            ]);
        } catch(PDOException $e) {
            // โค้ดสำหรับจัดการ Error กรณีบันทึกไม่สำเร็จ
        }
    }
    
    // เด้งกลับไปหน้าหลัก
    header("Location: home.php");
    exit();
} else {
    header("Location: home.php");
    exit();
}
?>
