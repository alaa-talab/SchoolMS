<?php
// Perform necessary database connection and setup
$conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Retrieve the timestamp from the AJAX request
$timestamp = $_GET['timestamp'] ?? 0;

// Query the database to fetch new messages since the given timestamp
// Adjust the SQL query based on your database schema
$query = "SELECT * FROM messages WHERE timestamp > :timestamp";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare the response data
$response = [
  'timestamp' => time(),
  'messages' => $messages
];

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

mysqli_close($conn);
?>
