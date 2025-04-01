<?php
require_once '../config.php';
require_once 'auth.php';

// Log the logout action if admin is logged in
if (isset($_SESSION['admin_id'])) {
    logAdminAction($_SESSION['admin_id'], 'logout', 'Admin logged out');
}

// Clear all session variables
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: login.php');
exit(); 