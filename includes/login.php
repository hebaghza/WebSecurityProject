<?php
include('db_connection.php');

// Define variables and initialize with empty values
$phone = $password = "";
$phone_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if phone is empty
    if (empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter your phone number.";
    } else {
        $phone = trim($_POST["phone"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($phone_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT UserID, PhoneNumber, Hashed_Password, Role FROM users WHERE PhoneNumber = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_phone);

            // Set parameters
            $param_phone = $phone;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                // Check if phone exists, if yes then verify password
                if ($stmt->num_rows == 1) {
                    // Bind result variables
                    $stmt->bind_result($user_id, $db_phone, $hashed_password, $role);
                    if ($stmt->fetch()) {
                        if (md5($password) == $hashed_password) {
                            /* Password is correct, so start a new session and
                            save the phone and role to the session */
                            session_start();
                            $_SESSION['phone'] = $db_phone;
                            $_SESSION['user_id'] = $user_id;
                            $_SESSION['role'] = $role; // Add role to session
                            header("location: ../account.php"); // Redirect to account.php after successful login
                            exit;
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    // Display an error message if phone doesn't exist
                    $phone_err = "No account found with that phone number.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>


<!DOCTYPE HTML>

<html>
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>PaySwift &mdash; PaySwift Website</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="PaySwift Website To Track Your Money" />
	<meta name="author" content="Group 1" />



	<!-- <link href="https://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet"> -->
	
	<!-- Animate.css -->
	<link rel="stylesheet" href="../css/animate.css">
	<!-- Icomoon Icon Fonts-->
	<link rel="stylesheet" href="../css/icomoon.css">
	<!-- Themify Icons-->
	<link rel="stylesheet" href="../css/themify-icons.css">
	<!-- Bootstrap  -->
	<link rel="stylesheet" href="../css/bootstrap.css">

	<!-- Magnific Popup -->
	<link rel="stylesheet" href="../css/magnific-popup.css">

	<!-- Owl Carousel  -->
	<link rel="stylesheet" href="../css/owl.carousel.min.css">
	<link rel="stylesheet" href="../css/owl.theme.default.min.css">

	<!-- Theme style  -->
	<link rel="stylesheet" href="../css/style.css">

	<!-- Modernizr JS -->
	<script src="../js/modernizr-2.6.2.min.js"></script>
	<!-- FOR IE9 below -->
	<!--[if lt IE 9]>
	<script src="js/respond.min.js"></script>
	<![endif]-->
    <style>
    .btn-group {
        display: inline-block;
        margin-right: 10px; /* Adjust spacing between buttons */
    }
    
    </style>
	</head>
	<body>
		
	<div class="gtco-loader"></div>
	<div class="container">    
    
</div>

	<div id="page">
	<nav class="gtco-nav" role="navigation">
		<div class="gtco-container">
			<div class="row">
				<div class="col-sm-4 col-xs-12">
					<div id="gtco-logo"><a href="index.php">PaySwift<em>.</em></a></div>
                    
				</div>
			</div>
			
		</div>
	</nav>

	<header id="gtco-header" class="gtco-cover" role="banner" style="background-image:url(images/img_bg_1.jpg);">
		<div class="overlay"></div>
		<div class="gtco-container">
			<div class="row">
				<div class="col-md-12 col-md-offset-0 text-left">
					<div class="display-t">
                        
						<div class="display-tc">
                            <h1 class="animate-box" data-animate-effect="fadeInUp" style="margin-top: 20px;">Login</h1>
                            <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            
                            <label for="phone" style="color: white">Phone Number:</label>
                            <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required style="background-color: rgb(211, 211, 211); border-radius: 5px; margin-top: 10px;">

                             <span class="error"><?php echo $phone_err; ?></span>

                             <label for="password" style="color: white">Password:</label>
                             <input type="password" id="password" name="password" placeholder="Enter your password" required style="background-color: rgb(211, 211, 211); border-radius: 5px; margin-top: 10px;">
                                <span class="error"><?php echo $password_err; ?></span>
                                <br></br>

                            
                                <input type="submit" value="Login" class="btn btn-white btn-lg btn-outline">
                            </form>
                        

							<h2 class="animate-box" data-animate-effect="fadeInUp" style="color: white">Don't Have an Account?</h2>

                            <div class="btn-group">
                            <p class="animate-box" data-animate-effect="fadeInUp"><a href="register.php" class="btn btn-white btn-lg btn-outline">Register Now</a></p>
                        </div>
                        <div class="btn-group">
                            <p class="animate-box" data-animate-effect="fadeInUp"><a href="index.php" class="btn btn-white btn-lg btn-outline">Home</a></p>
                        </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>

						
</div>

	<div class="gototop js-top">
		<a href="#" class="js-gotop"><i class="icon-arrow-up"></i></a>
	</div>
	
	<!-- jQuery -->
	<script src="../js/jquery.min.js"></script>
	<!-- jQuery Easing -->
	<script src="../js/jquery.easing.1.3.js"></script>
	<!-- Bootstrap -->
	<script src="../js/bootstrap.min.js"></script>
	<!-- Waypoints -->
	<script src="../js/jquery.waypoints.min.js"></script>
	<!-- Carousel -->
	<script src="../js/owl.carousel.min.js"></script>
	<!-- countTo -->
	<script src="../js/jquery.countTo.js"></script>
	<!-- Magnific Popup -->
	<script src="../js/jquery.magnific-popup.min.js"></script>
	<script src="../js/magnific-popup-options.js"></script>
	<!-- Main -->
	<script src="../js/main.js"></script>

	</body>
</html>
