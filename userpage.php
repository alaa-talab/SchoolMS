<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    // Redirect to the login page or display an error message
    echo "<div class='container'><div class='alert alert-danger'>You are not logged in. Please login to view this page.</div></div>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
        }
        .card {
            margin-top: 20px;
        }
        h2, h3, p {
            margin-bottom: 10px;
        }
        ul {
            padding-left: 20px;
        }
        .navbar .nav-item {
            margin-right: 10px;
        }
       

        #chatMessages {
    display: flex;
    flex-direction: column;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px;
    max-height: 300px;
    overflow-y: auto;
  }

  .message {
    display: flex;
    flex-direction: column;
    padding: 8px 12px;
    border-radius: 20px;
    margin-bottom: 10px;
  }

  .message.sent {
    background-color: #dcf8c6;
    align-self: flex-end;
    text-align: right;
  }

  .message.received {
    background-color: #f1f0f0;
    align-self: flex-start;
    text-align: left;
  }

  .message p {
    margin: 0;
  }

  .message .username {
    font-weight: bold;
    margin-bottom: 5px;
  }

 #chatForm {
    display: flex;
    align-items: center;
  }

  #chatForm input {
    flex: 1;
    margin-right: 10px;
  } 

  body {
    margin: auto;
    font-family: 'Raleway', sans-serif;
    overflow: auto;
    background: linear-gradient(315deg, rgba(101,0,94,1) 3%, rgba(60,132,206,1) 38%, rgba(48,238,226,1) 68%, rgba(255,25,25,1) 98%);
    animation: gradient 15s ease infinite;
    background-size: 400% 400%;
    background-attachment: fixed;
}

@keyframes gradient {
    0% {
        background-position: 0% 0%;
    }
    50% {
        background-position: 100% 100%;
    }
    100% {
        background-position: 0% 0%;
    }
}

.wave {
    background: rgb(255 255 255 / 25%);
    border-radius: 1000% 1000% 0 0;
    position: fixed;
    width: 200%;
    height: 12em;
    animation: wave 10s -3s linear infinite;
    transform: translate3d(0, 0, 0);
    opacity: 0.8;
    bottom: 0;
    left: 0;
    z-index: -1;
}

.wave:nth-of-type(2) {
    bottom: -1.25em;
    animation: wave 18s linear reverse infinite;
    opacity: 0.8;
}

.wave:nth-of-type(3) {
    bottom: -2.5em;
    animation: wave 20s -1s reverse infinite;
    opacity: 0.9;
}

@keyframes wave {
    2% {
        transform: translateX(1);
    }

    25% {
        transform: translateX(-25%);
    }

    50% {
        transform: translateX(-50%);
    }

    75% {
        transform: translateX(-25%);
    }

    100% {
        transform: translateX(1);
    }
}

  
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">User Page</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav"
            aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>


    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>
<div>
     <div class="wave"></div>
     <div class="wave"></div>
     <div class="wave"></div>
