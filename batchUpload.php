<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login
    header('Location: Login.php');
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thesesarchive_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['batchUpload'])) {
    $fileName = $_FILES['import_file']['name'];
    $file_ext = pathinfo($fileName, PATHINFO_EXTENSION);

    $allowed_ext = ['kls', 'csv', 'xlsx'];

    if (in_array($file_ext, $allowed_ext)) {
        $inputFileName = $_FILES['import_file']['tmp_name'];
        $spreadsheet = IOFactory::load($inputFileName);
        $data = $spreadsheet->getActiveSheet()->toArray();

        $msg = true; // Assume success by default
        foreach ($data as $count => $row) {
            if ($count > 0) {
                $username = $row['0'];
                $accountTypeDescription = $row['1']; // Assuming this is the account type description in the Excel file
                $firstName = $row['2'];
                $middleName = $row['3'];
                $lastName = $row['4'];
                $suffix = $row['5'];
                $emailAddress = $row['6'];
                $password = password_hash($row['7'], PASSWORD_DEFAULT); // Assuming the password is in column index 7
        
                // Get the account type ID based on the description
                $accountTypeQuery = "SELECT account_type_id FROM tbl_account_type WHERE account_type_description = '$accountTypeDescription'";
                $accountTypeResult = $conn->query($accountTypeQuery);
        
                if ($accountTypeResult->num_rows > 0) {
                    $accountTypeRow = $accountTypeResult->fetch_assoc();
                    $accountType = $accountTypeRow['account_type_id'];
        
                    // Insert into tbl_accounts
                    $userQuery = "INSERT INTO tbl_accounts (username, account_type_id, password) VALUES ('$username', '$accountType', '$password')";
                    $result = $conn->query($userQuery);
        
                    if ($result) {
                        // Get the auto-generated account_id
                        $accountID = $conn->insert_id;
        
                        // Insert into tbl_account_information
                        $infoQuery = "INSERT INTO tbl_account_information (account_id, fName, lName, mName, suffix, email_address) VALUES ('$accountID', '$firstName', '$lastName', '$middleName', '$suffix', '$emailAddress')";
                        $infoResult = $conn->query($infoQuery);
        
                        if (!$infoResult) {
                            // Handle the error
                            $msg = false;
                            echo "Error in infoQuery: " . $conn->error;
                            break;
                        }
                    } else {
                        // Handle the error
                        $msg = false;
                        echo "Error in userQuery: " . $conn->error;
                        break;
                    }
                } else {
                    // Handle the error if the account type is not found
                    $msg = false;
                    echo "Error: Account type not found for description '$accountTypeDescription'";
                    break;
                }
            }
        }
        if ($msg) {
            $_SESSION['status'] = "Successfully Imported";
            $_SESSION['status_code'] = "success";
        } else {
            $_SESSION['status'] = "Error in Importing";
            $_SESSION['status_code'] = "error";
        }

        header('Location: adminUserManagement.php');
        exit(0);
    } else {
        $_SESSION['status'] = "Invalid File";
        $_SESSION['status_code'] = "error";
        header('Location: adminUserManagement.php');
        exit(0);
    }
}
?>