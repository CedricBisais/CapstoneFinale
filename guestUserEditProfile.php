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

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page if the user is not logged in
    header("location:Login.php");
    exit;
}

if (isset($_POST["updateData"])) {
    // Process the form submission and update the user's information in the database
    $newUsername = $_POST["username"];
    $newFirstname = $_POST["fName"];
    $newLastname = $_POST["lName"];
    $newSuffix = $_POST["suffix"];
    $newEmail = $_POST["email_address"];

    // Check if the new username already exists and is different from the current username
    $checkUsernameSql = "SELECT username FROM tbl_account_information ai
                         INNER JOIN tbl_accounts a ON ai.account_id = a.account_id
                         WHERE username = ? AND username <> ?";
    $stmtCheck = $conn->prepare($checkUsernameSql);
    $stmtCheck->bind_param("ss", $newUsername, $_SESSION["username"]);
    $stmtCheck->execute();
    $stmtCheckResult = $stmtCheck->get_result();

    if ($stmtCheckResult->num_rows > 0) {
        // Username already exists, inform the user and redirect
        $_SESSION['usernameCheckResult'] = 'taken';
        header("location:userProfile.php");
        exit;
    } else {
        // Update the user's information in the database
        $updateSql = "UPDATE tbl_account_information ai
                      INNER JOIN tbl_accounts a ON ai.account_id = a.account_id
                      SET a.username = ?, ai.fName = ?, ai.lName = ?, ai.suffix = ?, ai.email_address = ?
                      WHERE a.username = ?";
        $stmtUpdate = $conn->prepare($updateSql);
        $stmtUpdate->bind_param("ssssss", $newUsername, $newFirstname, $newLastname, $newSuffix, $newEmail, $_SESSION["username"]);

        if ($stmtUpdate->execute()) {
            // Update the session username to reflect the new username
            $_SESSION["username"] = $newUsername;
            // Redirect with a parameter indicating a successful update
            $_SESSION['updateResult'] = 'success';
            header("location:guestUserProfile.php");
            exit;
        } else {
            // Provide feedback to the user about the error
            echo "Error updating record: " . $stmtUpdate->error;
        }

        $stmtUpdate->close();
    }

    $stmtCheck->close();
}

// Fetch the updated user information
$sql = "SELECT ai.*, a.username 
        FROM tbl_account_information ai
        INNER JOIN tbl_accounts a ON ai.account_id = a.account_id
        WHERE a.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $fName = $row['fName'];
    $lName = $row['lName'];
    $suffix = $row['suffix'];
    $email_address = $row['email_address'];
}

$stmt->close();
$conn->close();
?>