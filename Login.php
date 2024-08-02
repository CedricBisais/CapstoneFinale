<?php

session_start();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="icon" href="Capstone2_Logo.png" />

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="css/Login.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

</head>
<body>

<style>
#togglePassword {
    position: absolute;
    top: 35%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
}	

img#sti-logo {
    border-radius: 50%;
    overflow: hidden;
}

#back {
    position: absolute;
    top: 50px;
    left: 10px;
}


</style>

<div class="container-fluid">
    <div class="row ">
        <!-- IMAGE CONTAINER BEGIN -->
        <div class="col-lg-6 col-md-6 d-none d-md-block infinity-image-container"></div>
        <!-- IMAGE CONTAINER END -->

        <!-- FORM CONTAINER BEGIN -->
        <div class="col-lg-6 col-md-6 infinity-form-container">
            <div class="col-lg-9 col-md-12 col-sm-9 col-xs-12 infinity-form">
                <!-- Go Back Link -->
                                <div class="text-left">
                    <a href="indexPage.php" id="back" class="btn btn-secondary">
                        <i class="fa fa-home"></i> Home
                    </a>
                </div>
                <!-- Company Logo -->
                <div class="text-center mb-3 mt-5">
                    <img src="STI LOGO.png" id="sti-logo" width="150px" alt="STI Logo">
                </div>
                <div class="text-center mb-4">
                    <h4>Login into your account</h4>
                </div>

                <?php
                if (isset($expirationMessage)) {
                    echo $expirationMessage;
                    echo '<script>
                            // Remove parameters from the URL after displaying the message
                            var urlWithoutParams = window.location.pathname;
                            history.replaceState({}, document.title, urlWithoutParams);
                        </script>';
                }
                ?>

                <!-- Display error message if any -->
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger">' . $_GET['error'] . '</div>';
                    echo '<script>
                            // Remove parameters from the URL after displaying the message
                            var urlWithoutParams = window.location.pathname;
                            history.replaceState({}, document.title, urlWithoutParams);
                        </script>';
                }
                ?>

                <!-- Display success message if any -->
                <?php
                if (isset($_GET['success']) && $_GET['success'] == 1) {
                    echo '<div class="alert alert-success">Password successfully changed. You can now log in.</div>';
                    echo '<script>
                            // Remove parameters from the URL after displaying the message
                            var urlWithoutParams = window.location.pathname;
                            history.replaceState({}, document.title, urlWithoutParams);
                        </script>';
                }
                ?>

                <!-- Form -->
                <form action="connection.php" method="post" class="px-3">
                    <!-- Input Box -->
                    <div class="form-input">
                        <span><i class="fa fa-envelope-o"></i></span>
                        <input type="text" name="username" placeholder="Username" tabindex="10">
                    </div>
                    <div class="form-input">
                        <span><i class="fa fa-lock"></i></span>
                        <input type="password" id="pword" name="password" placeholder="Password" >
                        <i id="togglePassword" class="fa fa-eye-slash" aria-hidden="true"></i>
                    </div>
                    <div class="row mb-3">
                        <!-- Remember Checkbox -->
                        <div class="col-auto d-flex align-items-center">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="cb1">
                                <label class="custom-control-label text-white" for="cb1">Remember me</label>
                            </div>
                        </div>
                    </div>
                    <!-- Login Button -->
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                    <div class="text-right">
                        <a href="forgotPassword.php" class="forget-link">Forgot password?</a>
                    </div>
                </form>
            </div>
        </div>
        <!-- FORM CONTAINER END -->
    </div>
</div>

<script>
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
</script>

</body>
</html>