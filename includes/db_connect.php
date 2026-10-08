<?php
$db_path = __DIR__ . '/../db/matetiww.db';

try {
    $conn = new PDO("sqlite:" . $db_path);
    
    // แจ้งเตือนถ้ามี Error
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $e->getMessage();
    exit();
}
?>
