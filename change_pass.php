<?php
session_start();
require_once "includes/db_connection.php"; // Include your database connection file

// Check if user is logged in
if (!isset($_SESSION["phone"])) {
    // Redirect user to login page or display an error message
    header("location: login.php");
    exit;
}

// Define variables and initialize with empty values
$old_password = $new_password = $confirm_password = "";
$old_password_err = $new_password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate old password
    $old_password = trim($_POST["oldPass"]);
    $new_password = trim($_POST["newPass"]);
    $confirm_password = trim($_POST["confirmNewPass"]);

    // Check if password fields are empty
    if (empty($old_password)) {
        $old_password_err = "Please enter your old password.";
    }
    if (empty($new_password)) {
        $new_password_err = "Please enter a new password.";
    }
    if (empty($confirm_password)) {
        $confirm_password_err = "Please confirm your new password.";
    }

    // Hash the entered old password
    $hashed_old_password = md5($old_password);

    // Retrieve the hashed password from the database for the logged-in user
    $sql = "SELECT Hashed_Password FROM users WHERE PhoneNumber = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("s", $param_phone);

        // Set parameters
        $param_phone = $_SESSION["phone"];

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Store result
            $stmt->store_result();

            // Check if phone exists
            if ($stmt->num_rows == 1) {
                // Bind result variables
                $stmt->bind_result($hashed_password);
                if ($stmt->fetch()) {
                    // Compare entered old password hash with stored hash
                    if ($hashed_old_password != $hashed_password) {
                        $old_password_err = "The old password you entered is incorrect.";
                    } else {
                        // Old password matches, proceed to update
                        // Validate and hash new password
                        if ($new_password !== $confirm_password) {
                            $confirm_password_err = "Passwords do not match.";
                        } else {
                            // Hash the new password before updating in the database
                            $hashed_new_password = md5($new_password);

                            // Update the password in the database
                            $update_sql = "UPDATE users SET Hashed_Password = ? WHERE PhoneNumber = ?";
                            if ($update_stmt = $conn->prepare($update_sql)) {
                                $update_stmt->bind_param("ss", $param_hashed_password, $param_phone);
                                $param_hashed_password = $hashed_new_password;
                                $param_phone = $_SESSION["phone"];
                                if ($update_stmt->execute()) {
                                    // Password updated successfully
                                    // Redirect to login page
                                    header("location: includes/login.php");
                                    exit;
                                } else {
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                            } else {
                                echo "Oops! Something went wrong. Please try again later.";
                            }
                        }
                    }
                }
            } else {
                // Display an error message if phone doesn't exist
                echo "No account found with that phone number.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        $stmt->close();
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }

    // Close connection
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <style>
        /* Basic styling */
        body {
            background-color: rgba(29, 43, 83, 0.89);
            font-family: "Roboto", sans-serif;
            background-color: #17B794;
            background: rgb(211, 211, 211);
            background-color: rgba(29, 43, 83, 0.89);
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Change Password</h2>
        <form id="changePasswordForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="oldPass">Old Password:</label>
            <input type="password" id="oldPass" name="oldPass" placeholder="Enter your old Password" required>
            <span class="error"><?php echo $old_password_err; ?></span><br>

            <label for="newPass">New Password:</label>
            <input type="password" id="newPass" name="newPass" placeholder="Enter the new password" required>
            <span class="error"><?php echo $new_password_err; ?></span><br>

            <label for="confirmNewPass">Confirm New Password:</label>
            <input type="password" id="confirmNewPass" name="confirmNewPass" placeholder="Confirm the new password" required>
            <span class="error"><?php echo $confirm_password_err; ?></span><br>

            <input type="submit" value="Change Password">
        </form>
        <form action="account.php" method="post">
        <input type="submit" value="Go Back">
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#changePasswordForm').submit(function(event) {
                event.preventDefault(); // Prevent form submission

                // Reset error messages
                $('.error').text('');

                // Validate new password
                var newPassword = $('#newPass').val();
                if (!isValidPassword(newPassword)) {
                    $('#newPass').next('.error').text('Password must be at least 6 characters, one uppercase letter, one lowercase letter, one number, one special character');
                    return;
                }

                // Validate confirm new password
                var confirmNewPassword = $('#confirmNewPass').val();
                if (newPassword !== confirmNewPassword) {
                    $('#confirmNewPass').next('.error').text('Passwords do not match');
                    return;
                }

                // If all validations pass, submit the form
                this.submit();
            });

            function isValidPassword(password) {
                // Add password validation logic here
                return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/.test(password); // Example: At least 6 characters, one uppercase letter, one lowercase letter, one number, and one special character
            }
        });
    </script>

</body>

</html>