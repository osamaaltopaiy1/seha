<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $role = $_POST['role'] ?? 'staff';

    if(empty($username) || empty($password) || empty($full_name)) {
        $_SESSION['error'] = "جميع الحقول المطلوبة يجب ملؤها";
        header("Location: create.php");
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();

    try {
        // التحقق من عدم وجود اسم مستخدم مكرر
        $check_query = "SELECT id FROM users WHERE username = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$username]);
        
        if($check_stmt->rowCount() > 0) {
            $_SESSION['error'] = "اسم المستخدم موجود مسبقاً";
            header("Location: create.php");
            exit();
        }

        // إنشاء المستخدم الجديد
        $query = "INSERT INTO users (username, password, full_name, email, phone, role, status) 
                 VALUES (:username, :password, :full_name, :email, :phone, :role, 1)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':username' => $username,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':full_name' => $full_name,
            ':email' => $email,
            ':phone' => $phone,
            ':role' => $role
        ]);

        $_SESSION['success'] = "تم إضافة المستخدم بنجاح";
        header("Location: index.php");
        exit();

    } catch(PDOException $e) {
        $_SESSION['error'] = "حدث خطأ أثناء إضافة المستخدم";
        header("Location: create.php");
        exit();
    }
}

header("Location: index.php");
exit();
?>
