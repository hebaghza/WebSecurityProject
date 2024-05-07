<!DOCTYPE HTML>
<?php
    // Start the session
    session_start();
?>
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
    <link rel="stylesheet" href="css/animate.css">
    <!-- Icomoon Icon Fonts-->
    <link rel="stylesheet" href="css/icomoon.css">
    <!-- Themify Icons-->
    <link rel="stylesheet" href="css/themify-icons.css">
    <!-- Bootstrap  -->
    <link rel="stylesheet" href="css/bootstrap.css">

    <!-- Magnific Popup -->
    <link rel="stylesheet" href="css/magnific-popup.css">

    <!-- Owl Carousel  -->
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">

    <!-- Theme style  -->
    <link rel="stylesheet" href="css/style.css">

    <!-- Modernizr JS -->
    <script src="js/modernizr-2.6.2.min.js"></script>
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
                <div id="gtco-logo"><a href="includes/index.php">PaySwift<em>.</em></a></div>
            </div>
            <div class="col-xs-8 text-right menu-1">
                <ul>
                    <li><a href="includes/index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li class="has-dropdown active">
                        <a href="services.php">Services</a>
                        <ul class="dropdown">
                            <li><a href="includes/login.php">Money Transfer</a></li>
                            <li><a href="includes/login.php">Adding Money</a></li>
                        </ul>
                    </li>
                    <?php 
                        if(isset($_SESSION['username'])) {
                            echo '<li><a href="logout.php">Logout</a></li>';
                        } else {
                            echo '<li><a href="includes/login.php">Login</a></li>';
                        }
                    ?>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
            </div>
        </div>
        
    </div>
</nav>

<header id="gtco-header" class="gtco-cover gtco-cover-xs" role="banner" style="background-image:url(images/img_bg_1.jpg);">
    <div class="overlay"></div>
    <div class="gtco-container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 text-center">
                <div class="display-t">
                    <div class="display-tc">
                        <h1 class="animate-box" data-animate-effect="fadeInUp">Our Services</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="gtco-section border-bottom">
    <div class="gtco-container">
        <div class="row animate-box">
            <div class="col-md-8 col-md-offset-2 text-center gtco-heading">
                <h2>What We Offer</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="feature-left animate-box" data-animate-effect="fadeInLeft">
                    <span class="icon">
                        <i class="ti-settings"></i>
                    </span>
                    <div class="feature-copy">
                        <h3>Money Transfer</h3>
                        <p>Send Money Anywhere, Anytime: Fast, Secure, and Hassle-Free!</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-left animate-box" data-animate-effect="fadeInLeft">
                    <span class="icon">
                        <i class="ti-ruler-pencil"></i>
                    </span>
                    <div class="feature-copy">
                        <h3>Adding Money</h3>
                        <p>Top Up with Ease: Instantly Add Funds to Your Account, Anytime, Anywhere!</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-left animate-box" data-animate-effect="fadeInLeft">
                    <span class="icon">
                        <i class="ti-face-smile"></i>
                    </span>
                    <div class="feature-copy">
                        <h3>Customer Support</h3>
                        <p>Customer Care at Your Fingertips: Round-the-Clock Support for Your Peace of Mind!</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



<footer id="gtco-footer" role="contentinfo">
    <div class="gtco-container">
        <div class="row row-pb-md">

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
                        <li><a href="mailto:comPaySwift@gmail.com"><i class="icon-mail2"></i> PaySwift@gmail.com</a></li>
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
<script src="js/jquery.min.js"></script>
<!-- jQuery Easing -->
<script src="js/jquery.easing.1.3.js"></script>
<!-- Bootstrap -->
<script src="js/bootstrap.min.js"></script>
<!-- Waypoints -->
<script src="js/jquery.waypoints.min.js"></script>
<!-- Carousel -->
<script src="js/owl.carousel.min.js"></script>
<!-- countTo -->
<script src="js/jquery.countTo.js"></script>
<!-- Magnific Popup -->
<script src="js/jquery.magnific-popup.min.js"></script>
<script src="js/magnific-popup-options.js"></script>
<!-- Main -->
<script src="js/main.js"></script>

</body>
</html>
