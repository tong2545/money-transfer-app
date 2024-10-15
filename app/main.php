<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // ถ้าผู้ใช้เข้าสู่ระบบแล้ว จะส่งไปหน้าแดชบอร์ด
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าเริ่มต้น - Money Transfer App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        h1 {
            color: #2c3e50;
        }
        a {
            color: #3498db;
            text-decoration: none;
            font-size: 18px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>ยินดีต้อนรับสู่ Money Transfer App</h1>
    <a href="register.php">ลงทะเบียน</a> |
    <a href="login.php">เข้าสู่ระบบ</a>
</body>
</html>
