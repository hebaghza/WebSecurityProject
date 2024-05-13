<?php
// Check if session is already active
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session if it's not already active
}

// Generate CSRF token if not already generated
if (!isset($_SESSION['_token'])) {
    $_SESSION['_token'] = bin2hex(random_bytes(32)); // Generate a random token
}
?>
