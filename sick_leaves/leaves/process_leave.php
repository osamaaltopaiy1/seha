<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();

try {
    // حساب تاريخ النهاية
    $start_date = new DateTime($_POST['start_date']);
    $end_date = clone $start_date;
    $end_date->modify('+' . ($_POST['duration_days'] - 1) . ' days');

    $query = "INSERT INTO sick_leaves 
              (leave_number, national_id, patient_name, job_title, doctor_name, 
               doctor_title, start_date, end_date, duration_days, created_by) 
              VALUES 
              (:leave_number, :national_id, :patient_name, :job_title, :doctor_name,
               :doctor_title, :start_date, :end_date, :duration_days, :created_by)";

    $stmt = $db->prepare($query);
    
    $stmt->execute([
        ':leave_number' => $_POST['leave_number'],
        ':national_id' => $_POST['national_id'],
        ':patient_name' => $_POST['patient_name'],
        ':job_title' => $_POST['job_title'],
        ':doctor_name' => $_POST['doctor_name'],
        ':doctor_title' => $_POST['doctor_title'],
        ':start_date' => $start_date->format('Y-m-d'),
        ':end_date' => $end_date->format('Y-m-d'),
        ':duration_days' => $_POST['duration_days'],
        ':created_by' => $_SESSION['user_id']
    ]);

    $_SESSION['success'] = "تم إصدار الإجازة المرضية بنجاح";
    header("Location: view.php?id=" . $db->lastInsertId());
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "حدث خطأ أثناء إصدار الإجازة: " . $e->getMessage();
    header("Location: create.php");
    exit();
}
?>
