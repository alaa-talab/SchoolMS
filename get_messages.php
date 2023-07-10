<?php
if(isset($_GET['username'])) {
  $username = $_GET['username'];

  // Connect to the database
  $conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test"); // Update with your database credentials

  // Retrieve the chat messages from the database
  $query = "SELECT * FROM chat_messages ORDER BY id ASC";
  $result = mysqli_query($conn, $query);

  // Prepare the HTML string for chat messages
  $html = '';

  // Build the HTML for each chat message
  while ($row = mysqli_fetch_assoc($result)) {
    $messageClass = ($row['username'] === $username) ? 'sent' : 'received';
    $html .= '<div class="message ' . $messageClass . '">';
    $html .= '<p class="username">' . $row['username'] . '</p>';
    $html .= '<p>' . $row['message'] . '</p>';
    $html .= '</div>';
  }

  // Close the database connection
  mysqli_close($conn);

  // Return the HTML string
  echo $html;
}
?>
