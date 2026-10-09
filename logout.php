<?php
session_start(); // เข้าถึง Session ปัจจุบัน
session_destroy(); // ล้างข้อมูลที่จำไว้ว่าเราล็อกอินอยู่ออกทั้งหมด
header("Location: index.php"); // เด้งกลับไปหน้าล็อกอ
exit();
?>
