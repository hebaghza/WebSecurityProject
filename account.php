<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account</title>

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
            text-align: center;
            /* Center align the content */
        }
        
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        /* Style for the buttons */
        
        .action-button {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 10px;
            /* Adjust margin to create space between buttons */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            /* Remove underline from anchor tags */
        }
        
        .action-button:hover {
            background-color: #45a049;
        }
    </style>

</head>

<?php
    session_start();
    include('includes/csrftoken.php');
    
    // Validate CSRF token
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['_token']) {
            // CSRF token is missing or incorrect, handle the error
            // For example, redirect user to an error page
            header('Location: error.php');
            exit();
        }
        // Proceed with processing form data
    }

     // Check if 'role' key is set in $_SESSION
     $role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

    if ($_SESSION['role'] == 'admin') {
        $view_transaction_button = '<a href="view.php" class="action-button">View Transaction</a>';
        $add_admin_button = '<a href="includes/add_admin.php" class="action-button">Add Admin</a>';
        $transfer_button = '';
        $add_money_button = '';
        $password_button = '';
        $phone_button = '';
    } else {
        $view_transaction_button = '';
        $add_admin_button = '';
        $transfer_button = '<a href="transfer.php" class="action-button">Transfer Money</a>';
        $add_money_button = '<a href="add_money.php" class="action-button">Add Money</a>';
        $password_button = '<a href="change_pass.php" class="action-button">Change The password</a>';
        $phone_button = '<a href="change_phone.php" class="action-button">Change The phone number</a>';
    }
?>

<body>

    <h2>Welcome To Your Account</h2>

    <div id="accountDetails">
        <!-- User account details will be displayed here -->
    </div>


    <div id="balanceContainer">
        <!-- Balance will be displayed here -->
    </div>


    <div id="Services">
        <!-- Buttons to perform actions -->
        <?php echo $transfer_button; ?>
        <?php echo $add_money_button; ?>
        <?php echo $view_transaction_button; ?>
        <?php echo $add_admin_button; ?>
        <?php echo $password_button; ?>
        <?php echo $phone_button; ?>

        <form action="logout.php" method="post">
            <input type="submit" value="Logout" class="action-button">
        </form>
        <?php
            // Remove .DS_Store file
            $ds_store_file = '.DS_Store';
            if (file_exists($ds_store_file)) {
                unlink($ds_store_file);
            }
        ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        // This is a dummy function to simulate fetching user account details
        function fetchAccountDetails() {
            // Replace this with your actual logic to fetch user account details from the server
            // For demonstration purposes, I'm using static data
           

            // Display user account details
            var accountDetails = document.getElementById("accountDetails");
            accountDetails.innerHTML = `
           
        `;
        }

        $(document).ready(function () {
            // Fetch balance from the server
            $.ajax({
                url: 'fetch_balance.php', // Endpoint to fetch the balance
                method: 'GET',
                success: function (response) {
                    // Update HTML content with balance
                    $('#balanceContainer').html(`<p><strong>Your Account Balance:</strong> ${response.balance}</p>`);
                },
                error: function (xhr, status, error) {
                    console.error(error); // Log any errors to the console
                }
            });
        });

        // Call the function to fetch and display user account details when the page loads
        fetchAccountDetails();
    </script>

</body>

</html>
