<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thesesarchive_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['updateProfilePicture'])) {
    $targetDirectory = "Uploads/";

    if (!file_exists($targetDirectory)) {
        mkdir($targetDirectory, 0777, true);
    }

    // Check if the user wants to remove the profile picture
    if (isset($_POST['removeProfilePicture']) && $_POST['removeProfilePicture'] == '1') {
        // Set the profile_picture field in the database to NULL
        $updateSql = "UPDATE tbl_account_information SET profile_picture = NULL WHERE account_id = (SELECT account_id FROM tbl_accounts WHERE username = ?)";
        $stmt = $conn->prepare($updateSql);

        if (!$stmt) {
            die("Error in SQL statement: " . $conn->error);
        }

        $stmt->bind_param("s", $_SESSION['username']);

        if (!$stmt) {
            die("Error in binding parameters: " . $conn->error);
        }

        if ($stmt->execute()) {
            $_SESSION['profilePictureResult'] = 'success';
        } else {
            $_SESSION['profilePictureResult'] = 'error';
        }

        $stmt->close();
    } else {
        $originalFileName = $_FILES["profilePicture"]["name"];
        $targetFile = $targetDirectory . $originalFileName;
        $uploadOk = 1;

        if ($_FILES["profilePicture"]["size"] > 2000000) {
            $_SESSION['profilePictureResult'] = 'error_size';
            $uploadOk = 0;
        }

        $imageInfo = getimagesize($_FILES["profilePicture"]["tmp_name"]);
        if (!$imageInfo || ($imageInfo[2] != IMAGETYPE_JPEG && $imageInfo[2] != IMAGETYPE_PNG)) {
            $_SESSION['profilePictureResult'] = 'error_type';
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            $_SESSION['profilePictureResult'] = 'error_upload';
        } else {
            // Assuming username is unique in tbl_accounts
            $updateSql = "UPDATE tbl_account_information SET profile_picture = ? WHERE account_id = (SELECT account_id FROM tbl_accounts WHERE username = ?)";
            $stmt = $conn->prepare($updateSql);

            if (!$stmt) {
                die("Error in SQL statement: " . $conn->error);
            }

            $stmt->bind_param("ss", $originalFileName, $_SESSION['username']);

            if (!$stmt) {
                die("Error in binding parameters: " . $conn->error);
            }

            if ($stmt->execute()) {
                $_SESSION['profilePictureResult'] = 'success';

                if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $targetFile)) {
                    $_SESSION['profilePictureResult'] = 'success_upload';
                } else {
                    $_SESSION['profilePictureResult'] = 'error_upload';
                }
            } else {
                $_SESSION['profilePictureResult'] = 'error';
            }

            $stmt->close();
        }
    }

    header("Location: facultyUserProfile.php");
    exit;
}

$conn->close();
?>