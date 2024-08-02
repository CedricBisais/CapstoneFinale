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
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="Capstone2_Logo.png" />

    <!-- Sidebar -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Logo -->
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Socials Logo -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- css and js -->
    <link href="adminUserManagement.css" rel="stylesheet" type="text/css">
    <script src="js/adminDashboard.js"></script>

    <!-- Table and Modal -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
	
    <!-- Search and Pagination CSS JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
   

</head>
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
            <a href="Logout.php" class="nav_link"><i class='bx bx-log-out nav_icon'></i><span class="nav_name">Log out</span></a>
        </nav>
    </div>
    <!-- Container Main start -->
    <div class="height-auto bg-light">
        <h4>User Management</h4>
    </div>
    <!-- Container Main end -->

    
<style>
body {
	color: #566787;
	background: #f5f5f5;
	font-family: 'Varela Round', sans-serif;
	font-size: 13px;
}
.table-responsive {
    margin: 30px 0;
}
.table-wrapper {
	background: #fff;
	padding: 20px 25px;
	border-radius: 3px;
	min-width: 1000px;
	box-shadow: 0 1px 1px rgba(0,0,0,.05);
}
.table-title {        
	padding-bottom: 15px;
	background: #435d7d;
	color: #fff;
	padding: 16px 30px;
	min-width: 100%;
	margin: -20px -25px 10px;
	border-radius: 3px 3px 0 0;
}
.table-title h2 {
	margin: 5px 0 0;
	font-size: 24px;
}
.table-title .btn-group {
	float: right;
}
.table-title .btn {
	color: #fff;
	float: right;
	font-size: 13px;
	border: none;
	min-width: 50px;
	border-radius: 2px;
	border: none;
	outline: none !important;
	margin-left: 10px;
}
.table-title .btn i {
	float: left;
	font-size: 21px;
	margin-right: 5px;
}
.table-title .btn span {
	float: left;
	margin-top: 2px;
}
table.table tr th, table.table tr td {
	border-color: #e9e9e9;
	padding: 12px 15px;
	vertical-align: middle;
}
table.table tr th:first-child {
	width: 60px;
}
table.table tr th:last-child {
	width: 100px;
}
table.table-striped tbody tr:nth-of-type(odd) {
	background-color: #fcfcfc;
}
table.table-striped.table-hover tbody tr:hover {
	background: #f5f5f5;
}
table.table th i {
	font-size: 13px;
	margin: 0 5px;
	cursor: pointer;
}	
table.table td:last-child i {
	opacity: 0.9;
	font-size: 22px;
	margin: 0 5px;
}
table.table td a {
	font-weight: bold;
	color: #566787;
	display: inline-block;
	text-decoration: none;
	outline: none !important;
}
table.table td a:hover {
	color: #2196F3;
}
table.table td a.edit {
	color: #FFC107;
}
table.table td a.delete {
	color: #F44336;
}
table.table td i {
	font-size: 19px;
}
table.table .avatar {
	border-radius: 50%;
	vertical-align: middle;
	margin-right: 10px;
}
.pagination {
	float: right;
	margin: 0 0 5px;
}
.pagination li a {
	border: none;
	font-size: 13px;
	min-width: 30px;
	min-height: 30px;
	color: #999;
	margin: 0 2px;
	line-height: 30px;
	border-radius: 2px !important;
	text-align: center;
	padding: 0 6px;
}
.pagination li a:hover {
	color: #666;
}	
.pagination li.active a, .pagination li.active a.page-link {
	background: #03A9F4;
}
.pagination li.active a:hover {        
	background: #0397d6;
}
.pagination li.disabled i {
	color: #ccc;
}
.pagination li i {
	font-size: 16px;
	padding-top: 6px
}
.hint-text {
	float: left;
	margin-top: 10px;
	font-size: 13px;
}    
/* Custom checkbox */
.custom-checkbox {
	position: relative;
}
.custom-checkbox input[type="checkbox"] {    
	opacity: 0;
	position: absolute;
	margin: 5px 0 0 3px;
	z-index: 9;
}
.custom-checkbox label:before{
	width: 18px;
	height: 18px;
}
.custom-checkbox label:before {
	content: '';
	margin-right: 10px;
	display: inline-block;
	vertical-align: text-top;
	background: white;
	border: 1px solid #bbb;
	border-radius: 2px;
	box-sizing: border-box;
	z-index: 2;
}
.custom-checkbox input[type="checkbox"]:checked + label:after {
	content: '';
	position: absolute;
	left: 6px;
	top: 3px;
	width: 6px;
	height: 11px;
	border: solid #000;
	border-width: 0 3px 3px 0;
	transform: inherit;
	z-index: 3;
	transform: rotateZ(45deg);
}
.custom-checkbox input[type="checkbox"]:checked + label:before {
	border-color: #03A9F4;
	background: #03A9F4;
}
.custom-checkbox input[type="checkbox"]:checked + label:after {
	border-color: #fff;
}
.custom-checkbox input[type="checkbox"]:disabled + label:before {
	color: #b8b8b8;
	cursor: auto;
	box-shadow: none;
	background: #ddd;
}
/* Modal styles */
.modal .modal-dialog {
	max-width: 400px;
}
.modal .modal-header, .modal .modal-body, .modal .modal-footer {
	padding: 20px 30px;
}
.modal .modal-content {
	border-radius: 3px;
	font-size: 14px;
}
.modal .modal-footer {
	background: #ecf0f1;
	border-radius: 0 0 3px 3px;
}
.modal .modal-title {
	display: inline-block;
}
.modal .form-control {
	border-radius: 2px;
	box-shadow: none;
	border-color: #dddddd;
}
.modal textarea.form-control {
	resize: vertical;
}
.modal .btn {
	border-radius: 2px;
	min-width: 100px;
}	
.modal form label {
	font-weight: normal;
}
#password-container {
    position: relative;
}
#togglePassword {
    position: absolute;
    top: 70%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
}	.l-navbar .nav_logo-icon,
        .nav_link i {
            font-size: 30px; /* You can adjust this value to make the icons larger */
        }

        .header .header_toggle i {
            font-size: 30px; /* Adjust this value as needed */
        }
