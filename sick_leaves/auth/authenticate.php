<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "الرجاء إدخال اسم المستخدم وكلمة المرور";
        header("Location: ../login.php");
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();

    try {
        $query = "SELECT * FROM users WHERE username = ? AND status = 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$username]);

        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                
                header("Location: ../dashboard.php");
                exit();
            }
        }

        $_SESSION['error'] = "اسم المستخدم أو كلمة المرور غير صحيحة";
        header("Location: ../login.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "حدث خطأ في الاتصال بقاعدة البيانات";
        header("Location: ../login.php");
        exit();
    }
}

header("Location: ../login.php");
exit();
?>
