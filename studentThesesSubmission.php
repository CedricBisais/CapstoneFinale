<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page
    header("location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$database = "thesesarchive_db";

$connection = new mysqli($servername, $username, $password, $database);
$stmt = $connection->prepare("SELECT a.*, ai.* FROM tbl_accounts a JOIN tbl_account_information ai ON a.account_id = ai.account_id WHERE a.username = ?");
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

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

function getNextAscensionID($connection) {
    $query = "SELECT MAX(ascension_id) as max_id FROM tbl_ascension";
    $result = $connection->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        return $row['max_id'] + 1;
    } else {
        return 1; // Default to 1 if there's an issue fetching the max ID
    }
}
function getAscensionDescription($ascensionID) {
    // Format the ascension_description with leading zeros
    return str_pad($ascensionID, 3, '0', STR_PAD_LEFT);
}
function isThesisTitleExists($connection, $thesisTitle) {
    $query = "SELECT COUNT(*) as count FROM tbl_archives WHERE archive_title = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $thesisTitle);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $stmt->close();
    return $count > 0;
}


$ascensionID = getNextAscensionID($connection); // Assuming $connection is the mysqli connection object
$archiveDescription = getAscensionDescription($ascensionID);

$thesisTitle = $authors = $course = $dateOfPublication = $abstract = $targetFile = $errorMessage = "";
$submissionMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $thesisTitle = $_POST["archive_title"];
    $authors = $_POST["archive_authors"];
    $course = $_POST["course"];
    $dateOfPublication = $_POST["date_of_publication"];
    $abstract = $_POST["abstract"];


    if (isThesisTitleExists($connection, $thesisTitle)) {
        $_SESSION['status'] = "Error: Thesis with the same title already exists.";
        $_SESSION['status_code'] = 'error';
        
    }   else {
        if (isset($_FILES["thesisFile"])) {
        $targetDir = "Uploads/Archives/";
        $targetFile = $targetDir . basename($_FILES["thesisFile"]["name"]);
    
        // Check if the file already exists
        if (file_exists($targetFile)) {
            $_SESSION['status'] = "Error: The file already exists.";
            $_SESSION['status_code'] = 'error';
        } else {
            if ($_FILES["thesisFile"]["error"] == UPLOAD_ERR_OK) {
                if (move_uploaded_file($_FILES["thesisFile"]["tmp_name"], $targetFile)) {
                    // Insert data into tbl_ascension to get the next ascension_id
                    $queryAscension = "INSERT INTO tbl_ascension (ascension_id) VALUES (NULL)";
                    $resultAscension = $connection->query($queryAscension);

                    if ($resultAscension) {
                        // Get the last inserted ascension_id
                        $ascensionID = $connection->insert_id;

                        // Use the obtained ascension_id to insert into tbl_archives
                        $queryArchives = "INSERT INTO tbl_archives (ascension_id, archive_description, archive_title, archive_authors, course_id, archive_date_of_publication, archive_abstract, archive_document, status) 
                                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";

                        $stmtArchives = $connection->prepare($queryArchives);

                        if ($stmtArchives) {
                            $stmtArchives->bind_param("isssssss", $ascensionID, $archiveDescription, $thesisTitle, $authors, $course, $dateOfPublication, $abstract, $targetFile);

                            if ($stmtArchives->execute()) {
                                // Data inserted successfully
                                $_SESSION['status'] = "Thesis submitted successfully!";
                                $_SESSION['status_code'] = 'success';
                                $thesisTitle = $authors = $course = $dateOfPublication = $abstract = $targetFile = "";
                            } else {
                                // Error in executing the statement for tbl_archives
                                $_SESSION['status'] = "Error: Unable to submit thesis." . $stmtArchives->error;
                                $_SESSION['status_code'] = 'error';
                            }

                            $stmtArchives->close();
                        } else {
                            // Error in preparing the statement for tbl_archives
                            die("Error in preparing the statement for tbl_archives: " . $connection->error);
                        }
                    } else {
                        // Error in executing the statement for tbl_ascension
                        $_SESSION['status'] = "Error: Unable to submit thesis. " . $connection->error;
                        $_SESSION['status_code'] = 'error';
                    }
                } else {
                    echo "Error uploading file.";
                }
            }
        }
    }
}
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Thesis Submit</title>
            <link rel="icon" href="Capstone2_Logo.png" />
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
    <link href="studentHomePage.css" rel="stylesheet" type="text/css">
    <script src="Js/scrollreveal.min.js"></script>
    <script src="Js/studentHome.js"></script>

    <!-- Theses Submission -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Tiny MCE -->
    <script src="https://cdn.tiny.cloud/1/yf3ltynrh66mirvm6k5x58l8mdk72raowlpe51g7t6ako7k0/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    


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
    <a class="navbar-brand mt-2 mt-lg-0" href="studentHomepage.php">
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
</head>

