<?php

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$con = new mysqli('localhost', 'root', '', 'thesesarchive_db');

session_start();

// checks if connected to the database
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Set session timeout (e.g., 30 minutes)
$sessionTimeout = 30; // 30 minutes in seconds

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // validate data for login
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $username = validate($_POST['username']);
    $password = validate($_POST['password']);

    // checks username field
    if (empty($username) || empty($password)) {
        header("Location: login.php?error=Username and password are required");
        exit();
    } else {
        // check if the username exists
        $sql = "SELECT A.*, T.account_type_description, IFNULL(TIME_TO_SEC(TIMEDIFF(NOW(), A.creation_time)), 0) AS account_age_seconds
                FROM tbl_accounts A
                INNER JOIN tbl_account_type T ON A.account_type_id = T.account_type_id
                WHERE A.username = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $storedPassword = $row['password'];

            // Verify the user's input password using password_verify
            if (password_verify($password, $storedPassword)) {
                // Password is correct
                $_SESSION["user_id"] = $row["account_id"];
                $_SESSION["username"] = $username;
                $_SESSION["account_type_id"] = $row["account_type_id"];
                $_SESSION["account_type"] = $row["account_type_description"];

                // Check if the account type is 'Guest' and has expired
                if ($_SESSION["account_type"] === 'Guest') {
                    $accountAgeSeconds = $row['account_age_seconds'];
                    $maxGuestAccountDuration = 30; // 30 minutes in seconds

                    if ($accountAgeSeconds > $maxGuestAccountDuration) {
                        // Delete the guest account
                        $deleteSql = "DELETE FROM tbl_accounts WHERE account_id = ?";
                        $deleteStmt = $con->prepare($deleteSql);
                        $deleteStmt->bind_param("i", $_SESSION["user_id"]);
                        $deleteStmt->execute();
                        
                        $expirationMessage = '<div class="alert alert-danger">The account has expired</div>';
                    }
                }

                // Define an associative array mapping account types to their respective pages
                $accountTypePages = [
                    'Admin' => 'adminDashboard.php',
                    'Student' => 'studentHomePage.php',
                    'Librarian' => 'librarianArchiveManagement.php',
                    'Guest' => 'guestHomepage.php',
                    'Faculty' => 'facultyHomepage.php',
                    // Add more account types as needed
                ];

                // Check if the account type exists in the array
                if (array_key_exists($_SESSION["account_type"], $accountTypePages)) {
                    header("location: " . $accountTypePages[$_SESSION["account_type"]]);
                    exit();
                } else {
                    // Handle other user types as needed
                    header("location: unknownUser.php");
                    exit();
                }
            } else {
                // Incorrect password
                header("Location: login.php?error=Incorrect Password");
                exit();
            }
        } else {
            // User not found
            header("Location: login.php?error=User not found or the user has been expired");
            exit();
        }
    }
}

// Session timeout handling
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionTimeout)) {
    // Expire the session
    session_unset();
    session_destroy();
    $expirationMessage = '<div class="alert alert-danger">Session expired. Please log in again.</div>';
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>