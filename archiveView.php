<?php
$conn = new mysqli('localhost', 'root', '', 'thesesarchive_db');

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT a.* FROM `tbl_archives` a WHERE a.archive_id = '{$_GET['id']}'");

    if ($qry->num_rows) {
        foreach ($qry->fetch_array() as $k => $v) {
            if (!is_numeric($k))
                $$k = $v;
        }

        $submitted = "N/A";
        if (isset($account_id)) {
            $student = $conn->query("SELECT * FROM tbl_account_information WHERE account_id = '{$account_id}'");
            if ($student->num_rows > 0) {
                $res = $student->fetch_array();
                $submitted = $res['email_address'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Web Based Thesis Archive for STI College Carmona</title>
    <link rel="icon" href="Capstone2_Logo.png" />

    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/jqvmap/jqvmap.min.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/dist/css/adminlte.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/dist/css/custom.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/assets/css/styles.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/summernote/summernote-bs4.min.css">
    <link rel="stylesheet" href="http://localhost/CapstoneFinale/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
            
    <!-- Font Awesome -->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet">

    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
     <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
            
    <!-- Your custom CSS and JS files -->
    <link href="studentHomePage.css" rel="stylesheet" type="text/css">
     <script src="js/scrollreveal.min.js"></script>
    <script src="js/studentHome.js"></script>
            

 
<style>
         #header {
            height: 70vh;
            width: calc(100%);
            position: relative;
            top: -1em;
        }

        #header:before {
            content: "";
            position: absolute;
            height: calc(100%);
            width: calc(100%);
            background-image: url(http://localhost/otas/uploads/cover-1638840281.png);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
        }

        #header>div {
            position: absolute;
            height: calc(100%);
            width: calc(100%);
            z-index: 2;
        }

        #top-Nav a.nav-link.active {
            color: #001f3f;
            font-weight: 900;
            position: relative;
        }

        #top-Nav a.nav-link.active:before {
            content: "";
            position: absolute;
            border-bottom: 2px solid #001f3f;
            width: 33.33%;
            left: 33.33%;
            bottom: 0;
        }

        :root {
            --bg-img: url('http://localhost/otas/uploads/cover-1638840281.png');
        }

        #main-header {
            position: relative;
            background: rgb(0, 0, 0) !important;
            background: radial-gradient(circle, rgba(0, 0, 0, 0.48503151260504207) 22%, rgba(0, 0, 0, 0.39539565826330536) 49%, rgba(0, 212, 255, 0) 100%) !important;
        }

        #main-header:before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url(http://localhost/otas/uploads/cover-1638840281.png);
            background-repeat: no-repeat;
            background-size: cover;
            filter: drop-shadow(0px 7px 6px black);
            z-index: -1;
        }

        .user-img {
            position: absolute;
            height: 27px;
            width: 27px;
            object-fit: cover;
            left: -7%;
            top: -12%;
        }

        .btn-rounded {
            border-radius: 50px;
        }

        #login-nav {
            position: fixed !important;
            top: 0 !important;
            z-index: 1037;
            padding: 1em 1.5em !important;
        }

        #top-Nav {
            top: 4em;
        }

        .text-sm .layout-navbar-fixed .wrapper .main-header~.content-wrapper,
        .layout-navbar-fixed .wrapper .main-header.text-sm~.content-wrapper {
            margin-top: calc(3.6) !important;
            padding-top: calc(5em) !important;
        }

        .list-group {
            margin-bottom: 0 !important;
        }

        #document_field {
            min-height: 80vh
        }

    #document_field {
        min-height: 80vh;
    }
    .card-body .container-fluid>fieldset {
            margin-right: 15px; /* Adjust the margin as needed */
        }

        /* Remove margin-right from the last fieldset to prevent extra space */
        .card-body .container-fluid>fieldset:last-child {
            margin-right: 0;
        }.body {
            font-family: 'Times New Roman', Times, serif;
        }
</style>


<div class="content py-4">
    <div class="col-12">
        <div class="card card-outline card-primary shadow rounded-0">
            <div class="card-header">
                <h3 class="card-title">
                    Archive Ascension Number- <?= isset($archive_description) ? $archive_description : "" ?>
                </h3>
            </div>
            <div class="card-body rounded-0">
                <div class="container-fluid">
                    <h2><b><?= isset($archive_title) ? $archive_title : "" ?></b></h2>
                    <small class="text-muted">Uploaded on <?= date("F d, Y h:i A", strtotime($archive_datetime)) ?></small>

                    <?php if (isset($account_id) && $_settings->userdata('login_type') == "2" && $account_id == $_settings->userdata('id')): ?>
                        <div class="form-group">
                            <a href="./?page=submit-archive&id=<?= isset($archive_id) ? $archive_id : "" ?>" class="btn btn-flat btn-default bg-navy btn-sm"><i class="fa fa-edit"></i> Edit</a>
                            <button type="button" data-id="<?= isset($archive_id) ? $archive_id : "" ?>" class="btn btn-flat btn-danger btn-sm delete-data"><i class="fa fa-trash"></i> Delete</button>
                        </div>
                    <?php endif; ?>

                    <fieldset>
                        <legend class="text-navy">Date of Publication:</legend>
                        <div class="pl-4"><large><?= isset($archive_date_of_publication) ? $archive_date_of_publication : "----" ?></large></div>
                    </fieldset>

                    <fieldset>
                        <legend class="text-navy">Abstract:</legend>
                        <div class="pl-4"><large><?= isset($archive_abstract) ? html_entity_decode($archive_abstract) : "" ?></large></div>
                    </fieldset>

                    <fieldset>
                        <legend class="text-navy">Author/s:</legend>
                        <div class="pl-4"><large><?= isset($archive_authors) ? html_entity_decode($archive_authors) : "" ?></large></div>
                    </fieldset>

                    <fieldset>
                        <legend class="text-navy">Project Document:</legend>
                        <div class="pl-4">
                        <iframe src="<?= isset($archive_document) ? $archive_document : "" ?>" frameborder="0" id="document_field" class="text-center w-100">Loading Document ...</iframe>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
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
                    <div class="col item social"><a href="https://www.facebook.com/carmona.sti.edu"><i class="icon bi bi-facebook"></i></a><a href="https://twitter.com/sticollege"><i class="icon bi bi-twitter"></i></a><a href="https://www.youtube.com/user/STIdotEdu"><i class="icon bi bi-youtube"></i></a><a href="https://www.instagram.com/stidotedu/"><i class="icon bi bi-instagram"></i></a></div>
                </div>
                <p class="copyright">STI College Carmona © 2023</p>
            </div>
        </footer>
    </div>

<script>
    // ... (rest of the script) ...
        </script>
    
          
</body>
</html>