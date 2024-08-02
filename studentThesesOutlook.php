    <?php
    session_start();

    if (!isset($_SESSION["username"]) && !isset($_SESSION["password"])) {
        header("location:Login.php");
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "thesesarchive_db";

    $connection = new mysqli($servername, $username, $password, $database);

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

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

    $limit = 5;
    $page = isset($_GET['p']) ? $_GET['p'] : 1;
    $offset = $limit * ($page - 1);
    $paginate = " LIMIT {$limit} OFFSET {$offset}";

    $search = "";
    if (isset($_GET['q'])) {
        $keyword = $connection->real_escape_string($_GET['q']);
        $search = " AND (archive_title LIKE '%{$keyword}%' OR archive_abstract LIKE '%{$keyword}%' OR archive_authors LIKE '%{$keyword}%' OR archive_date_of_publication LIKE '%{$keyword}%' OR course_description LIKE '%{$keyword}%')";
    }

    $sqlCount = "SELECT COUNT(*) as count FROM tbl_archives a JOIN tbl_course c ON a.course_id = c.course_id WHERE a.status = 1 {$search}";
    $resultCount = $connection->query($sqlCount);

    if ($resultCount === false) {
        die("Error in count query: " . $connection->error);
    }

    $count_all = $resultCount->fetch_assoc()['count'];

    $pages = ceil($count_all / $limit);

    $sql = "SELECT a.*, c.course_description FROM tbl_archives a JOIN tbl_course c ON a.course_id = c.course_id WHERE a.status = 1 {$search} ORDER BY archive_id DESC {$paginate}";
    $result = $connection->query($sql);

    if ($result === false) {
        die("Error in select query: " . $connection);
    }

    function truncateText($text, $maxLength = 500) {
        // Truncate the text to the specified length
        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength) . '...';
        }
        return $text;
    }
    $filterDate = "";
    if (isset($_GET['publication_date'])) {
        $selectedDate = $connection->real_escape_string($_GET['publication_date']);
        $filterDate = " AND archive_date_of_publication = '{$selectedDate}'";
    }


    $sql = "SELECT * FROM tbl_archives WHERE status = 1 {$search} {$filterDate} ORDER BY archive_id DESC {$paginate}";

    $sql = "SELECT *, c.course_description 
            FROM tbl_archives a
            JOIN tbl_course c ON a.course_id = c.course_id
            WHERE a.status = 1 {$search} {$filterDate} 
            ORDER BY a.archive_id DESC {$paginate}";
    $result = $connection->query($sql);

    $programQuery = "SELECT DISTINCT course_description FROM tbl_course";
    $programResult = $connection->query($programQuery);

    $programs = [];
    while ($programRow = $programResult->fetch_assoc()) {
        $programs[] = $programRow['course_description'];
    }

    $programFilter = isset($_GET['program']) ? $connection->real_escape_string($_GET['program']) : '';
    $programCondition = $programFilter ? " AND c.course_description = '{$programFilter}'" : '';

    $sql = "SELECT a.*, c.course_description 
            FROM tbl_archives a
            JOIN tbl_course c ON a.course_id = c.course_id
            WHERE a.status = 1 {$search} {$filterDate} {$programCondition}
            ORDER BY a.archive_id DESC {$paginate}";

    $result = $connection->query($sql);
  

    ?>
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Thesis Outlook</title>
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
        <link href="studentHomepage.css" rel="stylesheet" type="text/css">
        <script src="js/scrollreveal.min.js"></script>
        <script src="js/studentHome.js"></script>


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
    }.unpublished {
    display: none;
    }

    </style>

    <body>
    <section class="content">
        <div class="container">
            <div class="content py-2">
                <div class="col-12">
                    <div class="card card-outline card-primary shadow rounded-0">
                        <div class="card-body rounded-0">
                            <h2>Archive List</h2>
                            <hr class="bg-navy">
                            <form action="studentThesesOutlook.php" method="GET" id="searchForm" class="mb-3">
                                <!-- Include other filter inputs here -->
                                <div class="input-group mb-3">
                                    <select class="form-select" id="programSelect" name="program" onchange="submitForm()">
                                        <option value="" <?php echo empty($programFilter) ? 'selected' : ''; ?>>All Programs</option>
                                        <?php foreach ($programs as $program) : ?>
                                            <option value="<?php echo $program; ?>" <?php echo ($programFilter == $program) ? 'selected' : ''; ?>><?php echo $program; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <input type="text" name="q" class="form-control" placeholder="Search..."
                                        value="<?php echo isset($_GET['q']) ? $_GET['q'] : ''; ?>">
                                    <!-- Include the selected program in the form submission -->
                                    <input type="hidden" name="program" value="<?php echo $programFilter; ?>">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </form>
                            <?php if (!empty($_GET['q'])) : ?>
                            <h3 class="text-center"><b>Search Result for "<?php echo $keyword; ?>" keyword</b></h3>
                            <?php endif ?>
                            <div class="list-group">
                                <?php if (mysqli_num_rows($result) > 0) : ?>
                                    <?php
                                    while ($row = $result->fetch_assoc()) {
                                        $archiveId = $row['archive_id'];
                                        $title = $row['archive_title'];
                                        $abstract = $row['archive_abstract'];
                                        $authors = $row['archive_authors'];
                                        $date= $row['archive_date_of_publication'];
                                        $status = $row['status'];
                                        $course=$row['course_description'];

                                        echo "<a href='./usersArchiveView.php?id={$archiveId}' class='text-decoration-none text-dark list-group-item list-group-item-action " . ($status == 0 ? 'unpublished' : '') . "'>
                                                <div class='row'>
                                                    <div class='col-lg-4 col-md-5 col-sm-12 text-center'>
                                                    <img src='Uploads/try.jpg' class='banner-img img-fluid bg-gradient-dark' alt='Banner Image'>
                                                    </div>
                                                    <div class='col-lg-8 col-md-7 col-sm-12'>
                                                        <h3 class='text-navy'><b>{$title}</b></h3>
                                                        <h6>Author/s :</h6>
                                                        <p class='truncte-5'>{$authors}</p>
                                                        <h6>Date of Publication:</h6><p class='truncate-5'>{$date}</p>
                                                        <h6>Program:</h6><p class='truncate-5'>{$course}</p>
                                                        <h6>Abstract: </h6><p class='truncate-5'>" . truncateText($abstract) . "</p>                                                </div>
                                                </div>
                                            </a>";
                                    }
                                    ?>
                                <?php else : ?>
                                    <p class="text-center">No results found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer clearfix rounded-0">
                                <div class="col-12">
                                    <div class="row">
                                        <div class="col-md-6"><span class="text-muted">Display Items: <?php echo mysqli_num_rows($result); ?></span></div>
                                        <div class="col-md-6">
                                            <ul class="pagination pagination-sm m-0 float-right">
                                                <?php if ($page > 1) : ?>
                                                    <li class="page-item"><a class="page-link" href="studentThesesOutlook.php?p=<?= $page - 1 . $search ?>">«</a></li>
                                                <?php endif; ?>
                                                <?php for ($i = 1; $i <= $pages; $i++) : ?>
                                                    <li class="page-item <?= $page == $i ? 'active' : '' ?>"><a class="page-link" href="studentThesesOutlook.php?p=<?= $i . $search ?>"><?= $i ?></a></li>
                                                <?php endfor; ?>
                                                <?php if ($page < $pages) : ?>
                                                    <li class="page-item"><a class="page-link" href="studentThesesOutlook.php?p=<?= $page + 1 . $search ?>">»</a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
        <script>

    document.addEventListener("DOMContentLoaded", function() {
            // Sample data (replace this with your actual data)
            var archiveItems = ["Item 1", "Item 2", "Item 3"];

            // Get the list-group element
            var archiveList = document.getElementById("archiveList");

            // Loop through the data and create list items
            archiveItems.forEach(function(itemText) {
                var listItem = document.createElement("a");
                listItem.href = "#";  // Set the href attribute as needed
                listItem.className = "list-group-item list-group-item-action";
                listItem.textContent = itemText;

                // Append the list item to the list-group
                archiveList.appendChild(listItem);
            });

            // Update the display count
            var displayCount = archiveItems.length;
            document.querySelector(".text-muted").textContent = "Display Items: " + displayCount;
        });


    tinymce.init({
    selector: '#abstract',
    height: 300, // Adjust the height as needed
    menubar: false, // Hide the menu bar if desired
    plugins: 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    });
    </script>
     <script>
    function submitForm() {
        // Get the form element by its ID
        var form = document.getElementById('searchForm'); // Replace 'searchForm' with the actual ID of your form

        // Submit the form
        form.submit();
    }
</script>
    </body>
    </html>