<style>
/* Center the form within the card body */
.container-fluid2 {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
}

/* Center text and form elements within their containers */
.card-title {
    text-align: center;
}.card-outline {
    padding: 20px; /* Adjust the padding as needed */
    margin: 20px; /* Optional: Add margin for spacing */
}


</style>

<body>
    
<div class="content py-4">
    <div class="card card-outline card-primary shadow rounded-0">
        <div class="card-header rounded-0">
            <h5 class="card-title">Submit Thesis</h5>
        </div>
        <div class="card-body rounded-0">
            <div class="container-fluid2">
                <form action="" id="archive-form"  method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="">
                    <div class="row">
                
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="title" class="control-label text-navy">Thesis Title</label>
                                <input type="text" name="archive_title" autofocus placeholder="Thesis Title" class="form-control form-control-border" value="<?php echo $thesisTitle; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="author" class="control-label text-navy">Author/s</label>
                                    <textarea rows="3" name="archive_authors" placeholder="Author/s" class="form-control form-control-border summernote" required><?php echo $authors; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="course" class="control-label text-navy">Program</label>
                                        <select name="course" class="form-control form-control-border" required>
                                        <?php
                                        $queryCourses = "SELECT course_id, course_description FROM tbl_course";
                                        $resultCourses = $connection->query($queryCourses);

                                        // Check if the query was successful
                                        if ($resultCourses) {
                                            $courses = $resultCourses->fetch_all(MYSQLI_ASSOC);
                                        } else {
                                            // Handle the error
                                            die("Error fetching courses: " . $connection->error);
                                        }

                                        // Iterate through the courses and populate the options
                                        foreach ($courses as $course) {
                                            $courseId = $course['course_id'];
                                            $courseName = $course['course_description'];
                                            echo "<option value=\"$courseId\"";
                                            if ($courseId == $course) {
                                                echo 'selected';
                                            }
                                            echo ">$courseName</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="date_of_publication" class="control-label text-navy">Date of Publication</label>
                                <input type="date" class="form-control" name="date_of_publication" value="<?php echo $dateOfPublication; ?>" required max="<?php echo date('Y-m-d'); ?>">
                        </div>      
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="Abstract" class="control-label text-navy">Abstract</label>
                                <textarea rows="15" name="abstract"  placeholder="abstract" class="form-control" value="<?php echo $abstract; ?>" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="thesisFile" class="control-label text-muted">Thesis Document (PDF File Only)</label>
                                <input type="file" name="thesisFile" class="form-control form-control-border" accept="application/pdf" value="<?php echo $targetFile; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group text-center">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <button type="button" onclick="resetForm()" class="btn btn-danger">Cancel</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function resetForm() {
        document.getElementById("archive-form").reset();
    }
</script>
<script src="js/sweetalert.js"></script>
<?php 
        if(isset($_SESSION['status']) && $_SESSION['status'] !== '')
            {
                ?>
             <script> swal({
                title: "<?php echo $_SESSION['status']; ?>",
                // text: "You clicked the button!",
                icon: "<?php echo $_SESSION['status_code']; ?>",
                button: "OK",
                }); 
             </script>
                <?php
                unset($_SESSION['status']);
            }
    ?>
<script>
   

        tinymce.init({
            selector: '#abstract',
            height: 300,
            menubar: false,
            plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        });
   
</script>
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