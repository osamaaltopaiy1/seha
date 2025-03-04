<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

if(isset($_GET['id']) && $_GET['id'] != $_SESSION['user_id']) {
    $database = new Database();
    $db = $database->getConnection();

    try {
        // تغيير حالة المستخدم
        $query = "UPDATE users SET status = NOT status WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $_GET['id']]);

        $_SESSION['success'] = "تم تغيير حالة المستخدم بنجاح";
    } catch(PDOException $e) {
        $_SESSION['error'] = "حدث خطأ أثناء تغيير حالة المستخدم";
    }
}

header("Location: index.php");
exit();
?>
