<?php
// Start or resume the session
session_start();

// Check if a session is active
if (session_status() === PHP_SESSION_ACTIVE) {
    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();
}

// Return a response indicating successful logout
echo 'success';
?>
