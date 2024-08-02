<?php
// getDepartments.php

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

// Retrieve the selected course from the AJAX request
$selectedCourse = $_POST['course'];

// Fetch departments based on the selected course
$sql = "SELECT department_description FROM tbl_department WHERE department_id IN (
            SELECT department_id FROM tbl_course WHERE course_description = '$selectedCourse'
        )";

$result = $connection->query($sql);

if ($result->num_rows > 0) {
    // Output options for the department dropdown
    while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['department_description'] . "'>" . $row['department_description'] . "</option>";
    }
} else {
    echo "<option value=''>No departments found</option>";
}

// Close the database connection
$connection->close();
?>