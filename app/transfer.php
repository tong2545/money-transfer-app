<?php
session_start();
include 'config.php'; // รวมไฟล์ config.php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $recipient_username = $_POST['recipient_username']; // รับชื่อผู้รับ
    $user_id = $_SESSION['user_id'];

    // ดึง ID ของผู้รับจากชื่อผู้ใช้
    $stmt_recipient = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt_recipient->bind_param("s", $recipient_username);
    $stmt_recipient->execute();
    $stmt_recipient->bind_result($recipient_id);
    $stmt_recipient->fetch();
    $stmt_recipient->close();

    // ตรวจสอบว่าผู้รับมีอยู่จริงหรือไม่
    if ($recipient_id === null) {
        echo "<script>alert('ไม่พบผู้รับที่ระบุ');</script>";
    } else {
        // ตรวจสอบยอดเงินของผู้ส่ง
        $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($sender_balance);
        $stmt->fetch();
        $stmt->close();

        if ($sender_balance >= $amount) {
            // ลดยอดเงินจากผู้ส่ง
            $stmt = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $stmt->bind_param("di", $amount, $user_id);
            $stmt->execute();

            // เพิ่มเงินให้ผู้รับ
            $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->bind_param("di", $amount, $recipient_id);
            $stmt->execute();

            // บันทึกประวัติการโอนเงินสำหรับผู้ส่ง
            $stmt_transaction_sender = $conn->prepare("INSERT INTO transactions (sender_id, recipient_id, amount, transaction_type) VALUES (?, ?, ?, 'transfer')");
            $stmt_transaction_sender->bind_param("iid", $user_id, $recipient_id, $amount);
            $stmt_transaction_sender->execute();
            $stmt_transaction_sender->close();

            // บันทึกประวัติการโอนเงินสำหรับผู้รับ
            $stmt_transaction_recipient = $conn->prepare("INSERT INTO transactions (sender_id, recipient_id, amount, transaction_type) VALUES (?, ?, ?, 'received')");
            $stmt_transaction_recipient->bind_param("iid", $recipient_id, $user_id, $amount);
            $stmt_transaction_recipient->execute();
            $stmt_transaction_recipient->close();

            echo "<script>alert('โอนเงินเรียบร้อย'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('ยอดเงินไม่เพียงพอ');</script>";
        }
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โอนเงิน - Money Transfer App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>โอนเงิน</h1>
        <form method="POST">
            <label for="recipient_username">ชื่อผู้รับ:</label>
            <input type="text" id="recipient_username" name="recipient_username" required>
            <label for="amount">จำนวนเงิน:</label>
            <input type="number" id="amount" name="amount" required>
            <button type="submit">โอนเงิน</button>
        </form>
    </div>
</body>
</html>
