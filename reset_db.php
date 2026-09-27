<?php
require_once 'includes/db_connect.php';

try {
    // สั่งเคลียร์ข้อมูลทุกตารางให้สะอาดหมดจด
    $conn->exec("DELETE FROM comments");
    $conn->exec("DELETE FROM posts");
    $conn->exec("DELETE FROM users");
    
    // รีเซ็ตค่าไอดีให้กลับมาเริ่มที่ 1 ใหม่
    $conn->exec("DELETE FROM sqlite_sequence");

    echo "<h2 style='color: green; font-family: sans-serif; text-align: center; margin-top: 50px;'>
            ✨ เคลียร์ฐานข้อมูลสะอาดเอี่ยมเรียบร้อยแล้ว! ✨<br>
            <a href='index.php'>กลับไปหน้าแรกเพื่อเริ่มใช้งานใหม่</a>
          </h2>";
} catch (PDOException $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
}
?>
