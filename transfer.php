<?php
session_start();
include('includes/db_connection.php');

// Define variables and initialize with empty values
$amount = "";
$amount_err = "";
$phone_err = "";

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['phone'])) {
    header("location: login.php");
    exit;
}

// Get the user ID based on the phone number in the session
$userID = $_SESSION['user_id'];

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if recipient phone number is empty
    if (empty(trim($_POST["recipientPhoneNumber"]))) {
        $phone_err = "Please enter recipient's phone number.";
    } else {
        $recipientPhoneNumber = trim($_POST["recipientPhoneNumber"]);
    }

    // Check if amount is empty
    if (empty(trim($_POST["amount"]))) {
        $amount_err = "Please enter the amount.";
    } else {
        $amount = trim($_POST["amount"]);
    }

    // Validate amount
    if (empty($amount_err) && empty($phone_err)) {
        // Prepare and execute the SQL query
        $sql = "SELECT * FROM users WHERE PhoneNumber = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_POST["recipientPhoneNumber"]);
        $stmt->execute();
        $result = $stmt->get_result();

        // If no matching phone number is found, display an error message
        if ($result->num_rows === 0) {
            echo "<p> Recipient's phone number not found!</p>";
        } else {

            // If a matching phone number is found, update the balance and insert transactions
            $row = $result->fetch_assoc(); // Fetch as associative array
            $recipientUserID = $row['UserID']; // Retrieve the user ID of the recipient
            $newBalanceRecipient = $row['Balance'] + $amount;

            // Deduct the amount from the sender's balance
            $get_sender_balance_sql = "SELECT Balance FROM users WHERE UserID = ?";
            $get_sender_balance_stmt = $conn->prepare($get_sender_balance_sql);
            $get_sender_balance_stmt->bind_param("s", $userID);
            $get_sender_balance_stmt->execute();
            $get_sender_balance_result = $get_sender_balance_stmt->get_result();
            
            // Fetch the current balance of the sender
            if ($get_sender_balance_result->num_rows === 1) {
                $sender_row = $get_sender_balance_result->fetch_assoc();
                $currentSenderBalance = $sender_row['Balance'];

                // Check if the sender has enough balance to make the transfer
                if ($currentSenderBalance >= $amount) {
                    $newBalanceSender = $currentSenderBalance - $amount;

                    // Update the balance for the sender in the database
                    $update_sql_sender = "UPDATE users SET Balance = ? WHERE UserID = ?";
                    $update_stmt_sender = $conn->prepare($update_sql_sender);
                    $update_stmt_sender->bind_param("ss", $newBalanceSender, $userID);
                    $update_stmt_sender->execute();

                    // Update the balance for the recipient in the database
                    $update_sql_recipient = "UPDATE users SET Balance = ? WHERE UserID = ?";
                    $update_stmt_recipient = $conn->prepare($update_sql_recipient);
                    $update_stmt_recipient->bind_param("ss", $newBalanceRecipient, $recipientUserID);
                    $update_stmt_recipient->execute();                    

                    // Insert outgoing transaction into the transactions table
                    $insert_out_transaction_sql = "INSERT INTO transactions (UserID, transaction_type, amount) VALUES (?, 'Outcoming', ?)";
                    $insert_out_transaction_stmt = $conn->prepare($insert_out_transaction_sql);
                    $insert_out_transaction_stmt->bind_param("ss", $userID, $amount);
                    $insert_out_transaction_stmt->execute();

                    // Insert incoming transaction into the transactions table
                    $insert_in_transaction_sql = "INSERT INTO transactions (UserID, transaction_type, amount) VALUES (?, 'Incoming', ?)";
                    $insert_in_transaction_stmt = $conn->prepare($insert_in_transaction_sql);
                    $insert_in_transaction_stmt->bind_param("ss", $recipientUserID, $amount);
                    $insert_in_transaction_stmt->execute();

                    $update_stmt_sender->close();
                    $update_stmt_recipient->close();
                    $insert_in_transaction_stmt->close();
                    $insert_out_transaction_stmt->close(); // Close the insert_out_transaction_stmt here

                    // Display success message
                    echo "<p>Transfer of $$amount to {$_POST["recipientPhoneNumber"]} successful!</p>";
                } else {
                    // If the sender does not have enough balance, display an error message
                    echo "<p>Insufficient balance to make the transfer!</p>";
                }
            } else {
                // Handle case where sender user not found
                echo "Sender user not found!";
            }

            $get_sender_balance_stmt->close();

        }

        $stmt->close();
        // Close the database connection
        $conn->close();
        exit; // Stop further script execution
    } else {
        echo "<p>Please provide recipient's phone number and amount!</p>";
        exit; // Stop further script execution
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Transfer</title>
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
    </style>
</head>

<body>

    <h2>Money Transfer</h2>

    <form id="transferForm" method="post">
        <label for="recipientPhoneNumber">Recipient's Phone Number:</label>
        <input type="text" id="recipientPhoneNumber" name="recipientPhoneNumber" placeholder="Enter recipient's phone number" required pattern="[0-9]{10}" title="Phone number should be 10 digits">
        <span class="error"><?php echo $phone_err; ?></span>

        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" placeholder="Enter amount" required>
        <span class="error"><?php echo $amount_err; ?></span>

        <input type="submit" value="Transfer">
    </form>

    <div id="transferStatus"></div>

    <form action="account.php" method="post">
        <input type="submit" value="Go Back">
    </form>
    <form action="logout.php" method="post">
        <input type="submit" value="Logout">
    </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#transferForm').submit(function (event) {
                event.preventDefault(); // Prevent form submission

                var recipientPhoneNumber = $('#recipientPhoneNumber').val();
                var amount = $('#amount').val();

                // AJAX call to the server to handle the transfer
                $.ajax({
                    type: 'POST',
                    url: 'transfer.php', // The PHP script that handles the transfer
                    data: {
                        recipientPhoneNumber: recipientPhoneNumber,
                        amount: amount
                    },
                    success: function (response) {
                        $('#transferStatus').html(response);
                    }
                });
            });
        });
    </script>

</body>

</html>