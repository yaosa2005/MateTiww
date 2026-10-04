<?php
require_once 'includes/db_connect.php';

try {
    // ลบตารางเก่าทิ้งทั้งหมดเพื่อเริ่มใหม่
    $conn->exec("DROP TABLE IF EXISTS reviews");
    $conn->exec("DROP TABLE IF EXISTS comments");
    $conn->exec("DROP TABLE IF EXISTS posts");
    $conn->exec("DROP TABLE IF EXISTS users");

    // 1. สร้างตาราง users (มีคอลัมน์ role และ password)
    $conn->exec("CREATE TABLE users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        profile_pic TEXT DEFAULT 'pic/pro1.jpg',
        role TEXT DEFAULT 'user' 
    )");

    // เข้ารหัสรหัสผ่านแอดมินเป็น admin1234
    $admin_password = password_hash('admin1234', PASSWORD_DEFAULT);

    // เพิ่มบัญชีแอดมินอัตโนมัติผ่าน SQL
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES ('Admin', 'admin@matetiww.com', :password, 'admin')");
    $stmt->execute([':password' => $admin_password]);

    // 2. สร้างตาราง posts
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

    // 3. สร้างตาราง comments
    $conn->exec("CREATE TABLE comments (
        comment_id INTEGER PRIMARY KEY AUTOINCREMENT,
        post_id INTEGER NOT NULL,
        user_id INTEGER NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(post_id),
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )");

    // 4. สร้างตาราง reviews
    $conn->exec("CREATE TABLE reviews (
        review_id INTEGER PRIMARY KEY AUTOINCREMENT,
        target_user_id INTEGER NOT NULL,
        reviewer_id INTEGER NOT NULL,
        rating INTEGER NOT NULL,
        comment TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (target_user_id) REFERENCES users(user_id),
        FOREIGN KEY (reviewer_id) REFERENCES users(user_id)
    )");

    echo "<script>alert('รีเซ็ตฐานข้อมูลและสร้างรหัสผ่านแอดมิน (admin1234) เรียบร้อยแล้ว!'); window.location.href='index.php';</script>";

} catch (PDOException $e) {
    echo "เกิดข้อผิดพลาดในการสร้างฐานข้อมูล: " . $e->getMessage();
}
?>
