<?php
// Connect to the database
$conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['action']) && $_POST['action'] == 'delete') {
    // Get the user ID from the form
    $userId = $_POST['userId'];
  // Delete the user from the database
$sql = "DELETE FROM users WHERE id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);



    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Return the success message
        echo "User with ID {$userId} deleted successfully.";
    } 
 else {
    echo "Failed to delete user." . mysqli_error($conn);
}

// Check for any MySQL errors
if ($stmt === false) {
    echo "Error: " . mysqli_error($conn);
}

// Close the statement
mysqli_stmt_close($stmt);

// Close the database connection
mysqli_close($conn);
}