<?php
session_start(); // Session ปัจจุบัน
session_destroy(); // ล้างข้อมูลที่จำไว้ว่าเราล็อกอินอยู่ออกทั้งหมด
header("Location: index.php");
exit();
?>
