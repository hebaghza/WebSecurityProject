<?php
session_start();

// Generate CSRF token if not already generated
if (!isset($_SESSION['_token'])) {
    $_SESSION['_token'] = bin2hex(random_bytes(32)); // Generate a random token
}
?>
