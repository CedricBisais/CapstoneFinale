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

// Using a prepared statement to retrieve user information
$sql = "SELECT ai.*, a.username, ai.email_address, at.account_type_description, ai.profile_picture
        FROM tbl_account_information ai
        INNER JOIN tbl_accounts a ON ai.account_id = a.account_id
        INNER JOIN tbl_account_type at ON a.account_type_id = at.account_type_id
        WHERE a.username = ?";

$stmt = $conn->prepare($sql);

// Check if the prepared statement is successful
if (!$stmt) {
    die("Error in SQL statement: " . $conn->error);
}

$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $username = $row['username'];
    $accountType = $row['account_type_description'];
    $fName = $row['fName'];
    $lName = $row['lName'];
    $suffix = $row['suffix'];
    $email_address = $row['email_address'];
    $profilePicture = $row['profile_picture']; // Added this line to fetch the profile picture
    // Add other fields as needed
} else {
    // Handle the case where user data is not found
    $username = "N/A";
    $accountType = "N/A";
    $fName = "N/A";
    $lName = "N/A";
    // Add other default values as needed
}

$stmt->close();
$conn->close();
?>


    <!DOCTYPE html>
    <html>
    <head>
    <link rel="icon" href="Capstone2_Logo.png" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="adminDashboard.css" rel="stylesheet" type="text/css">
        <script src="Js/adminDashboard.js"></script>

        <!-- Modal -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

        <!-- Sweetalert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

        <!-- MDB -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.css" rel="stylesheet">
        
   
        


        
    </head>
    <body id="body-pd">
        <header class="header" id="header">
            <div class="header_toggle"><i class='bx bx-menu' id="header-toggle"></i></div>
            <a href="adminUserProfile.php">
                <img src="Uploads/<?php echo $row['profile_picture']; ?>" class="rounded-circle" height="55" width="55" loading="lazy" alt="Profile Picture">
            </a>        </header>
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
        <div class="height-30 bg-light">
            <h4>User Profile</h4>
        </div>
        <!-- Container Main end -->


        <style>
     /* Center the form within the card body */
     .container-fluid2 {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
    }

    
    .card-title {
        text-align: center;
    }

   
    .card-outline {
        border: 15px solid #ccc;
    }

    .user-info-card {
        border: 3px solid #ccc;
        padding: 15px;
        border-radius: 10px;
        margin-top: 20px;
        width: 100%; 
    }
    @media (min-width: 768px) {
      
        .user-info-card {
            width: 70%; 
        }
    }

    @media (max-width: 768px) {
        
        .user-info-card {
            width: 90%; 
            margin: 10px auto; 
        }
    }
        .student-img {
            margin: 0; 
    }.l-navbar .nav_logo-icon,
        .nav_link i {
            font-size: 30px; /* You can adjust this value to make the icons larger */
        }

        .header .header_toggle i {
            font-size: 30px; /* Adjust this value as needed */
        }
        
        
    </style> 
    <div class="content py-4">
    <div class="card card-outline card-primary shadow rounded-0">
        <div class="card-header rounded-0">
            <h5 class="card-title">Your Information:</h5>
        </div>
        <div class="card-body rounded-0">
            <div class="container-fluid">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-lg-4 col-sm-12">
                            <div class="image-container">
                            <img src="Uploads/<?php echo isset($profilePicture) ? $profilePicture : 'default_profile.jpg'; ?>" alt="Profile Picture" class="img-fluid student-img bg-gradient-dark border" height="400" width="400">
                            </div>
                        </div>
                        <div class="col-lg-8 col-sm-12">
                            <div class="user-info-card">
                                <dl>
                                    <dt class="text-navy">Username:</dt>
                                    <dd class="pl-4"><?php echo $username; ?></dd>
                                    <dt class="text-navy">First Name:</dt>
                                    <dd class="pl-4"><?php echo $fName; ?></dd>
                                    <dt class="text-navy">Last Name:</dt>
                                    <dd class="pl-4"><?php echo $lName . ' ' . $suffix; ?></dd>
                                    <dt class="text-navy">Email:</dt>
                                    <dd class="pl-4"><?php echo $row['email_address']; ?></dd>
                                    <dt class="text-navy">Type:</dt>
                                    <dd class="pl-4"><?php echo $accountType; ?></dd>
                                </dl>
                                <a href="#updateProfile" class="updateProfile btn btn btn-default bg-navy btn-flat editbtn" data-toggle='modal'> <i class="fa fa-edit" data-toggle='tooltip'></i> Update Profile</a>
                                <a href="#changePassword" class="changePassword btn btn btn-default bg-navy btn-flat editbtn" data-toggle='modal'> <i class="fa fa-key" data-toggle='tooltip'></i> Change Password</a>
                                <a href="#profilePicture" class="profilePicture btn btn btn-default bg-navy btn-flat editbtn" data-toggle='modal'> <i class="fa fa-user" data-toggle='tooltip'></i> Profile Picture</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        <style>
            .modal-xl {
                    max-width: 50%;
                }

                /* Center the modal vertically */
                .modal.vertical-alignment {
                    top: 50%;
                    transform: translateY(-50%);
                }
        </style>

                <!-- Modal Update Profile-->            
                <div class="modal fade" id="updateProfile" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Update Profile</h1>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form action="adminUserEditProfile.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>First Name</label>
                        <input type="text" name="fName" class="form-control" value="<?php echo $fName; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Last Name</label>
                        <input type="text" name="lName" class="form-control" value="<?php echo $lName; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Suffix</label>
                        <input type="text" name="suffix" class="form-control" value="<?php echo $suffix; ?>">
                    </div>
                    <div class="mb-3">
                        <label>Email address</label>
                        <input type="email" name="email_address" class="form-control" value="<?php echo $email_address; ?>" required onsubmit="return handleSubmit();">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                    <button type="submit" name="updateData" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <script>
        // Function to display SweetAlert for username already taken
        function showUsernameTakenAlert() {
            console.log("showUsernameTakenAlert called");  // Debugging statement
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'The new username is already taken. Please choose a different one.',
            });
        }

        // Function to display SweetAlert for update success
        function showUpdateSuccessAlert() {
            console.log("showUpdateSuccessAlert called");  // Debugging statement
            Swal.fire({
                icon: 'success',
                title: 'Update Successful',
                text: 'Your information has been updated successfully!',
            });
        }

        // Check if there is a session variable indicating the username check result
        document.addEventListener("DOMContentLoaded", function () {
            console.log("DOMContentLoaded event fired");  // Debugging statement
            <?php
                if (isset($_SESSION['usernameCheckResult']) && $_SESSION['usernameCheckResult'] === 'taken') {
                    echo 'showUsernameTakenAlert();';
                    // Clear the session variable after displaying the message
                    unset($_SESSION['usernameCheckResult']);
                }
            ?>

            // Check if there is a session variable indicating a successful update
            <?php
                if (isset($_SESSION['updateResult']) && $_SESSION['updateResult'] === 'success') {
                    echo 'showUpdateSuccessAlert();';
                    // Clear the session variable after displaying the message
                    unset($_SESSION['updateResult']);
                }
            ?>
        });
    </script>

                <!-- Modal Change Password-->
        <div class="modal fade" id="changePassword" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Change Password</h1>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <form action="adminUserChangePassword.php" method="POST">
                <div class="modal-body">
            <div class="mb-3">
                <label for="oldpassword">Old Password</label>
                <div class="input-group">
                    <input type="password" id="oldpassword" name="oldpassword" class="form-control" required>
                    <button class="btn btn-outline-secondary" type="button" id="toggleOldPassword" title="Toggle Password Visibility" >
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <div class="mb-3">
                <label for="password">New Password</label>
                <div class="input-group">
                    <input type="password" id="password" name="newpassword" class="form-control"  required >
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" title="Toggle Password Visibility" >
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
            <div class="mb-3">
                <label for="cpassword">Confirm Password</label>
                <div class="input-group">
                    <input type="password" id="cpassword" name="confirmpassword" class="form-control" required >
                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword" title="Toggle Password Visibility" >
                        <i class="fa fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
                <div class="modal-footer">
                    <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                    <button type="submit" name="updatePassword" class="btn btn-primary">Update</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Check if there is a session variable indicating the result of the password change
        const passwordChangeResult = <?php echo isset($_SESSION['passwordChangeResult']) ? json_encode(htmlspecialchars($_SESSION['passwordChangeResult'])) : 'null'; ?>;
        
        // Check if there is a session variable indicating the result of the profile update
        const profileUpdateResult = <?php echo isset($_SESSION['profileUpdateResult']) ? json_encode(htmlspecialchars($_SESSION['profileUpdateResult'])) : 'null'; ?>;
        
        // Function to display SweetAlert based on the result
        const displaySweetAlert = (result, message) => {
            Swal.fire({
                icon: result === "success" ? "success" : "error",
                title: result === "success" ? "Success" : "Error",
                text: message,
            });
        };

        // Display SweetAlert for password change result
        if (passwordChangeResult) {
            const passwordChangeMessages = {
                success: "Your password has been updated successfully!",
                mismatch: "New password and confirm password do not match.",
                incorrect: "Old password is incorrect.",
                notfound: "User not found in the database.",
                error: "Error updating password. Please try again later.",
                invalidlength: "Password must be between 8 and 32 characters.",

            };
            const passwordChangeMessage = passwordChangeMessages[passwordChangeResult] || "An unexpected error occurred.";
            displaySweetAlert(passwordChangeResult, passwordChangeMessage);
            <?php unset($_SESSION['passwordChangeResult']); ?>
        }

        // Display SweetAlert for profile update result
        if (profileUpdateResult) {
            const profileUpdateMessages = {
                success: "Your profile has been updated successfully!",
                error: "Error updating profile. Please try again later.",
            };
            const profileUpdateMessage = profileUpdateMessages[profileUpdateResult] || "An unexpected error occurred.";
            displaySweetAlert(profileUpdateResult, profileUpdateMessage);
            <?php unset($_SESSION['profileUpdateResult']); ?>
        }
    });




            // Toggle password visibility
        function togglePasswordVisibility(inputField, toggleButton) {
            const type = inputField.getAttribute('type') === 'password' ? 'text' : 'password';
            inputField.setAttribute('type', type);

            // Change the eye icon based on password visibility
            const eyeIconClass = type === 'password' ? 'fa-eye' : 'fa-eye-slash';
            toggleButton.querySelector('i').classList.remove('fa-eye', 'fa-eye-slash');
            toggleButton.querySelector('i').classList.add(eyeIconClass);
        }

        // Attach event listeners to toggle buttons
        document.addEventListener("DOMContentLoaded", function () {
            const toggleOldPassword = document.getElementById('toggleOldPassword');
            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');

            const oldPassword = document.getElementById('oldpassword');
            const newPassword = document.getElementById('password');
            const confirmPassword = document.getElementById('cpassword');

            toggleOldPassword.addEventListener('click', () => togglePasswordVisibility(oldPassword, toggleOldPassword));
            togglePassword.addEventListener('click', () => togglePasswordVisibility(newPassword, togglePassword));
            toggleConfirmPassword.addEventListener('click', () => togglePasswordVisibility(confirmPassword, toggleConfirmPassword));
        });

    </script>

                <!-- Modal Profile Picture --> 
    <div class="modal fade" id="profilePicture" tabindex="-1" aria-labelledby="profilePictureModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="profilePictureModalLabel">Update Profile Picture</h1>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <form action="adminUserProfilePicture.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body d-flex align-items-center">
                        <div class="mb-3">
                            <label for="profilePicture">Choose Image</label>
                            <input type="file" name="profilePicture" class="form-control-file" accept="image/*"  onchange="previewImage(this);">
                            <img id="imagePreview" src="#" alt="Preview" style="max-width: 100%; max-height: 300px;">
                        </div>
                        <div class="mb-3 text-center">
                        <input type="checkbox" name="removeProfilePicture" value="1"> Remove Profile Picture
                    </div>
                    </div>
                    <div class="modal-footer">
                        <input type="button" class="btn btn-default" data-dismiss="modal" value="Cancel">
                        <button type="submit" name="updateProfilePicture" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to display a preview of the selected image
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
         // Function to display SweetAlert for profile picture update result
    function showProfilePictureResultAlert() {
        const result = "<?php echo isset($_SESSION['profilePictureResult']) ? $_SESSION['profilePictureResult'] : 'null'; ?>";

        if (result === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Profile picture updated successfully!',
            });
        } else if (result === 'error_size') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Sorry, your file is too large.',
            });
        } else if (result === 'error_type') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Sorry, only JPG and PNG files are allowed.',
            });
        } else if (result === 'error_upload') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Sorry, your file was not uploaded.',
            });
        } else if (result === 'success_upload') {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Profile picture updated successfully and file moved successfully.',
            });
        }

        // Clear the session variable after displaying the message
        <?php unset($_SESSION['profilePictureResult']); ?>
    }

    // Check if there is a session variable indicating the profile picture update result
    document.addEventListener("DOMContentLoaded", function () {
        showProfilePictureResultAlert();
    });

    </script>

    <!-- FOOTER -->
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


    </body>
    </html>