<div class="container">
    <?php
    

    // Check if the user is logged in
    if (!isset($_SESSION['name'])) {
        // Redirect to the login page or display an error message
        echo "<div class='container'><div class='alert alert-danger'>You are not logged in. Please login to view this page.</div></div>";
        exit();
    }

    // Connect to the database
    $conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

    // Get the username of the logged-in user
    $username = $_SESSION['name'];

    // Retrieve user information and subjects for the logged-in user
    $sql = "SELECT u.name, u.email, u.role, us.subject_n, us.mark, s.min_mark
            FROM users u
            LEFT JOIN user_subject us ON u.name = us.user_name
            LEFT JOIN subject s ON us.subject_n = s.subject_name
            WHERE u.name = '$username'";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Fetch all rows from the result as an associative array
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Store the user information and subjects as an array
        $user = null;
        foreach ($data as $row) {
            if (!$user) {
                $user = [
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'role' => $row['role'],
                    'subjects' => []
                ];
            }
            if (!empty($row['subject_n'])) {
                $user['subjects'][] = [
                    'subject' => $row['subject_n'],
                    'mark' => $row['mark'],
                    'min_mark' => $row['min_mark']
                ];
            }
        }

        // Check if a success message is set and display it
        if (isset($_GET['message'])) {
            $message = $_GET['message'];
            echo "<div class='container'><div class='alert alert-success'>$message</div></div>";
        }

        // Output the user information and subjects
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo "<h2 class='text-center'>User: " . $user['name'] . "</h2>";
        echo "<p>Email: " . $user['email'] . "</p>";
        echo "<p>Role: " . $user['role'] . "</p>";

        // Check if any subjects exist for the user
        if (count($user['subjects']) > 0) {
            echo "<h3>Subjects:</h3>";
            echo "<ul>";
            foreach ($user['subjects'] as $subject) {
                echo "<li>" . $subject['subject'] . " - Mark: " . $subject['mark'] . " (Minimum: " . $subject['min_mark'] . ")</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No subjects found for this user.</p>";
        }

        echo '</div>';
        echo '</div>';
    } else {
        echo "<p>Error retrieving user information: " . mysqli_error($conn) . "</p>";
    }

    // Close the database connection
    mysqli_close($conn);
    ?>
</div>

<div class="container mt-4">    
   
<div id="chatMessages" class="mt-4">
<?php
// Connect to the database
$conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test"); // Update with your database credentials

// Retrieve the chat messages from the database
$query = "SELECT * FROM chat_messages ORDER BY id ASC";
$result = mysqli_query($conn, $query);

// Display the chat messages
while ($row = mysqli_fetch_assoc($result)) {
  $messageClass = ($row['username'] === $username) ? 'sent' : 'received';
  echo '<div class="message ' . $messageClass . '">';
  echo '<p class="username">' . $row['username'] . '</p>';
  echo '<p>' . $row['message'] . '</p>';
  echo '</div>';
}

// Close the database connection
mysqli_close($conn);
?>
</div>
<div id="chatForm" class="mt-4">
    <form>
      <div class="input-group">
        <input type="text" name="chat_message" id="chatMessage" class="form-control" placeholder="Type your message...">
        <div class="input-group-append">
        <button type="submit" class="btn btn-primary btn-sm">Send</button>
        </div>
      </div>
    </form>
  </div>
</div>
</div>





<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://js.pusher.com/7.0/pusher.min.js"></script>

<script>
  $(document).ready(function() {
    // Initialize Pusher with your Pusher app credentials
    var pusher = new Pusher('c01901f33b7b7a6f34ff', {
      cluster: 'ap2',
      encrypted: true
    });

    // Subscribe to the chat channel
    var channel = pusher.subscribe('chat');

    // Bind to the 'message' event on the channel
    channel.bind('message', function(data) {
      // Append the received message to the chatMessages div
      var message = '<div class="message">';
      message += '<p class="username">' + data.username + '</p>';
      message += '<p>' + data.message + '</p>';
      message += '</div>';

      $('#chatMessages').append(message);
    });

    // Load the chat messages using AJAX
    function loadChatMessages(username) {
      $.ajax({
        url: 'get_messages.php',
        method: 'GET',
        data: { username: username }, // Pass the username as a parameter
        success: function(response) {
          $('#chatMessages').html(response);
        }
      });
    }

    // Call the loadChatMessages function initially with the username
    var username = '<?php echo $username; ?>';
    loadChatMessages(username);

    // Handle form submission for sending chat messages
    $('#chatForm').submit(function(e) {
      e.preventDefault();
      var message = $('#chatMessage').val();

      // Send the message via AJAX to the server
      $.ajax({
        url: 'send_message.php',
        method: 'POST',
        data: { message: message, username: username },
        success: function(response) {
          // Clear the chat input field after sending the message
          $('#chatMessage').val('');

          // Load the updated chat messages
          loadChatMessages(username);
        }
      });
    });
  });
</script>


</body>
</html>
