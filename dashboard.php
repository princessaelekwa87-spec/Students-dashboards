<?php
require_once '../../includes/auth.php';
requireRole('admin');

// Get counts
$users_count = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$patients_count = $conn->query("SELECT COUNT(*) as count FROM patients")->fetch_assoc()['count'];
$alerts_count = $conn->query("SELECT COUNT(*) as count FROM alerts WHERE status='new'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Homecare HMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    <div class="container mt-4">
        <h1>Admin Dashboard</h1>
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">Users</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $users_count; ?></h5>
                        <p class="card-text">Registered users</p>
                        <a href="users.php" class="btn btn-light btn-sm">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Patients</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $patients_count; ?></h5>
                        <p class="card-text">Clients in system</p>
                        <a href="patients.php" class="btn btn-light btn-sm">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Active Alerts</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $alerts_count; ?></h5>
                        <p class="card-text">Unresolved alerts</p>
                        <a href="alerts.php" class="btn btn-light btn-sm">View</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Recent Alerts</div>
                    <div class="card-body">
                        <?php
                        $alerts = getAlerts(null, 5);
                        if (count($alerts) > 0) {
                            echo '<ul class="list-group">';
                            foreach ($alerts as $alert) {
                                $badge = $alert['status'] == 'new' ? 'danger' : 'secondary';
                                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                echo $alert['patient_name'] . ' - ' . $alert['type'] . ': ' . $alert['message'];
                                echo '<span class="badge bg-' . $badge . '">' . $alert['status'] . '</span>';
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p>No recent alerts</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>