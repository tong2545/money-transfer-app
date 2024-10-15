<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// แสดงข้อมูลผู้ใช้
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, balance FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $balance);
$stmt->fetch();
$stmt->close();

// ดึงประวัติธุรกรรม
$stmt = $conn->prepare("
    SELECT 
        t.amount,
        t.transaction_type,
        t.created_at,
        CASE 
            WHEN t.sender_id = ? THEN NULL
            ELSE u_sender.username 
        END AS sender_username,
        CASE 
            WHEN t.recipient_id = ? THEN NULL
            ELSE u_recipient.username 
        END AS recipient_username
    FROM 
        transactions t
    LEFT JOIN 
        users u_sender ON t.sender_id = u_sender.id
    LEFT JOIN 
        users u_recipient ON t.recipient_id = u_recipient.id
    WHERE 
        t.sender_id = ? OR t.recipient_id = ?
    ORDER BY 
        t.created_at DESC
");
$stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
$stmt->execute();
$stmt->bind_result($amount, $transaction_type, $created_at, $sender_username, $recipient_username);

$transactions = [];
while ($stmt->fetch()) {
    $transactions[] = [
        'amount' => $amount,
        'transaction_type' => $transaction_type,
        'created_at' => $created_at,
        'sender_username' => $sender_username,
        'recipient_username' => $recipient_username
    ];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ด - แอปโอนเงิน</title>
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
        .button-group {
            margin-top: 20px;
        }
        .button {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            margin: 0 10px;
        }
        .button:hover {
            background-color: #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>ยินดีต้อนรับ, <?php echo $username; ?></h1>
        <p>ยอดเงิน: <?php echo number_format($balance, 2); ?> บาท</p>
        <div class="button-group">
            <a href="deposit.php" class="button">ฝากเงิน</a>
            <a href="withdraw.php" class="button">ถอนเงิน</a>
            <a href="transfer.php" class="button">โอนเงิน</a>
            <a href="logout.php" class="button">ออกจากระบบ</a>
        </div>

        <h2>ประวัติธุรกรรม</h2>
        <table>
            <thead>
                <tr>
                    <th>ประเภทธุรกรรม</th>
                    <th>จำนวนเงิน</th>
                    <th>ผู้ส่ง</th>
                    <th>ผู้รับ</th>
                    <th>วันที่</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="5">ไม่มีประวัติธุรกรรม</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td>
                                <?php
                                // แสดงประเภทธุรกรรมที่ถูกต้อง
                                switch ($transaction['transaction_type']) {
                                    case 'deposit':
                                        echo 'ฝากเงิน';
                                        break;
                                    case 'withdraw':
                                        echo 'ถอนเงิน';
                                        break;
                                    case 'transfer':
                                        echo 'โอนเงิน';
                                        break;
                                }
                                ?>
                            </td>
                            <td><?php echo number_format($transaction['amount'], 2); ?> บาท</td>
                            <td><?php echo $transaction['sender_username'] ? $transaction['sender_username'] : 'คุณ'; ?></td>
                            <td><?php echo $transaction['recipient_username'] ? $transaction['recipient_username'] : 'คุณ'; ?></td>
                            <td><?php echo $transaction['created_at']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
