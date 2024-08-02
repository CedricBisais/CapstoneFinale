<!DOCTYPE html>
<html>
<head>
	<title>Reset Password</title>
	<link rel="icon" href="Capstone2_Logo.png" />
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="css/Login.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
	<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
</head>
<style>
	img#sti-logo {
    border-radius: 50%;
    overflow: hidden;
}
</style>
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
						<img src="STI LOGO.png" id="sti-logo" width="150px">
					</div>

					<?php
					if (isset($_GET['message'])) {
					    $message = urldecode($_GET['message']);
					    echo '<div class="alert alert-info">' . $message . '</div>';
					}
					?>

        			<div class="reset-form d-block">
					    <form class="reset-password-form px-3" action="forgotPasswordProcess.php" method="post">
					        <h4 class="mb-3">Reset Your password</h4>
					        <p class="mb-3 text-white">
					            Please enter your email address and we will send you a password reset link.
					        </p>
				  
					        <div class="form-input">
					            <span><i class="fa fa-envelope"></i></span>
					            <input type="email" name="email" placeholder="Email Address" tabindex="10" required>
					        </div>
					        <div class="mb-3"> 
					            <button type="submit" class="btn">Send Reset Link</button>
					        </div>
					    </form>
				  	</div>
				
				</div>
			</div>
			<!-- FORM CONTAINER END -->
		</div>
	</div>
</body>
</html>