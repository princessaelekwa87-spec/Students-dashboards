<?php
require_once '../../includes/auth.php';
requireRole('caregiver');

$caregiver_id = $_SESSION['user_id'];
$patient_id = $_GET['patient_id'] ?? null;

if (!$patient_id) {
    header('Location: patients.php');
    exit;
}

// Verify assignment
$check = $conn->prepare("SELECT * FROM caregiver_patient WHERE caregiver_id = ? AND patient_id = ?");
$check->bind_param('ii', $caregiver_id, $patient_id);
$check->execute();
if ($check->get_result()->num_rows == 0) {
    header('Location: patients.php');
    exit;
}

$patient = getPatientById($patient_id);
$medications = getMedicationsForPatient($patient_id);

// Mark as taken
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['taken'])) {
    $med_id = $_POST['med_id'];
    markMedicationTaken($med_id);
    header("Location: medications.php?patient_id=$patient_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medications for <?php echo htmlspecialchars($patient['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    <div class="container mt-4">
        <h1>Medications for <?php echo htmlspecialchars($patient['name']); ?></h1>
        <a href="patient_view.php?id=<?php echo $patient_id; ?>" class="btn btn-secondary mb-3">Back to Patient</a>
        <table class="table">
            <thead>
                <tr>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Schedule</th>
                    <th>Last Taken</th>
                    <th>Next Due</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($medications as $m): ?>
                <tr>
                    <td><?php echo htmlspecialchars($m['name']); ?></td>
                    <td><?php echo htmlspecialchars($m['dosage']); ?></td>
                    <td><?php echo htmlspecialchars($m['schedule']); ?></td>
                    <td><?php echo $m['last_taken'] ?? 'Never'; ?></td>
                    <td><?php echo $m['next_due']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="med_id" value="<?php echo $m['id']; ?>">
                            <button type="submit" name="taken" class="btn btn-sm btn-success" <?php echo ($m['last_taken'] && strtotime($m['last_taken']) > time()-3600) ? 'disabled' : ''; ?>>Mark Taken</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($medications)): ?>
                <tr><td colspan="6" class="text-center">No medications.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>