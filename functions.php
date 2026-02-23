<?php
require_once 'config.php';

// Alert functions
function getNewAlertsCount($patient_id = null) {
    global $conn;
    if ($patient_id) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM alerts WHERE patient_id = ? AND status = 'new'");
        $stmt->bind_param('i', $patient_id);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM alerts WHERE status = 'new'");
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getAlerts($patient_id = null, $limit = 10) {
    global $conn;
    if ($patient_id) {
        $stmt = $conn->prepare("SELECT a.*, p.name as patient_name FROM alerts a JOIN patients p ON a.patient_id = p.id WHERE a.patient_id = ? ORDER BY a.created_at DESC LIMIT ?");
        $stmt->bind_param('ii', $patient_id, $limit);
    } else {
        $stmt = $conn->prepare("SELECT a.*, p.name as patient_name FROM alerts a JOIN patients p ON a.patient_id = p.id ORDER BY a.created_at DESC LIMIT ?");
        $stmt->bind_param('i', $limit);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function acknowledgeAlert($alert_id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE alerts SET status = 'acknowledged' WHERE id = ?");
    $stmt->bind_param('i', $alert_id);
    return $stmt->execute();
}

// Patient functions
function getPatientsByCaregiver($caregiver_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT p.* FROM patients p JOIN caregiver_patient cp ON p.id = cp.patient_id WHERE cp.caregiver_id = ?");
    $stmt->bind_param('i', $caregiver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllPatients() {
    global $conn;
    $result = $conn->query("SELECT p.*, u.full_name as user_name FROM patients p LEFT JOIN users u ON p.user_id = u.id ORDER BY p.name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getPatientById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Medication functions
function getMedicationsForPatient($patient_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM medications WHERE patient_id = ? AND status = 'active' ORDER BY next_due ASC");
    $stmt->bind_param('i', $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function markMedicationTaken($med_id) {
    global $conn;
    $now = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("UPDATE medications SET last_taken = ? WHERE id = ?");
    $stmt->bind_param('si', $now, $med_id);
    return $stmt->execute();
}

// Sensor functions
function addSensorData($patient_id, $sensor_type, $value, $unit = '') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO sensor_data (patient_id, sensor_type, value, unit) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('isss', $patient_id, $sensor_type, $value, $unit);
    return $stmt->execute();
}

function getLatestSensorData($patient_id, $sensor_type = null) {
    global $conn;
    if ($sensor_type) {
        $stmt = $conn->prepare("SELECT * FROM sensor_data WHERE patient_id = ? AND sensor_type = ? ORDER BY recorded_at DESC LIMIT 1");
        $stmt->bind_param('is', $patient_id, $sensor_type);
    } else {
        $stmt = $conn->prepare("SELECT * FROM sensor_data WHERE patient_id = ? ORDER BY recorded_at DESC LIMIT 10");
        $stmt->bind_param('i', $patient_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>