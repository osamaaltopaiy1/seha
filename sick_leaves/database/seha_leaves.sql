-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS seha_leaves CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE seha_leaves;

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    status BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- جدول الإجازات المرضية
CREATE TABLE IF NOT EXISTS sick_leaves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    leave_number VARCHAR(15) UNIQUE NOT NULL,
    national_id VARCHAR(10) NOT NULL,
    patient_name VARCHAR(100) NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    doctor_name VARCHAR(100) NOT NULL,
    doctor_title VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    duration_days INT NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    status ENUM('active', 'cancelled') DEFAULT 'active',
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- إنشاء مستخدم admin افتراضي
-- كلمة المرور: admin123
INSERT INTO users (username, password, full_name, role, email, status) 
VALUES ('admin', '$2y$10$T8R.CzOXbrZrwsHT7cTfzuQcAS8wdR3h7oiqmOHit.iN3m0BSoOyK', 'مدير النظام', 'admin', 'admin@seha.com', 1);
