<?php

$accountId = $_GET['id'];

$servername = "localhost";
$username = "root";
$password = "";
$database = "thesesarchive_db";

// Create a connection
$con = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Fetch account information based on the account ID
$sql = "SELECT * FROM tbl_account_information WHERE account_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $accountId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Display account summary
echo "<p><strong>First Name:</strong> {$row['fName']}</p>";
echo "<p><strong>Middle Name:</strong> {$row['mName']}</p>";
echo "<p><strong>Last Name:</strong> {$row['lName']}</p>";
echo "<p><strong>Suffix:</strong> {$row['suffix']}</p>";
echo "<p><strong>Email Address:</strong> {$row['email_address']}</p>";

// Close the database connection
$stmt->close();
$con->close();
?>