<?php
// กำหนดที่อยู่ของไฟล์ฐานข้อมูล
$db_path = __DIR__ . '/../db/matetiww.db';

try {
    // เชื่อมต่อฐานข้อมูล
    $conn = new PDO("sqlite:" . $db_path);
    // ตแจ้งเตือนถ้ามี Error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
    exit();
}
?>
