<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// تضمين ملف الاتصال بقاعدة البيانات
require_once 'sick_leaves/config/database.php';

// التحقق من إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // تنظيف وتأمين المدخلات
    $service_id = htmlspecialchars(trim($_POST['service_id']), ENT_QUOTES, 'UTF-8');
    $identity = htmlspecialchars(trim($_POST['identity']), ENT_QUOTES, 'UTF-8');
    
    // التحقق من البيانات الخاصة
    if ($service_id === 'PSG0000' && $identity === '772419417') {
        echo json_encode([
            'status' => 'redirect',
            'url' => 'sick_leaves/login.php'
        ]);
        exit();
    }
    
    try {
        // إنشاء اتصال بقاعدة البيانات
        $database = new Database();
        $db = $database->getConnection();
        
        if(!$db) {
            throw new Exception("فشل الاتصال بقاعدة البيانات");
        }
        
        // الاستعلام عن الإجازة المرضية
        $query = "SELECT * FROM sick_leaves WHERE leave_number = :service_id AND national_id = :identity";
        $stmt = $db->prepare($query);
        
        // ربط المعلمات
        $stmt->bindParam(":service_id", $service_id);
        $stmt->bindParam(":identity", $identity);
        
        // تنفيذ الاستعلام
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // تم العثور على نتائج
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode([
                'status' => 'success',
                'data' => $result
            ], JSON_UNESCAPED_UNICODE);
        } else {
            // لم يتم العثور على نتائج
            echo json_encode([
                'status' => 'error',
                'message' => 'لا يوجد نتائج'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch(Exception $e) {
        error_log("Error in index2.php: " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    // إذا تم الوصول للصفحة مباشرة
    header("Location: index.html");
    exit();
}
?>
