<?php
// 1. กำหนดตำแหน่งที่จะสร้างไฟล์ฐานข้อมูล (จะไปสร้างอยู่ในโฟลเดอร์ db ชื่อ matetiww.db)
$db_file = __DIR__ . '/../db/matetiww.db';

try {
    // 2. ใช้ PDO เชื่อมต่อกับ SQLite 
    $conn = new PDO("sqlite:" . $db_file);
    // ตั้งค่าให้แสดง Error หากมีข้อผิดพลาด จะได้แก้โค้ดง่ายๆ
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. สร้างตาราง users (เก็บข้อมูลผู้ใช้)
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT NOT NULL UNIQUE,
        name TEXT NOT NULL
    )");

    // 4. สร้างตาราง posts (เก็บโพสต์หาเพื่อนติว/รูมเมท)
    $conn->exec("CREATE TABLE IF NOT EXISTS posts (
        post_id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        category TEXT,
        title TEXT,
        detail TEXT,
        status TEXT DEFAULT 'open',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )");

    // 5. สร้างตาราง comments (เก็บข้อความคอมเมนต์)
    $conn->exec("CREATE TABLE IF NOT EXISTS comments (
        comment_id INTEGER PRIMARY KEY AUTOINCREMENT,
        post_id INTEGER,
        user_id INTEGER,
        content TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(post_id),
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )");

    // 6. สร้างตาราง reviews (เก็บรีวิว)
    $conn->exec("CREATE TABLE IF NOT EXISTS reviews (
        review_id INTEGER PRIMARY KEY AUTOINCREMENT,
        post_id INTEGER,
        user_id INTEGER,
        rating INTEGER,
        comment TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(post_id),
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )");

} catch(PDOException $e) {
    echo "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
}
?>
