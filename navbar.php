<?php
if (!isset($_SESSION)) session_start();
$role = $_SESSION['role'] ?? '';
$full_name = $_SESSION['full_name'] ?? '';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="/homecare/dashboard.php">Homecare HMS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if ($role == 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="/homecare/pages/admin/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/homecare/pages/admin/users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="/homecare/pages/admin/patients.php">Patients</a></li>
                <li class="nav-item"><a class="nav-link" href="/homecare/pages/admin/assignments.php">Assignments</a></li>
                <?php elseif ($role == 'caregiver'): ?>
                <li class="nav-item"><a class="nav-link" href="/homecare/pages/caregiver/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/homecare/pages/caregiver/patients.php">My Patients</a></li>
                <li class="nav-item"><a class="nav-link" href="/homecare/pages/caregiver/alerts.php">Alerts</a></li>
                <?php elseif ($role == 'client'): ?>
                <li class="nav-item"><a class="nav-link" href="/homecare/pages/client/dashboard.php">My Dashboard</a></li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text me-3">
                Welcome, <?php echo htmlspecialchars($full_name); ?>
            </span>
            <a href="/homecare/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>