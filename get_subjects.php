<?php

 // Connect to the database
 $conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

 // Check if the connection was successful
 if (!$conn) {
     die("Connection failed: " . mysqli_connect_error());
 }

// Check if user ID is provided via POST
if (isset($_POST['userId'])) {
    $userId = $_POST['userId'];

    // Fetch subjects for the selected user from the database
    $sql = "SELECT * FROM subjects WHERE user_id = $userId";
    $result = mysqli_query($conn, $sql);

    $subjects = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $subjectId = $row['id'];
            $subjectName = $row['name'];
            // Build an array of subjects
            $subject = array(
                'id' => $subjectId,
                'name' => $subjectName
            );
            $subjects[] = $subject;
        }
    }

    // Return the subjects as JSON
    echo json_encode(array('subjects' => $subjects));
}
?>

















?>