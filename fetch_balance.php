<?php
session_start();
include('includes/db_connection.php');

// Initialize balance variable
$balance = 0.00;

// Fetch balance from the database
$sql = "SELECT Balance FROM users WHERE PhoneNumber = ?";

if ($stmt = $conn->prepare($sql)) {
    // Bind variables to the prepared statement as parameters
    $stmt->bind_param("s", $param_phone);

    // Set parameters
    $param_phone = $_SESSION['phone'];

    // Attempt to execute the prepared statement
    if ($stmt->execute()) {
        // Store result
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // Bind result variables
            $stmt->bind_result($db_balance);
            if ($stmt->fetch()) {
                $balance = $db_balance;
            }
        }
    }
}

// Close statement
$stmt->close();

// Close connection
$conn->close();

// Prepare and send the response
$response = array(
    'balance' => $balance
);

header('Content-Type: application/json');
echo json_encode($response);
?>
