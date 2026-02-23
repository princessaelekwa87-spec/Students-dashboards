<?php
require_once '../../includes/auth.php';
requireRole('admin');
$patients = getAllPatients();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include '../../includes/navbar.php'; ?>
    <div class="container mt-4">
        <h1>Patients</h1>
        <a href="patient_add.php" class="btn btn-primary mb-3">Add Patient</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Address</th>
                    <th>Emergency Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['name']); ?></td>
                    <td><?php echo $p['age']; ?></td>
                    <td><?php echo htmlspecialchars($p['address']); ?></td>
                    <td><?php echo htmlspecialchars($p['emergency_contact']); ?></td>
                    <td>
                        <a href="patient_edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="assignments.php?patient_id=<?php echo $p['id']; ?>" class="btn btn-sm btn-info">Assign</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>