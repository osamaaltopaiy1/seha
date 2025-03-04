<?php
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// تحديد المسار النشط
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/seha/sick_leaves/dashboard.php">نظام الإجازات المرضية</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>" 
                       href="/seha/sick_leaves/dashboard.php">الرئيسية</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'create.php' ? 'active' : ''; ?>" 
                       href="/seha/sick_leaves/leaves/create.php">إصدار إجازة</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" 
                       href="/seha/sick_leaves/leaves/index.php">سجل الإجازات</a>
                </li>
                <?php if($_SESSION['role'] === 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" 
                       href="/seha/sick_leaves/users/index.php">إدارة المستخدمين</a>
                </li>
                <?php endif; ?>
            </ul>
            <div class="d-flex">
                <span class="navbar-text mx-3">
                    مرحباً، <?php echo htmlspecialchars($_SESSION['full_name']); ?>
                </span>
                <a href="/seha/sick_leaves/auth/logout.php" class="btn btn-light">تسجيل الخروج</a>
            </div>
        </div>
    </div>
</nav>
