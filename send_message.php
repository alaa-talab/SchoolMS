<?php
session_start();

if (!isset($_SESSION['name'])) {
  // User is not logged in
  echo json_encode(['success' => false, 'message' => 'You are not logged in.']);
  exit();
}

// Get the message and username from the AJAX request
$message = $_POST['message'];
$username = $_POST['username'];

// Here, you can perform any additional validation or processing of the message
$conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test"); // Update with your database credentials

// Prepare the query to insert the message
$query = "INSERT INTO chat_messages (username, message) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ss", $username, $message);
mysqli_stmt_execute($stmt);

// Close the statement and database connection
mysqli_stmt_close($stmt);
mysqli_close($conn);

// Broadcast the message to the chat channel using Pusher
require 'vendor/autoload.php'; // Include the Pusher PHP library
$pusher = new Pusher\Pusher(
  'c01901f33b7b7a6f34ff',
  '9e695721cb90ff5328dd',
  '1629580',
  ['cluster' => 'ap2']
);
$pusher->trigger('chat', 'message', ['username' => $username, 'message' => $message]);

echo json_encode(['success' => true]);
?>
