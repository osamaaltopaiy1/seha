<?php
session_start();
if(!isset($_SESSION['user_id'])) {
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

$query = "SELECT l.*, u.full_name as created_by_name 
          FROM sick_leaves l 
          JOIN users u ON l.created_by = u.id 
          WHERE l.id = :id";
$stmt = $db->prepare($query);
$stmt->execute([':id' => $_GET['id']]);
$leave = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$leave) {
    header("Location: index.php");
    exit();
}

// التحقق من الصلاحيات - فقط المسؤول أو من أصدر الإجازة يمكنه رؤيتها
if($_SESSION['role'] !== 'admin' && $leave['created_by'] !== $_SESSION['user_id']) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الإجازة المرضية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
            .print-only {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <?php include '../includes/navbar.php'; ?>
    </div>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>تفاصيل الإجازة المرضية</h3>
                <div class="no-print">
                    <button onclick="window.print()" class="btn btn-secondary">طباعة</button>
                    <a href="index.php" class="btn btn-primary">عودة</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>معلومات المريض</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th>رقم الإجازة</th>
                                <td><?php echo htmlspecialchars($leave['leave_number']); ?></td>
                            </tr>
                            <tr>
                                <th>الاسم</th>
                                <td><?php echo htmlspecialchars($leave['patient_name']); ?></td>
                            </tr>
                            <tr>
                                <th>رقم الهوية</th>
                                <td><?php echo htmlspecialchars($leave['national_id']); ?></td>
                            </tr>
                            <tr>
                                <th>المسمى الوظيفي</th>
                                <td><?php echo htmlspecialchars($leave['job_title']); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>تفاصيل الإجازة</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th>تاريخ البداية</th>
                                <td><?php echo htmlspecialchars($leave['start_date']); ?></td>
                            </tr>
                            <tr>
                                <th>تاريخ النهاية</th>
                                <td><?php echo htmlspecialchars($leave['end_date']); ?></td>
                            </tr>
                            <tr>
                                <th>عدد الأيام</th>
                                <td><?php echo htmlspecialchars($leave['duration_days']); ?> يوم</td>
                            </tr>
                            <tr>
                                <th>الحالة</th>
                                <td>
                                    <span class="badge bg-<?php echo $leave['status'] === 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo $leave['status'] === 'active' ? 'نشط' : 'ملغي'; ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <h4>معلومات الطبيب</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th>اسم الطبيب</th>
                                <td><?php echo htmlspecialchars($leave['doctor_name']); ?></td>
                            </tr>
                            <tr>
                                <th>المسمى الوظيفي</th>
                                <td><?php echo htmlspecialchars($leave['doctor_title']); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>معلومات إضافية</h4>
                        <table class="table table-bordered">
                            <tr>
                                <th>تاريخ الإصدار</th>
                                <td><?php echo htmlspecialchars($leave['created_at']); ?></td>
                            </tr>
                            <tr>
                                <th>أصدرت بواسطة</th>
                                <td><?php echo htmlspecialchars($leave['created_by_name']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
