<?php
session_start();
require_once 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $category = trim($_POST['category']);
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    // บันทึกข้อมูลลงตาราง posts (ปรับให้ตรงกับโครงสร้างเดิมของคุณ)
    try {
        // แก้ตรงนี้: เปลี่ยนเป็น datetime('now', 'localtime')
        $stmt = $conn->prepare("INSERT INTO posts (user_id, category, title, content, created_at) VALUES (:user_id, :category, :title, :content, datetime('now', 'localtime'))");
        $stmt->execute([
            ':user_id' => $user_id,
            ':category' => $category,
            ':title' => $title,
            ':content' => $content
        ]);
    } catch (PDOException $e) {
        // แก้ตรงนี้ด้วย: เปลี่ยนเป็น datetime('now', 'localtime')
        $stmt = $conn->prepare("INSERT INTO posts (user_id, category, title, detail, created_at) VALUES (:user_id, :category, :title, :content, datetime('now', 'localtime'))");
        $stmt->execute([
            ':user_id' => $user_id,
            ':category' => $category,
            ':title' => $title,
            ':content' => $content
        ]);
    }
}

header("Location: home.php");
exit();
?>
