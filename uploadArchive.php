<?php

if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page
    header("location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "thesesarchive_db";

$connection = new mysqli($servername, $username, $password, $database);
$stmt = $connection->prepare("SELECT a.*, ai.* FROM tbl_accounts a JOIN tbl_account_information ai ON a.account_id = ai.account_id WHERE a.username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    // Default values if no record is found
    $row = [
        'profile_picture' => 'Uploads/default-user.png',
        'fName' => '',
        'lName' => '',
    ];
}

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

function getNextAscensionID($connection) {
    $query = "SELECT MAX(ascension_id) as max_id FROM tbl_ascension";
    $result = $connection->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        return $row['max_id'] + 1;
    } else {
        return 1; // Default to 1 if there's an issue fetching the max ID
    }
}
function getAscensionDescription($ascensionID) {
    // Format the ascension_description with leading zeros
    return str_pad($ascensionID, 3, '0', STR_PAD_LEFT);
}

function isThesisTitleExists($connection, $title) {
    $query = "SELECT COUNT(*) as count FROM tbl_archives WHERE archive_title = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $stmt->close();
    return $count > 0;
}

$ascensionID = getNextAscensionID($connection); // Assuming $connection is the mysqli connection object
$archiveDescription = getAscensionDescription($ascensionID);

$thesisTitle = $authors = $course = $dateOfPublication = $abstract = $targetFile = $errorMessage = "";
$submissionMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $thesisTitle = $_POST["archive_title"];
    $authors = $_POST["archive_authors"];
    $course = $_POST["course"];
    $dateOfPublication = $_POST["date_of_publication"];
    $abstract = $_POST["abstract"];
    
    if (isThesisTitleExists($connection, $thesisTitle)) {
        $_SESSION['status'] = "Error: Thesis with the same title already exists.";
        $_SESSION['status_code'] = 'error';
        
    }   else {
        if (isset($_FILES["thesisFile"])) {
        $targetDir = "Uploads/Archives/";
        $targetFile = $targetDir . basename($_FILES["thesisFile"]["name"]);
    

        // Check if the file already exists
        if (file_exists($targetFile)) {
            $_SESSION['status'] = "Error: The file already exists.";
            $_SESSION['status_code'] = 'error';
        } else {
            if ($_FILES["thesisFile"]["error"] == UPLOAD_ERR_OK) {
                if (move_uploaded_file($_FILES["thesisFile"]["tmp_name"], $targetFile)) {
                    // Insert data into tbl_ascension to get the next ascension_id
                    $queryAscension = "INSERT INTO tbl_ascension (ascension_id) VALUES (NULL)";
                    $resultAscension = $connection->query($queryAscension);

                    if ($resultAscension) {
                        // Get the last inserted ascension_id
                        $ascensionID = $connection->insert_id;

                        // Use the obtained ascension_id to insert into tbl_archives
                        $queryArchives = "INSERT INTO tbl_archives (ascension_id, archive_description, archive_title, archive_authors, course_id, archive_date_of_publication, archive_abstract, archive_document, status) 
                                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";

                        $stmtArchives = $connection->prepare($queryArchives);

                        if ($stmtArchives) {
                            $stmtArchives->bind_param("isssssss", $ascensionID, $archiveDescription, $thesisTitle, $authors, $course, $dateOfPublication, $abstract, $targetFile);

                            if ($stmtArchives->execute()) {
                                // Data inserted successfully
                                $_SESSION['status'] = "Thesis Uploaded successfully!";
                                $_SESSION['status_code'] = 'success';
                                $thesisTitle = $authors = $course = $dateOfPublication = $abstract = $targetFile = "";
                            } else {
                                // Error in executing the statement for tbl_archives
                                $_SESSION['status'] = "Error: Unable to Upload thesis." . $stmtArchives->error;
                                $_SESSION['status_code'] = 'error';
                            }

                            $stmtArchives->close();
                        } else {
                            // Error in preparing the statement for tbl_archives
                            die("Error in preparing the statement for tbl_archives: " . $connection->error);
                        }
                    } else {
                        // Error in executing the statement for tbl_ascension
                        $_SESSION['status'] = "Error: Unable to submit thesis. " . $connection->error;
                        $_SESSION['status_code'] = 'error';
                    }
                } else {
                    echo "Error uploading file.";
                }
            }
        }
    }
}
}
?>