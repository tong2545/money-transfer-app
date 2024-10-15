CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,  -- เพิ่ม UNIQUE เพื่อป้องกันชื่อผู้ใช้ซ้ำ
    password VARCHAR(255) NOT NULL,
    balance DECIMAL(10, 2) DEFAULT 0.00
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,                           -- ผู้ส่งเงิน
    recipient_id INT,                        -- ผู้รับเงิน (null สำหรับธุรกรรมประเภทอื่น)
    amount DECIMAL(10, 2) NOT NULL,
    recipient_username VARCHAR(50),          -- เพิ่มคอลัมน์นี้เพื่อเก็บชื่อผู้รับ
    transaction_type ENUM('deposit', 'withdraw', 'transfer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (recipient_id) REFERENCES users(id)  -- เชื่อมโยงกับผู้รับเงิน
);
