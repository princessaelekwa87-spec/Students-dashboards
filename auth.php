<?php
require_once 'config.php';
require_once 'functions.php'; // Load helper functions

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /homecare/login.php');
        exit;
    }
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function requireRole($role) {
    requireLogin();
    if (!hasRole($role)) {
        header('Location: /homecare/dashboard.php');
        exit;
    }
}

function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, username, role, full_name, email, phone FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
?>