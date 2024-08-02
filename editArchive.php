<?php
session_start();

$connection = mysqli_connect("localhost", "root", "", "thesesarchive_db");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['updateData'])) {
    $id = $_POST['update_id'];
    $ascNum = $_POST['archive_description'];
    $thesisTitle =$_POST['archive_title'];

    // Use prepared statement to prevent SQL injection
    $query = "UPDATE tbl_archives SET archive_description='$ascNum', archive_title='$thesisTitle' WHERE archive_id='$id'";
    $query_run = mysqli_query($connection, $query);


        if ($query_run) {
            $_SESSION['status'] = "Data Updated Successfully!";
            $_SESSION['status_code'] = 'success';
            header('Location: librarianArchiveManagement.php');
            exit();
        } else {
            $_SESSION['status'] = "Error updating data: " . mysqli_error($connection);
            $_SESSION['status_code'] = 'error';
        }

    } else {
        $_SESSION['status'] = "Error preparing statement: " . mysqli_error($connection);
        $_SESSION['status_code'] = 'error';
    }


// Close the database connection
mysqli_close($connection);
?>
