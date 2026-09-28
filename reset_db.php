<?php
require_once 'includes/db_connect.php';

try {
    // ลบตารางเก่าทิ้งทั้งหมดเพื่อเริ่มใหม่
    $conn->exec("DROP TABLE IF EXISTS comments");
    $conn->exec("DROP TABLE IF EXISTS posts");
    $conn->exec("DROP TABLE IF EXISTS users");

    // 1. สร้างตาราง users (เพิ่มคอลัมน์ profile_pic เข้าไปเลยตั้งแต่แรก)
    $conn->exec("CREATE TABLE users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        profile_pic TEXT DEFAULT 'https://api.dicebear.com/7.x/adventurer/svg?seed=Felix'
    )");

    // 2. สร้างตาราง posts (ใช้ฟิลด์ detail และตั้งค่าเวลาเบื้องต้น)
    $conn->exec("CREATE TABLE posts (
        post_id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        category TEXT NOT NULL,
        title TEXT NOT NULL,
        detail TEXT NOT NULL,
        status TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )");

    // 3. สร้างตาราง comments (ความคิดเห็น)
    $conn->exec("CREATE TABLE comments (
        comment_id INTEGER PRIMARY KEY AUTOINCREMENT,
        post_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(post_id),
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )");

    echo "<script>alert('สร้างฐานข้อมูลใหม่พร้อมระบบรูปโปรไฟล์เรียบร้อยแล้ว!'); window.location.href='index.php';</script>";

} catch (PDOException $e) {
    echo "เกิดข้อผิดพลาดในการสร้างฐานข้อมูล: " . $e->getMessage();
}
?>
