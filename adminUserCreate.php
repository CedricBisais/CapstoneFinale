<?php
$con = new mysqli('localhost', 'root', '', 'thesesarchive_db');

session_start();

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $username = validate($_POST['username']);
    $password = validate($_POST['password']);
    $account_type_id = validate($_POST['account_type_id']);
    $fName = validate($_POST['fName']);
    $mName = validate($_POST['mName']);
    $lName = validate($_POST['lName']);
    $suffix = isset($_POST['suffix']) ? validate($_POST['suffix']) : '';
    $email= validate($_POST['email_address']);
    // Validate the suffix against a predefined set of valid suffixes if it's not empty
    $validSuffixes = ['Jr', 'Sr', 'II', 'III', 'IV']; // Add or modify the suffixes as needed
    if (!empty($suffix) && !in_array($suffix, $validSuffixes)) {
        $_SESSION['status'] = 'Invalid suffix. Please provide a valid suffix.';
        $_SESSION['status_code'] = 'error';
        header("location: adminUserManagement.php");
        exit();
    }

    // Check for duplicate username
    $stmt = $con->prepare("SELECT * FROM tbl_accounts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $_SESSION['status'] = 'Duplicate username found.';
        $_SESSION['status_code'] = 'error';
        header("location: adminUserManagement.php");
        exit();
    }

    // Check password length requirements
    if (strlen($password) < 8 || strlen($password) > 32) {
        $_SESSION['status'] = 'Password must be between 8 and 32 characters.';
        $_SESSION['status_code'] = 'error';
        header("location: adminUserManagement.php");
        exit();
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Use prepared statement for the INSERT query
    $stmt = $con->prepare("INSERT INTO tbl_accounts (username, password, account_type_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $username, $hashedPassword, $account_type_id);
    $result1 = $stmt->execute();
    $stmt->close();

    if ($result1) {
        // User account created successfully, now insert additional information
        $account_id = $con->insert_id;

        $stmt = $con->prepare("INSERT INTO tbl_account_information (account_id, fName, mName, lName, suffix, email_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $account_id, $fName, $mName, $lName, $suffix, $email);
        $result2 = $stmt->execute();
        $stmt->close();

        if ($result2) {
            // User created successfully
            $_SESSION['status'] = 'User created successfully';
            $_SESSION['status_code'] = 'success';
        } else {
            // Handle database insertion error for account information
            $_SESSION['status'] = 'User creation failed (Account Information)';
            $_SESSION['status_code'] = 'error';
        }
    } else {
        // Handle database insertion error for account
        $_SESSION['status'] = 'User creation failed (Account)';
        $_SESSION['status_code'] = 'error';
    }

    header("location: adminUserManagement.php");
    exit();
}
?>