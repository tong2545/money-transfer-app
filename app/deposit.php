<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = $_POST['amount'];
    $user_id = $_SESSION['user_id'];

    // เพิ่มเงินในยอดเงิน
    $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->bind_param("di", $amount, $user_id);
    $stmt->execute();

    // บันทึกธุรกรรม
    $stmt_transaction = $conn->prepare("INSERT INTO transactions (sender_id, amount, transaction_type) VALUES (?, ?, 'deposit')");
    $stmt_transaction->bind_param("id", $user_id, $amount);
    $stmt_transaction->execute();
    $stmt_transaction->close();

    echo "<script>alert('ฝากเงินเรียบร้อย'); window.location.href='dashboard.php';</script>";

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ฝากเงิน - Money Transfer App</title>
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
        input[type="number"] {
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
        <h1>ฝากเงิน</h1>
        <form method="POST">
            <label for="amount">จำนวนเงิน:</label>
            <input type="number" id="amount" name="amount" required>
            <button type="submit">ฝากเงิน</button>
        </form>
    </div>
</body>
</html>
