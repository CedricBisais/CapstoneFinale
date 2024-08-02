<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "thesesarchive_db";

    // Create connection
    $connection = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Retrieve form data
    $courseDescription = $_POST['course'];
    $departmentDescription = $_POST['department'];

    // Insert new course into the database
    $insertSql = "INSERT INTO tbl_course (course_description, department_id) 
                  VALUES ('$courseDescription', (SELECT department_id FROM tbl_department WHERE department_description = '$departmentDescription'))";

    if ($connection->query($insertSql) === TRUE) {
        // Course added successfully
        session_start();
        $_SESSION['status'] = "Course added successfully!";
        $_SESSION['status_code'] = "success";
        header("Location: adminCourseManagement.php");
        exit();
    } else {
        // Error adding course
        session_start();
        $_SESSION['status'] = "Error: " . $connection->error;
        $_SESSION['status_code'] = "error";
        header("Location: adminCourseManagement.php");
        exit();
    }

    // Close the database connection
    $connection->close();
} else {
    // Redirect if accessed without POST data
    header("Location: adminCourseManagement.php");
    exit();
}
?>