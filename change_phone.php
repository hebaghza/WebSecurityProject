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
$old_phone = $new_phone = $confirm_phone = "";
$old_phone_err = $new_phone_err = $confirm_phone_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate old phone number
    $old_phone = trim($_POST["oldPhone"]);
    $new_phone = trim($_POST["newPhone"]);
    $confirm_phone = trim($_POST["confirmNewPhone"]);

    // Check if phone fields are empty
    if (empty($old_phone)) {
        $old_phone_err = "Please enter your old phone number.";
    }
    if (empty($new_phone)) {
        $new_phone_err = "Please enter a new phone number.";
    }
    if (empty($confirm_phone)) {
        $confirm_phone_err = "Please confirm your new phone number.";
    }

    // Check if old phone number matches the stored one
    if ($old_phone !== $_SESSION["phone"]) {
        $old_phone_err = "The old phone number you entered is incorrect.";
    }

    // Validate new phone number format (10 digits starting with 05)
    if (!preg_match("/^05\d{8}$/", $new_phone)) {
        $new_phone_err = "Phone number must be 10 digits and start with '05'.";
    }

    // Check if new phone number already exists in the database
    $check_sql = "SELECT UserID FROM users WHERE PhoneNumber = ?";
    if ($check_stmt = $conn->prepare($check_sql)) {
        $check_stmt->bind_param("s", $new_phone);
        if ($check_stmt->execute()) {
            $check_stmt->store_result();
            if ($check_stmt->num_rows > 0) {
                $new_phone_err = "This phone number is already registered.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $check_stmt->close();
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }

    // If no errors, update the phone number
    if (empty($old_phone_err) && empty($new_phone_err) && empty($confirm_phone_err)) {
        // Update the phone number in the database
        $update_sql = "UPDATE users SET PhoneNumber = ? WHERE PhoneNumber = ?";
        if ($update_stmt = $conn->prepare($update_sql)) {
            $update_stmt->bind_param("ss", $new_phone, $_SESSION["phone"]);
            if ($update_stmt->execute()) {
                // Phone number updated successfully
                $_SESSION["phone"] = $new_phone; // Update session with new phone number
                // You can redirect the user or show a success message
                echo "Phone number updated successfully.";
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
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
    <title>Change Phone Number</title>
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

        input[type="text"] {
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
        <h2>Change Phone Number</h2>
        <form id="changePhoneNumberForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="oldPhone">Old Phone Number:</label>
            <input type="text" id="oldPhone" name="oldPhone" placeholder="Enter your old phone number" required>
            <span class="error"><?php echo $old_phone_err; ?></span><br>

            <label for="newPhone">New Phone Number:</label>
            <input type="text" id="newPhone" name="newPhone" placeholder="Enter the new phone number" required>
            <span class="error"><?php echo $new_phone_err; ?></span><br>

            <label for="confirmNewPhone">Confirm New Phone Number:</label>
            <input type="text" id="confirmNewPhone" name="confirmNewPhone" placeholder="Confirm the new phone number" required>
            <span class="error"><?php echo $confirm_phone_err; ?></span><br>

            <input type="submit" value="Change Phone Number">
        </form>
        <form action="account.php" method="post">
        <input type="submit" value="Go Back">
        </form>
        <form action="logout.php" method="post">
        <input type="submit" value="Logout">
        </form>
    </div>

</body>

</html>