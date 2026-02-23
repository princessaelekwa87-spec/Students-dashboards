<?php
require_once '../includes/auth.php';
requireLogin();

$patient_id = $_GET['patient_id'] ?? null;
if (!$patient_id) {
    die("Patient ID required");
}

// Verify access (caregiver of this patient or admin)
if ($_SESSION['role'] == 'caregiver') {
    $check = $conn->prepare("SELECT * FROM caregiver_patient WHERE caregiver_id = ? AND patient_id = ?");
    $check->bind_param('ii', $_SESSION['user_id'], $patient_id);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        die("Access denied");
    }
} elseif ($_SESSION['role'] != 'admin') {
    die("Access denied");
}

$patient = getPatientById($patient_id);
if (!$patient) {
    die("Patient not found");
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sensor_type = $_POST['sensor_type'];
    $value = $_POST['value'];
    $unit = $_POST['unit'] ?? '';

    if (addSensorData($patient_id, $sensor_type, $value, $unit)) {
        $message = "Sensor data added successfully.";
    } else {
        $message = "Error adding data.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensor Simulator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-4">
        <h1>Sensor Simulator for <?php echo htmlspecialchars($patient['name']); ?></h1>
        <?php if ($message): ?>
        <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="sensor_type" class="form-label">Sensor Type</label>
                <select class="form-control" id="sensor_type" name="sensor_type" required>
                    <option value="heart_rate">Heart Rate</option>
                    <option value="temperature">Temperature</option>
                    <option value="motion">Motion</option>
                    <option value="door">Door</option>
                    <option value="bed">Bed</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="value" class="form-label">Value</label>
                <input type="text" class="form-control" id="value" name="value" required>
            </div>
            <div class="mb-3">
                <label for="unit" class="form-label">Unit (optional)</label>
                <input type="text" class="form-control" id="unit" name="unit">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="patient_view.php?id=<?php echo $patient_id; ?>" class="btn btn-secondary">Back</a>
        </form>
        <hr>
        <h3>Recent Sensor Data</h3>
        <table class="table">
            <thead>
                <tr><th>Type</th><th>Value</th><th>Unit</th><th>Time</th></tr>
            </thead>
            <tbody>
                <?php
                $data = getLatestSensorData($patient_id);
                foreach ($data as $d):
                ?>
                <tr>
                    <td><?php echo $d['sensor_type']; ?></td>
                    <td><?php echo $d['value']; ?></td>
                    <td><?php echo $d['unit']; ?></td>
                    <td><?php echo $d['recorded_at']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>