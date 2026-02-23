<?php
require_once '../../includes/auth.php';
requireRole('caregiver');

$caregiver_id = $_SESSION['user_id'];
$patient_id = $_GET['patient_id'] ?? null;

if ($patient_id) {
    // Verify assignment
    $check = $conn->prepare("SELECT * FROM caregiver_patient WHERE caregiver_id = ? AND patient_id = ?");
    $check->bind_param('ii', $caregiver_id, $patient_id);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        header('Location: patients.php');
        exit;
    }
    $alerts = getAlerts($patient_id, 50);
} else {
    // Get all alerts for all assigned patients
    $patients = getPatientsByCaregiver($caregiver_id);
    $patient_ids = array_column($patients, 'id');
    $alerts = [];
    if (!empty($patient_ids)) {
        $placeholders = implode(',', array_fill(0, count($patient_ids), '?'));
        $types = str_repeat('i', count($patient_ids));
        $stmt = $conn->prepare("SELECT a.*, p.name as patient_name FROM alerts a JOIN patients p ON a.patient_id = p.id WHERE a.patient_id IN ($placeholders) ORDER BY a.created_at DESC LIMIT 100");
        $stmt->bind_param($types, ...$patient_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        $alerts = $result->fetch_all(MYSQLI_ASSOC);
    }
}

// Handle acknowledge
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acknowledge'])) {
    $alert_id = $_POST['alert_id'];
    acknowledgeAlert($alert_id);
    header("Location: alerts.php" . ($patient_id ? "?patient_id=$patient_id" : ""));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    <div class="container mt-4">
        <h1>Alerts</h1>
        <?php if ($patient_id): ?>
        <a href="alerts.php" class="btn btn-secondary mb-3">View All My Patients</a>
        <?php endif; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alerts as $a): ?>
                <tr>
                    <td><?php echo htmlspecialchars($a['patient_name'] ?? 'Unknown'); ?></td>
                    <td><?php echo $a['type']; ?></td>
                    <td><?php echo htmlspecialchars($a['message']); ?></td>
                    <td><?php echo $a['created_at']; ?></td>
                    <td>
                        <span class="badge bg-<?php echo $a['status'] == 'new' ? 'danger' : 'secondary'; ?>">
                            <?php echo $a['status']; ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($a['status'] == 'new'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="alert_id" value="<?php echo $a['id']; ?>">
                            <button type="submit" name="acknowledge" class="btn btn-sm btn-success">Acknowledge</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($alerts)): ?>
                <tr><td colspan="6" class="text-center">No alerts found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>