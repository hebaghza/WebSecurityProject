<?php
include('includes/db_connection.php');
include('includes/csrftoken.php'); 

$amount = "";
$amount_err = "";
$phone_err = "";

if (!isset($_SESSION['phone'])) {
    header("location: login.php");
    exit;
}

$userID = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['_token']) {
        // CSRF token validation failed, return error
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "CSRF token validation failed"]);
        exit();
    }

    if (!preg_match("/^\d{10}$/", trim($_POST["recipientPhoneNumber"]))) {
        $phone_err = "Invalid phone number format. Please enter 10 digits.";
      } elseif (empty(trim($_POST["recipientPhoneNumber"]))) {
        $phone_err = "Please enter recipient's phone number.";
      } else {
        $recipientPhoneNumber = trim($_POST["recipientPhoneNumber"]);
    }

    if (empty(trim($_POST["amount"]))) {
        $amount_err = "Please enter the amount.";
    } else {
        $amount = trim($_POST["amount"]);
    }

    if (empty($amount_err) && empty($phone_err)) {
        $sql = "SELECT * FROM users WHERE PhoneNumber = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_POST["recipientPhoneNumber"]);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Recipient's phone number not found, return error
            http_response_code(400); // Bad Request
            echo json_encode(["phone_err" => "Recipient's phone number not found."]);
            $phone_err = "Recipient's phone number not found.";
            exit;
        } else {
            $row = $result->fetch_assoc();
            $recipientUserID = $row['UserID'];
            $newBalanceRecipient = $row['Balance'] + $amount;

            $get_sender_balance_sql = "SELECT Balance FROM users WHERE UserID = ?";
            $get_sender_balance_stmt = $conn->prepare($get_sender_balance_sql);
            $get_sender_balance_stmt->bind_param("s", $userID);
            $get_sender_balance_stmt->execute();
            $get_sender_balance_result = $get_sender_balance_stmt->get_result();
            
            if ($get_sender_balance_result->num_rows === 1) {
                $sender_row = $get_sender_balance_result->fetch_assoc();
                $currentSenderBalance = $sender_row['Balance'];

                if ($currentSenderBalance >= $amount) {
                    $newBalanceSender = $currentSenderBalance - $amount;

                    $update_sql_sender = "UPDATE users SET Balance = ? WHERE UserID = ?";
                    $update_stmt_sender = $conn->prepare($update_sql_sender);
                    $update_stmt_sender->bind_param("ss", $newBalanceSender, $userID);
                    $update_stmt_sender->execute();

                    $update_sql_recipient = "UPDATE users SET Balance = ? WHERE UserID = ?";
                    $update_stmt_recipient = $conn->prepare($update_sql_recipient);
                    $update_stmt_recipient->bind_param("ss", $newBalanceRecipient, $recipientUserID);
                    $update_stmt_recipient->execute();                    

                    $insert_out_transaction_sql = "INSERT INTO transactions (UserID, transaction_type, amount) VALUES (?, 'Outcoming', ?)";
                    $insert_out_transaction_stmt = $conn->prepare($insert_out_transaction_sql);
                    $insert_out_transaction_stmt->bind_param("ss", $userID, $amount);
                    $insert_out_transaction_stmt->execute();

                    $insert_in_transaction_sql = "INSERT INTO transactions (UserID, transaction_type, amount) VALUES (?, 'Incoming', ?)";
                    $insert_in_transaction_stmt = $conn->prepare($insert_in_transaction_sql);
                    $insert_in_transaction_stmt->bind_param("ss", $recipientUserID, $amount);
                    $insert_in_transaction_stmt->execute();

                    $update_stmt_sender->close();
                    $update_stmt_recipient->close();
                    $insert_in_transaction_stmt->close();
                    $insert_out_transaction_stmt->close();

                    // Transfer successful, return success message
                    echo json_encode(["success" => "Transfer of $$amount to {$_POST["recipientPhoneNumber"]} successful!"]);
                } else {
                    // Insufficient balance, return error
                    echo json_encode(["error" => "Insufficient balance to make the transfer!"]);
                }
            } else {
                // Sender user not found, return error
                echo json_encode(["error" => "Sender user not found!"]);
            }

            $get_sender_balance_stmt->close();
        }

        $stmt->close();
        $conn->close();
        exit;
    } else {
        // Invalid input, return error
        echo json_encode(["error" => "Please provide recipient's phone number and amount!"]);
        exit;
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

        .error {
            color: red;
        }
    </style>

<span class="error" id="server_phone_err"><?php echo $phone_err; ?></span>


</head>
<body>

<div class="container">
    <h2>Money Transfer</h2>
    <form id="transferForm" method="post">
        <label for="recipientPhoneNumber">Recipient's Phone Number:</label>
        <input type="text" id="recipientPhoneNumber" name="recipientPhoneNumber" placeholder="Enter recipient's phone number" required pattern="[0-9]{10}" title="Phone number should be 10 digits">
        <span class="error" id="phone_err"><?php echo $phone_err; ?></span>

        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" placeholder="Enter amount" required>
        <span class="error" id="amount_err"><?php echo $amount_err; ?></span>

        <input type="submit" value="Transfer">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
    </form>
    <div id="transferStatus"></div>
    
    <form action="account.php" method="post">
        <input type="submit" value="Go Back">
        <input type="hidden" name="_token" value="<?php echo $_SESSION['_token']; ?>">
    </form>
    <form action="logout.php" method="post">
        <input type="submit" value="Logout">
    </form>

    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
    $(document).ready(function () {
        $('#transferForm').submit(function (event) {
            event.preventDefault(); // Prevent form submission
            var recipientPhoneNumber = $('#recipientPhoneNumber').val();
            var amount = $('#amount').val();
            var token = $('input[name="_token"]').val(); // Get the CSRF token value
            $.ajax({
                type: 'POST',
                url: 'transfer.php',
                data: {
                    recipientPhoneNumber: recipientPhoneNumber,
                    amount: amount,
                    _token: token
                },
                dataType: 'json', // Expect JSON response
                success: function (response) {
                    if (response.hasOwnProperty('amount_err')) {
                        $('#amount_err').html(response.amount_err);
                    } else if (response.hasOwnProperty('error')) {
                        $('#transferStatus').html('<p class="error">' + response.error + '</p>');
                    } else if (response.hasOwnProperty('success')) {
                        $('#transferStatus').html('<p>' + response.success + '</p>');
                    }
                },
                error: function (xhr, status, error) {
                    // Handle AJAX errors here
                    console.error(xhr.responseText);
                    // Display error message in transferStatus
                    $('#transferStatus').html('<p class="error"> Recipient phone number not found </p>');
                }
            });
        });

        // Additionally, handle form submission in case JavaScript is disabled
        $('#transferForm').on('submit', function() {
            // If JavaScript is disabled, this function will handle form submission
            return validateForm();
        });

        function validateForm() {
            // Perform form validation here
            // If validation fails, return false to prevent form submission
            // If validation succeeds, return true to allow form submission
        }
    });
</script>

</body>
</html>
