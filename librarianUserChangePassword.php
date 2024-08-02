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

// Validate if the old password matches the stored hashed password
$oldPassword = $_POST['oldpassword'];
$newPassword = $_POST['newpassword'];
$confirmPassword = $_POST['confirmpassword'];

// Check if new password matches confirm password
if ($newPassword !== $confirmPassword) {
    $_SESSION['passwordChangeResult'] = 'mismatch';
    header("Location: librarianProfile.php");
    exit();
}
if (strlen($newPassword) < 8 || strlen($newPassword) > 32) {
    $_SESSION['passwordChangeResult'] = 'invalidlength';
    header("Location: librarianProfile.php");
    exit();
}
$sql = "SELECT password FROM tbl_accounts WHERE username = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error in SQL statement: " . $conn->error);
}

$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hashedPassword = $row['password'];

    // Verify if the old password matches
    if (password_verify($oldPassword, $hashedPassword)) {
        // Hash the new password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $updateSql = "UPDATE tbl_accounts SET password = ? WHERE username = ?";
        $updateStmt = $conn->prepare($updateSql);

        if ($updateStmt) {
            $updateStmt->bind_param("ss", $hashedNewPassword, $_SESSION['username']);
            $updateStmt->execute();
            $_SESSION['passwordChangeResult'] = 'success';
        } else {
            $_SESSION['passwordChangeResult'] = 'error';
        }

        $updateStmt->close();
    } else {
        // Incorrect old password
        $_SESSION['passwordChangeResult'] = 'incorrect';
    }
} else {
    // User not found
    $_SESSION['passwordChangeResult'] = 'notfound';
}

$stmt->close();
$conn->close();

// Redirect back to the user profile page
header("Location: librarianProfile.php");
exit();
?>