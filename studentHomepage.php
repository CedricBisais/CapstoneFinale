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
    <link rel="icon" href="Capstone2_Logo.png" />

    <!-- Socials Logo -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->

    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>

    <!-- css and js -->
    <link href="studentHomepage.css" rel="stylesheet" type="text/css">
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
    <!-- Collapsible wrapper -->
    <header>
    <nav class="nav container">
    <a class="navbar-brand mt-2 mt-lg-0" href="studentHomePage.php">
        <img src="STI LOGO.png" height="65" alt="MDB Logo" loading="lazy"
        />
      </a>
                <div class="nav__menu" id="nav-menu">
                    <ul class="nav__list me-auto mb-2 mb-lg-0">
                        <li class="nav__item">
                            <a class="nav-link" href="studentHomePage.php">Home</a>
                        </li>
                        <li class="nav__item">
                            <a class="nav-link" href="studentThesesSubmission.php">Theses Submission</a>
                        </li>
                        <li class="nav__item">
                            <a class="nav-link" href="studentThesesOutlook.php">Theses Outlook</a>
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
  </header>
    <!-- Collapsible wrapper -->
    <style>
        
        </style>
    <!-- Right elements -->
    <div class="d-flex align-items-center">
    
      <!-- Avatar -->
      <div class="dropdown">
        <a class="dropdown-toggle d-flex align-items-center hidden-arrow" href="#" id="navbarDropdownMenuAvatar" role="button" data-mdb-toggle="dropdown" aria-expanded="false">
        <img src="Uploads/<?php echo $row['profile_picture']; ?>" class="rounded-circle" class="rounded-circle" height="55" width="55" loading="lazy"/>
        </a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuAvatar" >
          <li>
            <a class="dropdown-item" href="userProfile.php">My profile</a>
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
        <!-- Home section -->
        <section class="home">
            <div class="home__container container">
                <div class="home__data">
                    <h1 class="home__subtitle">Welcome, <?php echo ucfirst($row['fName']); ?>!</h1>
                    <!-- Display additional user information if needed -->
                    <h1 class="home__title">Hey STIer!</h1>
                    <p class="home__description">
                        Welcome to the STI College Carmona Thesis Archive! Dive into a world of knowledge right from your
                        screen. Our virtual library is a digital haven for STI students, offering a vast collection of
                        academic resources. Explore, learn, and excel with easy access to
                        a wealth of information. The STI College Carmona Thesis Archive is your gateway to a seamless and enriching
                        learning experience. Welcome to a new chapter of digital discovery!
                    </p>
                    <a href="studentThesesOutlook.php" class="home__button">Get Started</a>
                </div>

                <div class="home__img">
                    <img src="book.jpg" alt="">
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
              <li><a href="https://www.sti.edu/stiedu-disclosures_details.asp">Company</a></li>
              <li><a href="https://www.sti.edu/careers.asp">Team</a></li>
              <li><a href="https://www.sti.edu/blog1.asp">BLOG</a></li>
            </ul>
          </div>
          <div class="col-md-6 item text">
            <h3>Privacy Policy</h3>
            <p>We respect the fundamental rights of all individuals to the privacy of their personal data, and we commit to the responsible and lawful treatment of all personal data we handle. Moreover, we aim to comply with the requirements of all relevant personal data privacy and protection laws, particularly the Data Privacy Act of 2012 (DPA) and its implementing rules and regulations, while upholding our legitimate interests and effectively carrying out our responsibilities as an educational institution.
          </div>
          <div class="col item social">
            <a href="https://www.facebook.com/carmona.sti.edu"><i class="icon bi bi-facebook"></i></a>
            <a href="https://twitter.com/sticollege"><i class="icon bi bi-twitter"></i></a>
            <a href="https://www.youtube.com/user/STIdotEdu"><i class="icon bi bi-youtube"></i></a>
            <a href="https://www.instagram.com/stidotedu/"><i class="icon bi bi-instagram"></i></a>
          </div>
        </div>
        <p class="copyright">STI College Carmona © 2023</p>
      </div>
    </footer>
  </div>
</body>
</html>