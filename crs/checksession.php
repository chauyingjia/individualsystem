<?php
session_start();

// Function to check if user is logged in and has correct access
function checkUserAccess($required_type) {
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header("Location: login.php");
        exit();
    }

    // Check if user has correct type
    if ($_SESSION['u_type'] != $required_type) {
        header("Location: login.php");
        exit();
    }

    // Prevent caching
    header("Cache-Control: no-cache, no-store, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}
?> 