<?php
// Start the session
//session_start();
include('includes/csrftoken.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['_token']) {
        // CSRF token is missing or incorrect, handle the error (e.g., log, display error message, deny request)
        // Example: Redirect user to an error page
        header('Location: error.php');
        exit();
    }
    // Proceed with processing form data
}
// If the user is not logged in, redirect them to the login page
if (!isset($_SESSION['phone'])) {
    header("Location: login.php");
    exit;
}

// If the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection
    include 'includes/db_connection.php';

    // Form values
    $cardName = $_POST['cardName'];
    $cardNumber = $_POST['cardNumber'];
    $expiryDate = $_POST['expiryDate'];
    $cvv = $_POST['cvv'];
    $amount = $_POST['amount'];
    $userID = $_SESSION['user_id'];

    
    if (!preg_match('/^[a-zA-Z]+\s[a-zA-Z]+$/', $cardName)) {
        // Validate the card name
        $chargeError = "Please enter the name on the card.";
    }  elseif (!preg_match('/^\d{16}$/', $cardNumber)) {
        // Validate the card number
        $chargeError = "Card number must be 16 digits.";
    } elseif (!preg_match('/^\d{3}$/', $cvv)) {
        // Validate the CVV
        $chargeError = "CVV must be 3 digits.";
    } elseif (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $expiryDate)) {
        // Validate the expiry date
        $chargeError = "Expiry date must be in MM/YY format.";
    } else {
        // Start transaction
        $conn->begin_transaction();

        // Update balance in users table
        $stmt = $conn->prepare("UPDATE users SET Balance = Balance + ? WHERE UserID = ?");
        $stmt->bind_param("ds", $amount, $userID);

        // Execute the balance update statement
        $stmt->execute();

        // Close statement
        $stmt->close();

        // Prepare statement for transactions table
        $stmt = $conn->prepare("INSERT INTO transactions (UserID, transaction_type, amount) VALUES (?, 'Incoming', ?)");
        $stmt->bind_param("sd", $userID, $amount);

        // Execute the transactions statement
        $stmt->execute();

        // Close statement
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Success message
        $chargeSuccess = "Successfully charged $$amount to your card";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Charging - Add Money</title>
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
        input[type="password"],
        input[type="number"] {
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

        .error-message {
            color: red;
            margin-top: 5px;
            text-align: center;
        }

        .success-message {
            color: green;
            margin-top: 5px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Add Money</h2>
        <form id="chargeForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="cardName">Name on Card:</label>
            <input type="text" id="cardName" name="cardName" placeholder="Enter your name" required><br>

            <label for="cardNumber">Card Number:</label>
            <input type="text" id="cardNumber" name="cardNumber" placeholder="Enter card number" required><br>

            <label for="expiryDate">Expiry Date:</label>
            <input type="text" id="expiryDate" name="expiryDate" placeholder="MM/YY" required><br>

            <label for="cvv">CVV:</label>
            <input type="text" id="cvv" name="cvv" placeholder="Enter CVV" required><br>

            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" placeholder="Enter amount" required><br>

            <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

            <input type="submit" value="Charge Card">
        </form>

        <form action="account.php" method="post">
            <input type="submit" value="Go Back">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">

        </form>
        <form action="logout.php" method="post">
            <input type="submit" value="Logout">
            <!-- <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>"> -->

        </form>

        <div id="chargeStatus">
            <!-- Display error message -->
            <?php if (isset($chargeError)) : ?>
                <p class="error-message"><?php echo $chargeError; ?></p>
            <?php endif; ?>

            <!-- Display success message -->
            <?php if (isset($chargeSuccess)) : ?>
                <p class="success-message"><?php echo $chargeSuccess; ?></p>
            <?php endif; ?>
        </div>

        <!-- Other forms -->
        <!-- Your existing other forms -->
    </div>

    <script>
        // Automatically format expiry date input
        const expiryInput = document.getElementById('expiryDate');

        expiryInput.addEventListener('input', function(event) {
            const inputLength = event.target.value.length;
            const maxLength = 5; // MM/YY format

            if (inputLength === 2 && event.inputType !== 'deleteContentBackward') {
                event.target.value += '/';
            }

            if (inputLength > maxLength) {
                event.target.value = event.target.value.slice(0, maxLength);
            }
        });
    </script>

</body>

</html>
