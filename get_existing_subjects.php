<?php

// Connect to the database
$conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

// Retrieve subject names and IDs from the database
$sql = "SELECT subject_name FROM subject";
$result = mysqli_query($conn, $sql);
// Get the user name from the AJAX request
$userName = $_POST['userName'];

// Retrieve existing subjects for the user
$existingSubjects = [];
$sql = "SELECT subject_n FROM user_subject WHERE user_name = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $userName);
mysqli_stmt_execute($stmt);
$resultExistingSubjects = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($resultExistingSubjects)) {
    $existingSubjects[] = $row['subject_n'];
}


// Display the subjects in the modal
while ($row = mysqli_fetch_assoc($result)) {
    $subjectName = $row['subject_name'];
    $checked = in_array($subjectName, $existingSubjects) ? 'checked' : '';

    echo '<div class="form-check">';
    echo '<input class="form-check-input" type="checkbox" name="subjects[]" id="edit-subject-' . $subjectName . '" value="' . $subjectName . '" ' . $checked . '>';
    echo '<label class="form-check-label" for="edit-subject-' . $subjectName . '">' . $subjectName . '</label>';
    echo '</div>';
}


// Close the database connection
mysqli_close($conn);

?>
