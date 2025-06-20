<?php
// includes/auth.php
session_start();

function check_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit;
    }
}

function get_logged_in_user_id() {
    return $_SESSION['user_id'] ?? null;
}
?>
