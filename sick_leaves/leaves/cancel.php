<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

try {
    $query = "UPDATE sick_leaves SET status = 'cancelled', updated_at = CURRENT_TIMESTAMP WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $_GET['id']]);

    $_SESSION['success'] = "تم إلغاء الإجازة المرضية بنجاح";
} catch (PDOException $e) {
    $_SESSION['error'] = "حدث خطأ أثناء إلغاء الإجازة";
}

header("Location: index.php");
exit();
?>
