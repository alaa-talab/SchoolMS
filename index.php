<?php
// Connect to the database
$conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form has been submitted
if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
    // Get the form data and sanitize user input
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Check if the name is at least 8 characters long
if (strlen($name) < 8) {
    $response = array("status" => "error", "message" => "Name must be at least 8 characters long.");
    echo json_encode($response);
    exit;
}

// Check if the name contains capital letters or spaces
if (preg_match('/[A-Z\s]/', $name)) {
    $response = array("status" => "error", "message" => "Name must not contain capital letters or spaces.");
    echo json_encode($response);
    exit;
}

    // Check if the passwords match
    if ($password !== $_POST['password_confirm']) {
        $response = array("status" => "error", "message" => "Passwords do not match.");
        echo json_encode($response);
        exit;
    }

    // Check if the email is valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response = array("status" => "error", "message" => "Invalid email address: " . $email);
    echo json_encode($response);
    exit;
}

    // Check if the password is at least 8 characters long and has both upper and lower cases
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[^A-Za-z0-9]/', $password)) {
        $response = array("status" => "error", "message" => "Password must be at least 8 characters long, have both upper and lower cases, and contain a special character.");
        echo json_encode($response);
        exit;
    }

    // Check if there are no error messages
    if (!isset($errorMessage)) {
        // Check if the email already exists in the database
        $existingEmailQuery = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $existingEmailQuery);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $existingEmailResult = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($existingEmailResult) > 0) {
            $response = array("status" => "error", "message" => "Email already exists.");
            echo json_encode($response);
            exit;
        } else {
            // Set default values for role and status
            $defaultRole = 'user';
            $defaultStatus = 'inactive';

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $sql = "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $hashedPassword, $defaultRole, $defaultStatus);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                $response = array("status" => "success", "message" => "Successfully signed up.");
                echo json_encode($response);
                exit;
            } else {
                $response = array("status" => "error", "message" => "Error signing up.");
                echo json_encode($response);
                exit;
            }
        }
    }

    // Close the connection to the database
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        .error-message {
            color: red;
            margin-bottom: 10px;
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
h1 {

    color: white;   
}
label {

    color: white;
}
p {
    color: white;
}

a {
    font-weight: bold;
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
<div>
<div class="wave"></div>
     <div class="wave"></div>
     <div class="wave"></div>
<div class="container">
    <h1 class="text-center mt-5">Sign Up</h1>
    <div id="response-message"></div>
    <div class="row justify-content-center">
        <div class="col-md-6">
        <div id="particles-js"></div>
            <form action="index.php" method="post" id="signup-form">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="password-confirm" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="password-confirm" name="password_confirm" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary" id="signup-button">Sign Up</button>
                </div>
                <div class="mt-3 text-center">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

<script>
    $(document).ready(function() {
    $('#signup-form').submit(function(e) {
        e.preventDefault();
        $('#signup-button').attr('disabled', true); // Disable the submit button

        $.ajax({
            url: 'index.php', // Corrected URL
            type: 'post',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = 'login.php?status=success'; // Redirect to login page
                } else if (response.status === 'error') {
                    $('#response-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
                $('#signup-button').attr('disabled', false); // Enable the submit button
            },
            error: function() {
                $('#response-message').html('<div class="alert alert-danger">Error occurred. Please try again later.</div>');
            }
        });
    });
});

</script>
