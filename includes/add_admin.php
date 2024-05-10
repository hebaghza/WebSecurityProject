<?php
include('db_connection.php');
include('csrftoken.php');

// Define variables and initialize with empty values
$new_phone = $new_national_id = $new_password = $confirm_password = $new_first_name = $new_last_name = "";
$new_phone_err = $new_national_id_err = $new_password_err = $confirm_password_err = $new_first_name_err = $new_last_name_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['_token']) {
        // CSRF token is missing or incorrect, handle the error (e.g., log, display error message, deny request)
        // Example: Redirect user to an error page
        header('Location: error.php');
        exit();
    }

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
            $role = 'admin';

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
                echo "<script>alert('Admin added successfully');</script>";
            } else {
                echo "<script>alert('Something went wrong. Please try again later.');</script>";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>PaySwif &mdash; PaySwif Website</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="PaySwif Website To Track Your Money" />
    <meta name="author" content="Group 1" />
    <title>Add admin</title>

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

        input[type="text"],
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
    </style>
</head>

<body>

    <div class="container">
        <h2> Add admin</h2>
        <form id="addForm" method="post">

			<label for="new_first_name">First Name:</label>
            <input type="text" id="new_first_name" name="new_first_name" placeholder="Enter your first name" value="<?php echo $new_first_name; ?>" required>
            <span class="error" id="firstNameError"><?php echo $new_first_name_err; ?></span><br> 

            <label for="new_last_name">Last Name:</label>
            <input type="text" id="new_last_name" name="new_last_name" placeholder="Enter your last name" value="<?php echo $new_last_name; ?>" required>
            <span class="error" id="lastNameError"><?php echo $new_last_name_err; ?></span><br> 

            <label for="new_phone">Phone Number:</label>
            <input type="text" id="new_phone" name="new_phone" placeholder="Enter your phone number" value="<?php echo $new_phone; ?>" required>
            <span class="error" id="newPhoneError"><?php echo $new_phone_err; ?></span><br> 

            <label for="new_national_id">National ID:</label>
            <input type="text" id="new_national_id" name="new_national_id" placeholder="Enter your national ID" value="<?php echo $new_national_id; ?>" required>
            <span class="error" id="nationalIdError"><?php echo $new_national_id_err; ?></span><br> 

            <label for="new_password">Password:</label>
            <input type="password" id="new_password" name="new_password" placeholder="Enter your password" required>
            <span class="error" id="newPasswordError"><?php echo $new_password_err; ?></span><br> 

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
            <span class="error" id="confirmPasswordError"><?php echo $confirm_password_err; ?></span><br> 

            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
            <input type="submit" value="Add Admin">
        </form>

        <form action="../account.php" method="post">
            <input type="submit" value="Go Back">
            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
        </form>

        <form action="../logout.php" method="post">
            <input type="submit" value="Logout">
        </form>
        

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#addForm').submit(function(event) {
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
                return /^\d{10}$/.test(phone); // Example: 10 digits
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
                return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/.test(password); // At least 6 characters, one uppercase letter, one lowercase letter, one number, and one special character
            }
        });
    </script>

</body>

</html>
