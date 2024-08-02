<?php
$connection = mysqli_connect("localhost", "root", "", "thesesarchive_db");

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['deleteData'])) {
    $id = $_POST['delete_id'];

    $query = "DELETE FROM tbl_course WHERE course_id='$id'";
    $query_run = mysqli_query($connection, $query);

    if ($query_run) {
        echo '<script> alert("Data Deleted"); </script>';
        header('Location: adminCourseManagement.php');
    } else {
        echo '<script> alert("Data not Deleted"); </script>';
    }
}

mysqli_close($connection);
?>