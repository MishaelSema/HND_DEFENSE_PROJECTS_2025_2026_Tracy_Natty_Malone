<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

function require_role($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header('Location: ../home.php');
        exit;
    }
}