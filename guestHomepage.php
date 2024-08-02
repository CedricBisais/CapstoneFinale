<?php
session_start();


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "thesesarchive_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
  // Redirect to login
  header('Location: Login.php');
  exit;
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


?>

<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Homepage</title>


    <!-- Socials Logo -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">

    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

    <!-- css and js -->
    <link href="studentHomePage.css" rel="stylesheet">
    <script src="Js/scrollreveal.min.js"></script>
    <script src="Js/studentHome.js"></script>



    <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <!-- Container wrapper -->
  <div class="container-fluid">
    <!-- Toggle button -->
    <button
      class="navbar-toggler"
      type="button"
      data-mdb-toggle="collapse"
      data-mdb-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <i class="fas fa-bars"></i>
    </button>

    <!-- Collapsible wrapper -->
  <header>
    <nav class="nav container">
    <a class="navbar-brand mt-2 mt-lg-0" href="guestHomePage.php">
        <img src="STI LOGO.png" height="65" alt="MDB Logo" loading="lazy"
        />
      </a>
                <div class="nav__menu" id="nav-menu">
                    <ul class="nav__list me-auto mb-2 mb-lg-0">
                        <li class="nav__item">
                            <a class="nav-link" href="guestHomepage.php">Home</a>
                        </li>
                        <li class="nav__item">
                            <a class="nav-link" href="guestThesesOutlook.php">Theses Outlook</a>
                        </li>
                    </ul>

                    <div class="nav__close" id="nav-close">
                        <i class='bx bx-x'></i>
                    </div>
                </div>

                <!-- Toggle button -->
                <div class="nav__toggle" id="nav-toggle">
                    <i class='bx bx-grid-alt'></i>
                </div>
            </nav>

     <style>
     .home__img {
    border-radius: 30%;
    overflow: hidden;

     }.home__data {
        border: 5px solid #ccc;
        border-radius: 10px;
        padding: 20px;
        margin: 20px 0;
    }  .main {
            background-image: url('Library.jpg'); /* Replace 'path/to/your/image.jpg' with the actual path to your background image */
            background-size: cover; /* Adjust the background size as needed */
            background-position: center; /* Adjust the background position as needed */
            color: #fff; /* Set the text color to ensure readability on the background image */
            padding: 40px; /* Add padding for better content visibility */
        }

    </style>
  </header>
    <!-- Collapsible wrapper -->

    <!-- Right elements -->
    <div class="d-flex align-items-center">
      <!-- Avatar -->
      <div class="dropdown">
        <a class="dropdown-toggle d-flex align-items-center hidden-arrow" href="#" id="navbarDropdownMenuAvatar" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
        <img src="Uploads/<?php echo $row['profile_picture']; ?>" class="rounded-circle" class="rounded-circle" height="70" width="70" loading="lazy"/>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuAvatar" >
          <li>
            <a class="dropdown-item" href="guestUserProfile.php">My profile</a>
          </li>
          <li>
            <a class="dropdown-item" href="Logout.php">Logout</a>
          </li>
        </ul>
      </div>
    </div>
    <!-- Right elements -->
  </div>
  <!-- Container wrapper -->
</nav>
<!-- Navbar -->

  <!--==================== MAIN ====================-->
  <main class="main">
            <!--==================== HOME ====================-->
            <section class="home">
                <div class="home__container container">
                    <div class="home__data">
                        <span class="home__subtitle">Welcome, <?php echo ucfirst($_SESSION['username']); ?>!</span>
                        <h1 class="home__title">Hey STIer!</h1>
                        <p class="home__description">
                        Welcome to the STI College Carmona Library Online! Dive into a world of knowledge right from your screen.  Our virtual library is a digital haven for STI students, offering a vast collection of e-books, research materials, and academic resources. Explore, learn, and excel with easy access to a wealth of information. The STI College Library Online is your gateway to a seamless and enriching learning experience. Welcome to a new chapter of digital discovery!
                        </p>
                    </div>

                    <div class="home__img">
                        <img src="STI LOGO.png" alt="">
                        <div class="home__shadow"></div>
                    </div>
                </div>
            </section>
    </main>
</head>
<body>
    <div class="footer-dark">
        <footer>
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 col-md-3 item">
                        <h3>STI College Carmona</h3>
                        <ul>
                            <li>Location: Lot 2A Brgy. Maduya, Carmona, Cavite, Carmona, Philippines</li>
                            <li>Phone: (046) 430 1671</li>
                        </ul>
                    </div>
                    <div class="col-sm-6 col-md-3 item">
                        <h3>About</h3>
                        <ul>
                            <li><a href="#">Company</a></li>
                            <li><a href="#">Team</a></li>
                            <li><a href="#">Careers</a></li>
                        </ul>
                    </div>
                    <div class="col-md-6 item text">
                        <h3>School Name</h3>
                        <p>Praesent sed lobortis mi. Suspendisse vel placerat ligula. Vivamus ac sem lacus. Ut vehicula rhoncus elementum. Etiam quis tristique lectus. Aliquam in arcu eget velit pulvinar dictum vel in justo.</p>
                    </div>
                    <div class="col item social"><a href="https://www.facebook.com/carmona.sti.edu"><i class="bi bi-facebook"></i></a><a href="https://twitter.com/sticollege"><i class="bi bi-twitter"></i></a><a href="https://www.youtube.com/user/STIdotEdu"><i class="bi bi-youtube"></i></a><a href="https://www.instagram.com/stidotedu/"><i class="bi bi-instagram"></i></a></div>
                </div>
                <p class="copyright">STI College Carmona Â© 2023</p>
            </div>
        </footer>
    </div>
</body>
</html>