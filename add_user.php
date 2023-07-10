<?php
  if ($_POST['action'] == 'add') {
    // Get the user information from the form
    $userName = $_POST['name'];
    $userEmail = $_POST['email'];
    $userPassword = $_POST['password'];
    $userRole = $_POST['role'];
    $userStatus = $_POST['status'];

     // Connect to the database
   $conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

    // Hash the password
    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);

    // Insert the new user into the database
    $sql = "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $userName, $userEmail, $hashedPassword, $userRole, $userStatus);
    mysqli_stmt_execute($stmt);

    // Get the inserted user's ID
    $userId = mysqli_insert_id($conn);

    // Display success message
    $message = "User added successfully.";


   

  

    // Close the database connection
    mysqli_close($conn);
     
  }
  
?>
