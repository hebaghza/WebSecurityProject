<?php
include('db_connection.php');

// Define variables and initialize with empty values
$new_phone = $new_national_id = $new_password = $confirm_password = $new_first_name = $new_last_name = "";
$new_phone_err = $new_national_id_err = $new_password_err = $confirm_password_err = $new_first_name_err = $new_last_name_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate new phone number
    if (empty(trim($_POST["new_phone"]))) {
        $new_phone_err = "Please enter a phone number.";
    } else {
        // Prepare a select statement
        $sql = "SELECT UserID FROM users WHERE PhoneNumber = ?";

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

    // Validate new first name
    if (empty(trim($_POST["new_first_name"]))) {
        $new_first_name_err = "Please enter your First Name.";
    } else {
        $new_first_name = trim($_POST["new_first_name"]);
    }

    // Validate new last name
    if (empty(trim($_POST["new_last_name"]))) {
        $new_last_name_err = "Please enter your Last Name.";
    } else {
        $new_last_name = trim($_POST["new_last_name"]);
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
    if (empty($new_phone_err) && empty($new_national_id_err) && empty($new_first_name_err) && empty($new_last_name_err) && empty($new_password_err) && empty($confirm_password_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (UserID, FirstName, LastName, PhoneNumber, Hashed_Password, Role) VALUES (?, ?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $hashed_password = md5($new_password);
            $role = 'user';

            $stmt->bind_param("isssss", $param_userid, $param_firstname, $param_lastname, $param_phone, $param_password, $param_role);

            // Set parameters
            $param_userid = $new_national_id;
            $param_firstname = $new_first_name;
            $param_lastname = $new_last_name;
            $param_phone = $new_phone;
            $param_password = $hashed_password;
            $param_role = $role;

            // Attempt to execute the prepared statement

            if ($stmt->execute()) {
                // Redirect to login page
                header("location: ../account.php");
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
            margin-right: 10px; 
        }
    </style>
   
</head>
<body>

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
                            <h1 class="animate-box" data-animate-effect="fadeInUp" style="margin-top: 20px;">Register</h1>
                            <form id="registerForm" method="post">
                                <label for="new_first_name" style="color: white">First Name:</label>
                                <input type="text" id="new_first_name" name="new_first_name" placeholder="Enter your first name" value="<?php echo htmlspecialchars($new_first_name); ?>" required style="background-color: rgb(211, 211, 211); border-radius: 5px; margin-top: 10px;">
                                <span class="error" id="firstNameError"><?php echo $new_first_name_err; ?></span><br> 
                                
                                <label for="new_last_name" style="color: white">Last Name:</label>
                                <input type="text" id="new_last_name" name="new_last_name" placeholder="Enter your last name" value="<?php echo htmlspecialchars($new_last_name); ?>" required style="background-color: rgb(211, 211, 211); border-radius: 5px; margin-top: 10px;">
                                <span class="error" id="lastNameError"><?php echo $new_last_name_err; ?></span><br> 
                                
                                <label for="new_phone" style="color: white">Phone Number:</label>
                                <input type="text" id="new_phone" name="new_phone" placeholder="Enter your phone number" value="<?php echo htmlspecialchars($new_phone); ?>" required style="background-color: rgb(211, 211, 211); border-radius: 5px; margin-top: 10px;">
                                <span class="error" id="newPhoneError"><?php echo $new_phone_err; ?></span><br> 
                                
                                <label for="new_national_id" style="color: white">National ID:</label>
                                <input type="text" id="new_national_id" name="new_national_id" placeholder="Enter your national ID" value="<?php echo htmlspecialchars($new_national_id); ?>" required style="background-color: rgb(211, 211, 211); border-radius: 5px; margin-top: 10px;">
                                <span class="error" id="nationalIdError"><?php echo $new_national_id_err; ?></span><br> 
                                
                                <label for="new_password" style="color: white">Password:</label>
                                <input type="password" id="new_password" name="new_password" placeholder="Enter your password" required style="background-color: rgb(211, 211, 211); border-radius: 5px; margin-top: 10px;">
                                <span class="error" id="newPasswordError"><?php echo $new_password_err; ?></span><br> 
                                
                                <label for="confirm_password" style="color: white">Confirm Password:</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required style="background-color: rgb(211, 211, 211); border-radius: 5px; margin-top: 10px;">
                                <span class="error" id="confirmPasswordError"><?php echo $confirm_password_err; ?></span><br> 
                                
                                <input type="submit" value="Register" class="btn btn-white btn-lg btn-outline">
                            </form>

                            <h2 class="animate-box" data-animate-effect="fadeInUp" style="color: white">Have an Account?</h2>
                            <div class="btn-group">
                                <p class="animate-box" data-animate-effect="fadeInUp"><a href="login.php" class="btn btn-white btn-lg btn-outline">Login Now</a></p>
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
<div class="gototop js-top">
    <a href="#" class="js-gotop"><i class="icon-arrow-up"></i></a>
</div>

<script src="../js/main.js"></script>

    <script>
        $(document).ready(function() {
            $('#registerForm').submit(function(event) {
                event.preventDefault(); // Prevent form submission

                // Reset error messages
                $('#newPhoneError').text('');
                $('#nationalIdError').text('');
                $('#firstNameError').text('');
                $('#lastNameError').text('');
                $('#newPasswordError').text('');
                $('#confirmPasswordError').text('');

                // Validate phone number
                var newPhone = $('#new_phone').val();
                if (!isValidPhone(newPhone)) {
                    $('#newPhoneError').text('Invalid phone number  ');
                    return;
                }

                // Validate national ID
                var nationalId = $('#new_national_id').val();
                if (!isValidNationalId(nationalId)) {
                    $('#nationalIdError').text('Invalid national ID ');
                    return;
                }

                // Validate first name
                var firstName = $('#new_first_name').val();
                if (!isValidName(firstName)) {
                    $('#firstNameError').text('Please enter a valid first name ');
                    return;
                }

                // Validate last name
                var lastName = $('#new_last_name').val();
                if (!isValidName(lastName)) {
                    $('#lastNameError').text('Please enter a valid last name');
                    return;
                }

                // Validate password
                var newPassword = $('#new_password').val();
                if (!isValidPassword(newPassword)) {
                    $('#newPasswordError').text('Password must be at least 6 characters, one uppercase letter, one lowercase letter, one number, one special character');
                    return;
                }

                // Validate confirm password
                var confirmPassword = $('#confirm_password').val();
                if (newPassword !== confirmPassword) {
                    $('#confirmPasswordError').text('Password does not match');
                    return;
                }

                // If all validations pass, submit the form
                this.submit();
            });

            function isValidPhone(phone) {
                // Add phone number validation logic here
                return /^05\d{8}$/.test(phone); // Example: 10 digits
            }

            function isValidNationalId(nationalId) {
                // Add national ID validation logic here
                return /^\d{10}$/.test(nationalId); // Example: 10 digits
            }

            function isValidName(name) {
                // Add name validation logic here
                return /^[a-zA-Z]+$/.test(name); // Example: Only letters
            }

            function isValidPassword(password) {
                // Add password validation logic here
                return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/.test(password); //  At least 6 characters, one uppercase letter, one lowercase letter, one number, and one special character
            }
        });
    </script>
</body>
</html>

