<?php
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // تحديث بيانات المستخدم
    $password_hash = password_hash('733084415', PASSWORD_DEFAULT);
    
    $query = "UPDATE users SET 
                username = 'OSAMA772419417',
                password = :password,
                full_name = 'مدير النظام',
                email = 'osama@seha.com',
                status = 1
              WHERE role = 'admin'";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':password' => $password_hash]);
    
    if($stmt->rowCount() > 0) {
        echo "تم تحديث بيانات المستخدم بنجاح";
    } else {
        echo "لم يتم تحديث البيانات";
    }
} catch(PDOException $e) {
    echo "حدث خطأ: " . $e->getMessage();
}
?>
