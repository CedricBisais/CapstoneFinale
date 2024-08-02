<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thesesarchive_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$token = $_POST['token'];
$newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

$updateStmt = $conn->prepare("UPDATE tbl_accounts SET password = ?, password_reset_token = NULL WHERE password_reset_token = ?");
$updateStmt->bind_param("ss", $newPassword, $token);
$updateStmt->execute();

if ($updateStmt->error) {
    // Password update failed
    $_SESSION['error_message'] = 'Password update failed. Please try again.';
    header("Location: forgotPassword.php");
    exit();
} else {
    // Password update successful
    $_SESSION['success_message'] = 'Password updated successfully. You can now log in with your new password.';
    header("Location: login.php?success=1");
    exit();
}

$updateStmt->close();
$conn->close();
?>