</style>

</head>
<body>

<div class="center">
    <div class="table-responsive">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-sm-6">
                        <h2>Users Management</h2>
                    </div>
                    <div class="col-sm-6">
                        <a href="#BatchUploadModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Batch Upload</span></a>
                        <a href="#CreateUserModal" class="btn btn-success" data-toggle="modal"><i class="material-icons">&#xE147;</i> <span>Add New User</span></a>
                    </div>
                </div>
            </div>
            <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "thesesarchive_db";

            $con = new mysqli($servername, $username, $password, $database);

            if ($con->connect_error) {
                die("Connection failed: " . $con->connect_error);
            }

            // Select user information from tbl_accounts and tbl_account_information
            $sql = "SELECT a.account_id, a.username, at.account_type_description, ai.fName, ai.mName, ai.lName, ai.suffix, ai.email_address
            FROM tbl_accounts a
            INNER JOIN tbl_account_information ai ON a.account_id = ai.account_id
            INNER JOIN tbl_account_type at ON a.account_type_id = at.account_type_id";
    
            $result = $con->query($sql);
            
            // Check for errors in the query
            if (!$result) {
                die("Invalid query: " . $con->error);
            }
            ?>
            <table id="UserTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Account Type</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Suffix</th>
                        <th>Email Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "
                        <tr>
                            <td>{$row['username']}</td>
                            <td>{$row['account_type_description']}</td>
                            <td>{$row['fName']}</td>
                            <td>{$row['mName']}</td>
                            <td>{$row['lName']}</td>
                            <td>{$row['suffix']}</td>
                            <td>{$row['email_address']}</td>
                            <td>
                                <a href='#deleteUserModal' class='deletebtn' data-toggle='modal' data-id='{$row['account_id']}'><i class='material-icons' data-toggle='tooltip' title='Delete'>&#xE872;</i></a>
                            </td>
                        </tr>";
                    }
                    ?>
                </tbody>
            </table>
            <?php
            // Close the database connection
            $con->close();
            ?>
        </div>
    </div>
</div>
<!-- Batch Upload User-->
<div class="modal fade" id="BatchUploadModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Batch Upload</h1>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="batchUpload.php" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
                <input type="file" name="import_file" class="form-control"/>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                <button type="submit" name="batchUpload" class="btn btn-primary">Import</button>
            </div>
            </form>
		</div>
	</div>
</div>
<!-- Add User Modal HTML -->
<div class="modal fade" id="CreateUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Create User</h1>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="adminUserCreate.php" method="POST">
            <div class="modal-body">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Type</label>
                        <div class="col-sm-6">
                            <select class="form-select" name="account_type_id" required>
                                <option value="1">Admin</option>
                                <option value="2">Student</option>
                                <option value="3">Librarian</option>
                                <option value="4">Faculty</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>First Name</label>
                        <input type="text" name="fName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Middle Name</label>
                        <input type="text" name="mName" class="form-control" placeholder="">
                    </div>
                    <div class="mb-3">
                        <label>Last Name</label>
                        <input type="text" name="lName" class="form-control" placeholder="" required>
                    </div>
                    <div class="mb-3">
                        <label>Suffix</label>
                        <input type="text" name="suffix" class="form-control" placeholder="">
                    </div>
                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" name="email_address" class="form-control" placeholder="" required>
                    </div>
                    <div class="mb-3" id="password-container">
                        <label>Password</label>
                        <input type="password" name="password"class="form-control" id="pword" required>
                        <i id="togglePassword" class="fa fa-eye-slash" aria-hidden="true"></i>
                    </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                <button type="submit" name="createUser" class="btn btn-primary">Create User</button>
            </div>
            </form>
		</div>
	</div>
