<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

// إحصائيات إجازات اليوم
$today_query = "SELECT COUNT(*) as today_count FROM sick_leaves 
                WHERE DATE(created_at) = CURDATE()";
$today_stmt = $db->prepare($today_query);
$today_stmt->execute();
$today_leaves = $today_stmt->fetch(PDO::FETCH_ASSOC)['today_count'];

// إجمالي الإجازات
$total_query = "SELECT COUNT(*) as total_count FROM sick_leaves";
$total_stmt = $db->prepare($total_query);
$total_stmt->execute();
$total_leaves = $total_stmt->fetch(PDO::FETCH_ASSOC)['total_count'];

// عدد المستخدمين النشطين
$users_query = "SELECT COUNT(*) as users_count FROM users WHERE status = 1";
$users_stmt = $db->prepare($users_query);
$users_stmt->execute();
$active_users = $users_stmt->fetch(PDO::FETCH_ASSOC)['users_count'];

// آخر الإجازات المصدرة
$recent_query = "SELECT l.*, u.full_name as created_by_name 
                FROM sick_leaves l 
                JOIN users u ON l.created_by = u.id 
                ORDER BY l.created_at DESC 
                LIMIT 5";
$recent_stmt = $db->prepare($recent_query);
$recent_stmt->execute();
$recent_leaves = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - نظام الإجازات المرضية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-4">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4 text-white bg-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title">إجازات اليوم</h5>
                        <p class="card-text display-4"><?php echo $today_leaves; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4 text-white bg-success">
                    <div class="card-body text-center">
                        <h5 class="card-title">إجمالي الإجازات</h5>
                        <p class="card-text display-4"><?php echo $total_leaves; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4 text-white bg-info">
                    <div class="card-body text-center">
                        <h5 class="card-title">المستخدمين النشطين</h5>
                        <p class="card-text display-4"><?php echo $active_users; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">آخر الإجازات المصدرة</h5>
                        <a href="leaves/create.php" class="btn btn-primary btn-sm">إصدار إجازة جديدة</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>رقم الإجازة</th>
                                        <th>اسم المريض</th>
                                        <th>تاريخ البداية</th>
                                        <th>المدة</th>
                                        <th>الطبيب</th>
                                        <th>الحالة</th>
                                        <th>أصدرت بواسطة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($recent_leaves)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">لا توجد إجازات مصدرة حتى الآن</td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach($recent_leaves as $leave): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($leave['leave_number']); ?></td>
                                            <td><?php echo htmlspecialchars($leave['patient_name']); ?></td>
                                            <td><?php echo htmlspecialchars($leave['start_date']); ?></td>
                                            <td><?php echo htmlspecialchars($leave['duration_days']); ?> يوم</td>
                                            <td><?php echo htmlspecialchars($leave['doctor_name']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $leave['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo $leave['status'] === 'active' ? 'نشط' : 'ملغي'; ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($leave['created_by_name']); ?></td>
                                            <td>
                                                <a href="leaves/view.php?id=<?php echo $leave['id']; ?>" 
                                                   class="btn btn-sm btn-info">عرض</a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
