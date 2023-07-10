<?php
// Connect to the database
$conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

// Retrieve the users from the database
$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);

// Loop through the users and build the HTML table rows
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['id'];
    echo "<tr>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['role'] . "</td>";
    echo "<td>" . $row['status'] . "</td>";
    echo "<td>";
    echo "<button class='btn btn-sm btn-primary edit-user-button' data-toggle='modal' data-target='#edit-user-modal' data-user-id='" . $row['id'] . "' data-user-name='" . $row['name'] . "' data-user-email='" . $row['email'] . "' data-user-password='" . $row['password'] . "' data-user-role='" . $row['role'] . "' data-user-status='" . $row['status'] . "'>Edit</button>";
    echo "<button class='btn btn-sm btn-danger delete-user-button' onclick=\"DeleteUser($id)\">Delete</button>";
    echo "</td>";
    echo "</tr>";
}

// Close the database connection
mysqli_close($conn);
?>
