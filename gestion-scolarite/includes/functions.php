<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';

$database = new Database();
$db = $database->getConnection();
$auth = new Auth($db);

function isLoggedIn() {
    global $auth;
    return $auth->isLoggedIn();
}

function requireLogin() {
    global $auth;
    $auth->requireLogin();
}

function hasPermission($required_type) {
    global $auth;
    return $auth->hasPermission($required_type);
}

function getCurrentUser() {
    global $auth;
    return $auth->getCurrentUser();
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function showAlert($message, $type = 'info') {
    return "<div class='alert alert-{$type}'>{$message}</div>";
}

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}
?>
