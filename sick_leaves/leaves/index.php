<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

// البحث والتصفية
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$where_conditions = [];
$params = [];

if($search) {
    $where_conditions[] = "(leave_number LIKE :search OR national_id LIKE :search OR patient_name LIKE :search)";
    $params[':search'] = "%$search%";
}

if($status) {
    $where_conditions[] = "status = :status";
    $params[':status'] = $status;
}

if($date_from) {
    $where_conditions[] = "start_date >= :date_from";
    $params[':date_from'] = $date_from;
}

if($date_to) {
    $where_conditions[] = "start_date <= :date_to";
    $params[':date_to'] = $date_to;
}

// إذا كان المستخدم ليس مسؤولاً، يرى فقط الإجازات التي أصدرها
if($_SESSION['role'] !== 'admin') {
    $where_conditions[] = "created_by = :user_id";
    $params[':user_id'] = $_SESSION['user_id'];
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = "SELECT * FROM sick_leaves $where_clause ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute($params);
$leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سجل الإجازات المرضية</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>سجل الإجازات المرضية</h3>
                <a href="create.php" class="btn btn-primary">إصدار إجازة جديدة</a>
            </div>
            <div class="card-body">
                <!-- نموذج البحث -->
                <form method="GET" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="بحث..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="">كل الحالات</option>
                                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>نشط</option>
                                <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>ملغي</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_from" 
                                   value="<?php echo $date_from; ?>" placeholder="من تاريخ">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_to" 
                                   value="<?php echo $date_to; ?>" placeholder="إلى تاريخ">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">بحث</button>
                        </div>
                    </div>
                </form>

                <!-- جدول الإجازات -->
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>رقم الإجازة</th>
                                <th>اسم المريض</th>
                                <th>رقم الهوية</th>
                                <th>تاريخ البداية</th>
                                <th>المدة</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($leaves as $leave): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($leave['leave_number']); ?></td>
                                <td><?php echo htmlspecialchars($leave['patient_name']); ?></td>
                                <td><?php echo htmlspecialchars($leave['national_id']); ?></td>
                                <td><?php echo htmlspecialchars($leave['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($leave['duration_days']); ?> يوم</td>
                                <td>
                                    <span class="badge bg-<?php echo $leave['status'] === 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo $leave['status'] === 'active' ? 'نشط' : 'ملغي'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="view.php?id=<?php echo $leave['id']; ?>" 
                                       class="btn btn-sm btn-info">عرض</a>
                                    <?php if($_SESSION['role'] === 'admin' && $leave['status'] === 'active'): ?>
                                    <a href="cancel.php?id=<?php echo $leave['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('هل أنت متأكد من إلغاء هذه الإجازة؟')">إلغاء</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($leaves)): ?>
                            <tr>
                                <td colspan="7" class="text-center">لا توجد إجازات مرضية</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
