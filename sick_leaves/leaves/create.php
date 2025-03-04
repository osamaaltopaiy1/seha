<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

function generateLeaveNumber() {
    return 'PSG' . date('Y') . rand(1000000, 9999999);
}
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إصدار إجازة مرضية جديدة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h3>إصدار إجازة مرضية جديدة</h3>
            </div>
            <div class="card-body">
                <form action="process_leave.php" method="POST">
                    <input type="hidden" name="leave_number" value="<?php echo generateLeaveNumber(); ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الهوية</label>
                            <input type="text" class="form-control" name="national_id" required 
                                   pattern="[0-9]{10}" title="الرجاء إدخال 10 أرقام">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم المريض</label>
                            <input type="text" class="form-control" name="patient_name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المسمى الوظيفي</label>
                            <input type="text" class="form-control" name="job_title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم الطبيب</label>
                            <input type="text" class="form-control" name="doctor_name" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المسمى الوظيفي للطبيب</label>
                            <input type="text" class="form-control" name="doctor_title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ بداية الإجازة</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">عدد أيام الإجازة</label>
                            <input type="number" class="form-control" name="duration_days" min="1" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">إصدار الإجازة</button>
                        <a href="../dashboard.php" class="btn btn-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // حساب تاريخ نهاية الإجازة تلقائياً
        document.querySelector('input[name="duration_days"]').addEventListener('change', function() {
            const startDate = document.querySelector('input[name="start_date"]').value;
            if(startDate && this.value) {
                const endDate = new Date(startDate);
                endDate.setDate(endDate.getDate() + parseInt(this.value) - 1);
                // يمكن إضافة عنصر لعرض تاريخ النهاية
            }
        });
    </script>
</body>
</html>
