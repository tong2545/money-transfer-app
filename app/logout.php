<?php
// เริ่มเซสชัน
session_start();

// ทำลายเซสชันทั้งหมด
session_destroy();

// รีไดเรกต์ผู้ใช้ไปยังหน้า login.php
header("Location: main.php");
exit();
?>
