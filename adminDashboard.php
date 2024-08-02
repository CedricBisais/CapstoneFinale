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

$stmt = $conn->prepare("SELECT a.*, ai.* FROM tbl_accounts a JOIN tbl_account_information ai ON a.account_id = ai.account_id WHERE a.username = ?");
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

// Function to execute a SQL query and fetch a single value
function fetchSingleValue($sql) {
    global $conn;

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row ? $row['count'] : 0;
    }

    return 0;
}

// Fetch counts from the database
$activeUsersCount = fetchSingleValue("SELECT COUNT(*) AS count FROM tbl_accounts");
$totalArchivesCount = fetchSingleValue("SELECT COUNT(*) AS count FROM tbl_archives");
$publishedArchivesCount = fetchSingleValue("SELECT COUNT(*) AS count FROM tbl_archives WHERE status = 1");
$unpublishedArchivesCount = fetchSingleValue("SELECT COUNT(*) AS count FROM tbl_archives WHERE status = 0");

?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="icon" href="Capstone2_Logo.png" />
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link href="adminDashboard.css" rel="stylesheet" type="text/css">
    <script src="js/adminDashboard.js"></script>
</head>

<style>

    /* adminDashboard.css */

/* Increase font size for the counts */
.fs-3 {
    font-size: 2.5rem;
}

.fs-2 {
    font-size: 2rem;
}

/* Increase padding for the boxes */
.p-5 {
    padding: 2.5rem !important;
}

/* Add some margin between the count and the icon */
.text-center {
    margin-bottom: 1rem;
}

/* Add a subtle box-shadow to the boxes */
.shadow-sm {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

/* Center the icon within the box */
.primary-text {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* Increase the border-radius for a rounded appearance */
.border {
    border-radius: 50%;
}

/* Add a subtle background color to the icons */
.secondary-bg {
    background-color: #f8f9fa;
}

/* Add some margin to the top of the page */
#body-pd {
    margin-top: 3rem;
}

/* Adjust the font size for the header */
.header p {
    font-size: 1.5rem;
}

.bg-gradient-wavy {
        background: linear-gradient(45deg, rgba(54, 209, 220, 0.8), rgba(91, 134, 229, 0.8)); /* Updated gradient colors with opacity */
        position: relative;
        overflow: hidden;
    }

.bg-gradient-wavy::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, rgba(54, 209, 220, 0.8), rgba(91, 134, 229, 0.8)); /* Updated gradient colors with opacity */
    opacity: 0.2; /* Adjust the opacity as needed */
    z-index: -1;
    pointer-events: none;
}.l-navbar .nav_logo-icon,
        .nav_link i {
            font-size: 30px; /* You can adjust this value to make the icons larger */
        }

        .header .header_toggle i {
            font-size: 30px; /* Adjust this value as needed */
        }


</style>

<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"><i class='bx bx-menu' id="header-toggle"></i></div>
        
        <!-- Replace the profile picture with the dropdown menu -->
        <a href="adminUserProfile.php">
                <img src="Uploads/<?php echo $row['profile_picture']; ?>" class="rounded-circle" height="55" width="55" loading="lazy" alt="Profile Picture">
            </a>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div><a href="#" class="nav_logo"><i class='bx bx-layer nav_logo-icon'></i><span class="nav_logo-name">STI Carmona</span></a>
                <div class="nav_list">
                    <a href="adminDashboard.php" class="nav_link active"><i class='bx bx-grid-alt nav_icon'></i><span class="nav_name">Dashboard</span></a>
                    <a href="adminUserManagement.php" class="nav_link"><i class='bx bxs-user-account nav_icon'></i><span class="nav_name">User Management</span></a>
                    <a href="adminArchiveManagement.php" class="nav_link"><i class='bx bx-message-square-detail nav_icon'></i><span class="nav_name">Archive Management</span></a>
                    <a href="adminCourseManagement.php" class="nav_link"><i class='bx bx-bookmark nav_icon'></i><span class="nav_name">Course Management</span></a>
                    <a href="adminReports.php" class="nav_link"><i class='bx bx-flag nav_icon'></i><span class="nav_name">Reports</span></a>
                    <a href="adminUserProfile.php" class="nav_link"><i class='bx bx-user nav_icon'></i><span class="nav_name">Profile</span></a>
                </div>
            </div>
            <a href="Logout.php" class="nav_link"><i class='bx bx-log-out nav_icon'></i><span class="nav_name">Log Out</span></a>
        </nav>
    </div>
    <!-- DASHBOARD -->
    <div class="height-100 bg-light">
        <div class="container-fluid px-4">
        <div class="row g-3 my-2">
                <!-- Dashboard Card 1 -->
                <div class="col-lg-6 col-md-12">
                    <div class="p-5 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded bg-gradient-wavy">
                        <div class="text-center">
                            <h3 class="fs-3"><?php echo $activeUsersCount; ?></h3>
                            <p class="fs-5">Active Users</p>
                        </div>
                        <i class="fas fa-user fs-2 primary-text border rounded-full secondary-bg p-4 text-primary"></i>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="p-5 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded bg-gradient-wavy">
                        <div class="text-center">
                            <h3 class="fs-3"><?php echo $totalArchivesCount; ?></h3>
                            <p class="fs-5">Total Archives</p>
                        </div>
                        <i class="fas fa-archive fs-2 primary-text border rounded-full secondary-bg p-4 text-success"></i>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="p-5 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded bg-gradient-wavy">
                        <div class="text-center">
                            <h3 class="fs-3"><?php echo $publishedArchivesCount; ?></h3>
                            <p class="fs-5">Published Archives</p>
                        </div>
                        <i class="fas fa-upload fs-2 primary-text border rounded-full secondary-bg p-4 text-info"></i>
                    </div>
                </div>  
             

                <div class="col-lg-6 col-md-12">
                    <div class="p-5 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded bg-gradient-wavy">
                        <div class="text-center">
                            <h3 class="fs-3"><?php echo $unpublishedArchivesCount; ?></h3>
                            <p class="fs-5">Unpublished Archives</p>
                        </div>
                        <i class="fas fa-arrow-down fs-2 primary-text border rounded-full secondary-bg p-4 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>