</div>
<!-- Delete Modal HTML -->
<div id="deleteUserModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form action="adminUserDelete.php" method="POST">
				<div class="modal-header">						
					<h4 class="modal-title">Delete User</h4>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<div class="modal-body">		
                    <input type="hidden" name="delete_id" id="delete_id">			
					<p>Are you sure you want to delete these Records?</p>
                    <p class="text-warning" style="color: red !important;"><small>*This action cannot be undone.*</small></p>
                     <!-- Display account summary here -->
                     <div id="accountSummary"></div>
				</div>
				<div class="modal-footer">
					<input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
					<input type="submit" name="deleteData" class="btn btn-danger" value="Delete">
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Add this script at the end of adminUserManagement.php, before </body> -->
<script>
    // Function to fetch and display account summary
    function displayAccountSummary(accountId) {
        // Assuming you have a file named fetchAccountSummary.php to handle the fetching
        var url = "fetchAccountSummary.php?id=" + accountId;

        // Using AJAX to fetch the account summary
        $.ajax({
            type: "GET",
            url: url,
            success: function (data) {
                $("#accountSummary").html(data);
            }
        });
    }

    // Event listener to trigger fetching the account summary before showing the delete modal
    $('#deleteUserModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var accountId = button.data('id'); // Extract account ID from data-id attribute

        // Set the account ID in the hidden input field for deletion
        $("#delete_id").val(accountId);

        // Fetch and display the account summary
        displayAccountSummary(accountId);
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
            <a href="https://www.facebook.com/carmona.sti.edu"><i class="icon fa fa-facebook"></i></a>
            <a href="https://twitter.com/sticollege"><i class="icon fa fa-twitter"></i></a>
            <a href="https://www.youtube.com/user/STIdotEdu"><i class="icon fa fa-youtube"></i></a>
            <a href="https://www.instagram.com/stidotedu/"><i class="icon fa fa-instagram"></i></a>
          </div>
        </div>
        <p class="copyright">STI College Carmona © 2023</p>
      </div>
    </footer>
  </div>


    <script>
        //Delete Button
        $(document).ready(function() {
            $('.deletebtn').on('click',function(){
                $('#deleteUserModal').modal('show');

                $tr= $(this).closest('tr');

                var data = $tr.children("td").map(function() {
                        return $(this).text();
                }).get();
                  // Get the account ID from the data attribute
        var accountId = $(this).data('id');

                // Set the value in the hidden input field
                $('#delete_id').val(accountId);
                $('#account_id').val(data[1])
                $('#username').val(data[2]);
                $('#fName').val(data[3]);
                $('#mName').val(data[4]);
                $('#lName').val(data[5]);
                $('#suffix').val(data[6]);
                $('#email_address').val(data[7]);
                $('#profile_picture').val(data[8]);
            });
        });
        // Password Toggle
        $(document).ready(function() {
            $("#togglePassword").click(function() {
                var passwordField = $("#pword");
                var type = passwordField.attr("type");
        if (type === "password") {
            passwordField.attr("type", "text");
                $("#togglePassword").removeClass("fa-eye-slash");
                $("#togglePassword").addClass("fa-eye");
        } else {
            passwordField.attr("type", "password");
                $("#togglePassword").removeClass("fa-eye");
                $("#togglePassword").addClass("fa-eye-slash");
            }
        });
    });

    // Duplicates Validation
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('createUserForm').addEventListener('submit', function (event) {
            var username = document.getElementById('username').value;
            var email = document.getElementById('email').value;
            var contactNumber = document.getElementById('contact_number').value;

            // Check for duplicate username, email, and contact number
            if (isDuplicate(username, 'username') || isDuplicate(email, 'email') || isDuplicate(contactNumber, 'contact_number')) {
                alert('Duplicate entry found. Please use unique values for username, email, and contact number.');
                event.preventDefault();
            }
        });

        function isDuplicate(value, fieldName) {
            var existingValues = Array.from(document.querySelectorAll('.' + fieldName)).map(function (element) {
                return element.textContent.trim();
            });

            return existingValues.includes(value.trim());
        }
    });
    </script>

    
    <script>let table = new DataTable('#UserTable');</script>
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
</body>
</html>