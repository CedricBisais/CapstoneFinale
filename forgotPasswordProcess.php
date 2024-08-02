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

require 'vendor/autoload.php'; // Composer autoloader for PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generateToken() {
    return bin2hex(random_bytes(32));
}

$email = $_POST['email'];

$stmt = $conn->prepare("SELECT * FROM tbl_account_information ai
                        JOIN tbl_accounts a ON ai.account_id = a.account_id
                        WHERE ai.email_address = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

if ($user) {
    $token = generateToken();

    $updateStmt = $conn->prepare("UPDATE tbl_accounts SET password_reset_token = ? WHERE username = ?");
    $updateStmt->bind_param("ss", $token, $user['username']);
    $updateStmt->execute();
    $updateStmt->close();

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = "libraryarchive0@gmail.com";
        $mail->Password   = "okvmcoejborwhtpk";
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAutoTLS = false;
        $mail->Port       = 465;

        $mail->setFrom('libraryarchive0@gmail.com', 'Admin');
        $mail->addAddress($user['email_address'], $user['fName']);

        $mail->Subject = 'Password Reset';
        $mail->Body = 'Dear ' . $user['fName'] . ',<br><br>' .
        'To reset your password, click <a href="http://localhost/CapstoneFinale/forgotResetPassword.php?token=' . $token . '">here</a>.';

        if ($mail->send()) {
            $message = 'Password reset instructions sent to your email.';
        } else {
            $message = 'Error sending email. Please try again later. Error: ' . $mail->ErrorInfo;
        }

        header("Location: forgotPassword.php?message=" . urlencode($message));
        exit();
    } catch (Exception $e) {
        echo 'Error sending email. Please try again later. Error: ', $mail->ErrorInfo;
    }
} else {
    $message = 'Email not found.';
    header("Location: forgotPassword.php?message=" . urlencode($message));
    exit();
}

$conn->close();
?>