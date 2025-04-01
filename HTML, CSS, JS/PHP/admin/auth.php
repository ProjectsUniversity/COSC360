<?php
session_start();

function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit();
    }
}

function logAdminAction($action, $details = '') {
    global $pdo;
    $admin_id = $_SESSION['admin_id'] ?? null;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    if ($admin_id) {
        $stmt = $pdo->prepare("INSERT INTO audit_logs (admin_id, action_type, details, ip_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$admin_id, $action, $details, $ip_address]);
    }
}