CREATE TABLE boxes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    box_type ENUM('small', 'big'),
    price DECIMAL(10,2)
);

CREATE TABLE staff (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    role VARCHAR(50),
    contact VARCHAR(50)
);

CREATE TABLE daily_production (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE,
    small_boxes INT DEFAULT 0,
    big_boxes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE supplies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE,
    staff_id INT,
    small_boxes INT DEFAULT 0,
    big_boxes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES staff(id)
);

CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE,
    staff_id INT,
    amount DECIMAL(10,2),
    boxes_count INT,
    balance DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES staff(id)
);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'manager', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE backups (
    id INT PRIMARY KEY AUTO_INCREMENT,
    filename VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100),
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE supply_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    supply_id INT,
    user_id INT,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supply_id) REFERENCES supplies(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE login_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50),
    ip_address VARCHAR(45),
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO boxes (box_type, price) VALUES 
('small', 300),
('big', 500);

INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'); 