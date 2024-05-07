<?php
include('db_connection.php');

// Define variables and initialize with empty values
$new_phone = $new_national_id = $new_password = $confirm_password = "";
$new_phone_err = $new_national_id_err = $new_password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate new phone number
    if (empty(trim($_POST["new_phone"]))) {
        $new_phone_err = "Please enter a phone number.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE phone = ?";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_new_phone);

            // Set parameters
            $param_new_phone = trim($_POST["new_phone"]);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // store result
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $new_phone_err = "This phone number is already taken.";
                } else {
                    $new_phone = trim($_POST["new_phone"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Validate new national ID
    if (empty(trim($_POST["new_national_id"]))) {
        $new_national_id_err = "Please enter a national ID.";
    } else {
        $new_national_id = trim($_POST["new_national_id"]);
    }

    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Password must have at least 6 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting into database
    if (empty($new_phone_err) && empty($new_national_id_err) && empty($new_password_err) && empty($confirm_password_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (phone, national_id, password) VALUES (?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("sss", $param_new_phone, $param_new_national_id, $param_new_password);

            // Set parameters
            $param_new_phone = $new_phone;
            $param_new_national_id = $new_national_id;
            $param_new_password = password_hash($new_password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page
                header("location: login.php");
            } else {
                echo "Something went wrong. Please try again later.";
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

  	<!-- Facebook and Twitter integration -->
	<meta property="og:title" content=""/>
	<meta property="og:image" content=""/>
	<meta property="og:url" content=""/>
	<meta property="og:description" content=""/>
	<meta name="twitter:title" content="" />
	<meta name="twitter:image" content="" />
	<meta name="twitter:url" content="" />


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

	</head>
	<body>
		
	<div class="gtco-loader"></div>
	
	<div id="page">
	<nav class="gtco-nav" role="navigation">
		<div class="gtco-container">
			<div class="row">
				<div class="col-md-12 text-right gtco-contact">
					<ul class="">
						<li><a href="tel://966595372380"><i class="ti-mobile"></i> +966595372380 </a></li>
						<li><a href="https://www.twitter.com"><i class="ti-twitter-alt"></i> </a></li>
						<li><a href="mailto:comPaySwift@gmail.com"><i class="icon-mail2"></i></a></li>
						<li><a href="https://www.facebook.com"><i class="ti-facebook"></i></a></li>
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4 col-xs-12">
					<div id="gtco-logo"><a href="index.php">PaySwift<em>.</em></a></div>
				</div>
				<div class="col-xs-8 text-right menu-1">
					<ul>
						<li class="active"><a href="index.php">Home</a></li>
						<li><a href="../about.php">About</a></li>
						<li class="has-dropdown">
							<a href="../services.php">Services</a>
							<ul class="dropdown">
								<li><a href="login.php">Money Transfer</a></li>
								<li><a href="login.php">Adding Money</a></li>
							</ul>
						</li>
						<li><a href="login.php">login</a></li>
						<li><a href="../contact.php">Contact</a></li>
					</ul>
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
							<h1 class="animate-box" data-animate-effect="fadeInUp">Put Your Money To The Next Level</h1>
							<h2 class="animate-box" data-animate-effect="fadeInUp">Easy To Connect With Your Money</h2>
							<p class="animate-box" data-animate-effect="fadeInUp"><a href="register.php" class="btn btn-white btn-lg btn-outline">Register Now</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>

						
</div>

	

	<footer id="gtco-footer" role="contentinfo">
		<div class="gtco-container">
			<div class="row row-p	b-md">

				<div class="col-md-4">
					<div class="gtco-widget">
						<h3>About Us</h3>
						<p>Welcome to PaySwift! We're dedicated to providing tailored financial solutions for our customers' needs. With our experienced team and advanced technology, we prioritize integrity, transparency, and customer satisfaction. Thank you for choosing PaySwif </p>
						<p>lets embark on a journey towards financial success together!</p>					
					</div>
				</div>

				<div class="col-md-4 col-md-push-1">
					<div class="gtco-widget">
						<h3>Links</h3>
						<ul class="gtco-footer-links">
							<li><a href="#">Terms of services</a></li>
							<li><a href="#">Privacy Policy</a></li>
						</ul>
					</div>
				</div>

				<div class="col-md-4">
					<div class="gtco-widget">
						<h3>Get In Touch</h3>
						<ul class="gtco-quick-contact">
							<li><a href="tel://966595372380"><i class="icon-phone"></i> +966595372380</a></li>
							<li><a href="mailto:comPaySwift@gmail.com"><i class="icon-mail2"></i> PaySwift@gmail.Customer</a></li>
						</ul>
					</div>
				</div>

			</div>

			<div class="row copyright">
				<div class="col-md-12">
					<p class="pull-left">
						<small class="block">&copy; 2024 PaySwift. All Rights Reserved.</small> 
					</p>
					<p class="pull-right">
						<ul class="gtco-social-icons pull-right">
							<li><a href="https://www.twitter.com"><i class="icon-twitter"></i></a></li>
							<li><a href="mailto:comPaySwift@gmail.com"><i class="icon-mail2"></i></a></li>
							<li><a href="https://www.facebook.com"><i class="icon-facebook"></i></a></li>
						</ul>
					</p>
				</div>
			</div>

		</div>
	</footer>
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
