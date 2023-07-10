<?php
  if ($_POST['action'] == 'add_subject') {
     // Get the user information from the form
     $subject_name = $_POST['subject_name'];
     $min_mark = $_POST['min_mark'];


     // Connect to the database
    $conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");



      // Insert the new subject into the database
      $sql = "INSERT INTO subject (subject_name, min_mark) VALUES (?, ?)";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "si", $subject_name, $min_mark);
      mysqli_stmt_execute($stmt);

 
            // Display success message
            $message = "Subject added successfully.";


   

  

    // Close the database connection
    mysqli_close($conn);
     
  }
  
?>