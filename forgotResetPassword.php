<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="css/Login.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- IMAGE CONTAINER BEGIN -->
            <div class="col-lg-6 col-md-6 d-none d-md-block infinity-image-container"></div>
            <!-- IMAGE CONTAINER END -->

            <!-- FORM CONTAINER BEGIN -->
            <div class="col-lg-6 col-md-6 infinity-form-container">
                <div class="col-lg-8 col-md-12 col-sm-8 col-xs-12 infinity-form">
                    <div class="text-center mb-3 mt-5">
                        <img src="logo.png" width="150px">
                    </div>

                    <?php
                    session_start();
                    $token = $_GET['token'];

                    // Function to check if the token is valid
                    function isValidToken($token) {
                        $servername = "localhost";
                        $username = "root";
                        $password = "";
                        $dbname = "thesesarchive_db";

                        $conn = new mysqli($servername, $username, $password, $dbname);

                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $stmt = $conn->prepare("SELECT * FROM tbl_accounts WHERE password_reset_token = ?");
                        $stmt->bind_param("s", $token);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $conn->close();

                        // If the token exists in the database, return true; otherwise, return false
                        return $result->num_rows > 0;
                    }

                    // Check if the token is valid
                    if (isValidToken($token)) {
                        // Token is valid, proceed to display the password reset form
                        echo '<div class="alert alert-info">Token is valid. You can reset your password.</div>';
                        echo '<form class="reset-password-form px-3" action="forgotUpdatePassword.php" method="post">
                                <h4 class="mb-3">Reset Your password</h4>
                                <p class="mb-3 text-white">
                                    Please enter your new password.
                                </p>
                                <div class="form-input">
                                    <span><i class="fa fa-lock"></i></span>
                                    <input type="password" name="password" placeholder="New Password" tabindex="10" required
                                           minlength="8" maxlength="32"> <!-- Set minlength and maxlength attributes -->
                                </div>
                                <div class="mb-3"> 
                                    <input type="hidden" name="token" value="' . htmlspecialchars($token) . '">
                                    <button type="submit" class="btn">Update Password</button>
                                </div>
                            </form>';
                    } else {
                        // Token is not valid, redirect to forgotPassword.php with an error message
                        $_SESSION['error_message'] = 'Invalid or expired token.';
                        header("Location: forgotPassword.php");
                        exit();
                    }
                    ?>

                </div>
            </div>
            <!-- FORM CONTAINER END -->
        </div>
    </div>
</body>
</html>