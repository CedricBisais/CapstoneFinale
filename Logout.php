<?php
session_start();

// Check if the user is authenticated
if (isset($_SESSION['user_id'])) {
    // Check the account type before unsetting session variables
    $allowedAccountTypes = ['Student', 'Admin', 'Librarian', 'Guest', 'Faculty'];

    if (in_array($_SESSION['account_type'], $allowedAccountTypes)) {
        // Unset session variables specific to the user's account type
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['account_type_id']);
        unset($_SESSION['account_type']);
    }

    // Set anti-caching headers
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');

    // Redirect to login or another page after logout
    header('Location: indexPage.php');
    exit;
} else {
    // If the user is not authenticated, redirect to login
    header('Location: indexPage.php');
    exit;
}
?>