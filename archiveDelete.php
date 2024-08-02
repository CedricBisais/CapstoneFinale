<?php
session_start();

$connection = mysqli_connect("localhost", "root", "", "thesesarchive_db");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['deleteData'])) {
    $id = mysqli_real_escape_string($connection, $_POST['delete_id']);

    // Check if the record exists in tbl_archives
    $checkRecordQuery = "SELECT * FROM tbl_archives WHERE archive_id = '$id'";
    $checkRecordResult = mysqli_query($connection, $checkRecordQuery);

    if (mysqli_num_rows($checkRecordResult) > 0) {
        $row = mysqli_fetch_assoc($checkRecordResult);
        $documentPath = $row['archive_document'];

        // Delete related records in tbl_archive_asc_detail and tbl_reports
        // Add appropriate delete queries and error handling here if needed

        // Delete the associated file
        if (file_exists($documentPath)) {
            unlink($documentPath);
        }

        // Delete the record from tbl_archives
        $deleteQueryArchives = "DELETE FROM tbl_archives WHERE archive_id = '$id'";
        $deleteQueryAscension = "DELETE FROM tbl_ascension WHERE ascension_id='$id'";

        $queryRunArchives = mysqli_query($connection, $deleteQueryArchives);
        $queryRunAscension = mysqli_query($connection, $deleteQueryAscension);

        if ($queryRunArchives && $queryRunAscension) {
            $_SESSION['status'] = "Data Deleted Successfully!";
            $_SESSION['status_code'] = 'success';
            header('Location: librarianArchiveManagement.php');
            exit();
        } else {
            $_SESSION['status'] = "Data Deletion Failed: " . mysqli_error($connection);
            $_SESSION['status_code'] = 'error';
            echo "Error deleting data: " . mysqli_error($connection);
        }
    } else {
        $_SESSION['status'] = "Record not found!";
        $_SESSION['status_code'] = 'error';
        echo "Record not found!";
    }
}

// Close the database connection
mysqli_close($connection);
?>
