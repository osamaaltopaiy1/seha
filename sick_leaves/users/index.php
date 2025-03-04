<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM users ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>إدارة المستخدمين</h3>
                <a href="create.php" class="btn btn-primary">إضافة مستخدم جديد</a>
            </div>
            <div class="card-body">
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الاسم الكامل</th>
                                <th>اسم المستخدم</th>
                                <th>البريد الإلكتروني</th>
                                <th>الصلاحية</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php echo $user['role'] === 'admin' ? 'مسؤول' : 'موظف'; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $user['status'] ? 'success' : 'danger'; ?>">
                                        <?php echo $user['status'] ? 'نشط' : 'معطل'; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm btn-info">تعديل</a>
                                    <?php if($user['id'] !== $_SESSION['user_id']): ?>
                                    <a href="toggle_status.php?id=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm <?php echo $user['status'] ? 'btn-warning' : 'btn-success'; ?>"
                                       onclick="return confirm('هل أنت متأكد من تغيير حالة هذا المستخدم؟')">
                                        <?php echo $user['status'] ? 'تعطيل' : 'تفعيل'; ?>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
