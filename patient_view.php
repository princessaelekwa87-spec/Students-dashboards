<?php
require_once '../../includes/auth.php';
requireRole('caregiver');

$patient_id = $_GET['id'] ?? 0;
$patient = getPatientById($patient_id);
if (!$patient) {
    header('Location: patients.php');
    exit;
}

// Verify this caregiver is assigned
$caregiver_id = $_SESSION['user_id'];
$check = $conn->prepare("SELECT * FROM caregiver_patient WHERE caregiver_id = ? AND patient_id = ?");
$check->bind_param('ii', $caregiver_id, $patient_id);
$check->execute();
if ($check->get_result()->num_rows == 0 && $_SESSION['role'] != 'admin') {
    header('Location: patients.php');
    exit;
}

$latest_sensor = getLatestSensorData($patient_id);
$medications = getMedicationsForPatient($patient_id);
$alerts = getAlerts($patient_id, 5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    <div class="container mt-4">
        <h1><?php echo htmlspecialchars($patient['name']); ?></h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">Personal Info</div>
                    <div class="card-body">
                        <p><strong>Age:</strong> <?php echo $patient['age']; ?></p>
                        <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($patient['address'])); ?></p>
                        <p><strong>Emergency Contact:</strong> <?php echo htmlspecialchars($patient['emergency_contact']); ?> (<?php echo $patient['emergency_phone']; ?>)</p>
                        <p><strong>Medical Conditions:</strong> <?php echo nl2br(htmlspecialchars($patient['medical_conditions'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">Latest Sensor Readings</div>
                    <div class="card-body">
                        <?php if (count($latest_sensor) > 0): ?>
                        <ul class="list-group">
                            <?php foreach ($latest_sensor as $s): ?>
                            <li class="list-group-item"><?php echo $s['sensor_type']; ?>: <?php echo $s['value'] . ' ' . $s['unit']; ?> (<?php echo $s['recorded_at']; ?>)</li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p>No sensor data yet.</p>
                        <?php endif; ?>
                        <a href="../../pages/sensor_simulator.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-sm btn-secondary mt-2">Simulate Sensor</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">Medications</div>
                    <div class="card-body">
                        <?php if (count($medications) > 0): ?>
                        <ul class="list-group">
                            <?php foreach ($medications as $m): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo $m['name'] . ' ' . $m['dosage']; ?> - Next: <?php echo $m['next_due']; ?>
                                <?php if ($m['last_taken']): ?>
                                <span class="badge bg-success">Last taken: <?php echo $m['last_taken']; ?></span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p>No medications scheduled.</p>
                        <?php endif; ?>
                        <a href="medications.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-sm btn-primary mt-2">Manage</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">Recent Alerts</div>
                    <div class="card-body">
                        <?php if (count($alerts) > 0): ?>
                        <ul class="list-group">
                            <?php foreach ($alerts as $a): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo $a['type'] . ': ' . $a['message']; ?> (<?php echo $a['created_at']; ?>)
                                <span class="badge bg-<?php echo $a['status'] == 'new' ? 'danger' : 'secondary'; ?>"><?php echo $a['status']; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <p>No recent alerts.</p>
                        <?php endif; ?>
                        <a href="alerts.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-sm btn-primary mt-2">View All</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>