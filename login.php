<?php
session_start();

// Check if the login form has been submitted
if (isset($_POST['email']) && isset($_POST['password'])) {
    $emailOrName = $_POST['email'];
    $password = $_POST['password'];

    // Connect to the database
    $conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("SELECT * FROM users WHERE (email=? OR name=?)");
    $stmt->bind_param("ss", $emailOrName, $emailOrName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Fetch user data from the database
        $row = $result->fetch_assoc();

        // Verify the entered password against the stored hashed password
        if (password_verify($password, $row['password'])) {
            if ($row['status'] == 'inactive') {
                $response = array("status" => "inactive", "message" => "This account is inactive, please wait for the administration to activate your account.");
                echo json_encode($response);
                exit();
            } else {
                // Store the user data in the session
                $_SESSION['id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['role'] = $row['role'];

                // Create response array for successful login
                $response = array("status" => "success", "role" => $row['role']);
                echo json_encode($response);
                exit();
            }
        } else {
            $response = array("status" => "error", "message" => "Invalid email or password.");
            echo json_encode($response);
            exit();
        }
    } else {
        $response = array("status" => "error", "message" => "Invalid email or password.");
        echo json_encode($response);
        exit();
    }

    // Close the database connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700&display=swap" rel="stylesheet">
</head>
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
<body>
<div class="wave"></div>
<div class="wave"></div>
<div class="wave"></div>
<div class="container">
    <h1 class="text-center mt-5">Log In</h1>
    <div id="response-message"></div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div id="particles-js"></div>
            <form id="login-form">
                <div class="mb-3">
                    <label for="email" class="form-label">Email or Name</label>
                    <input type="text" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="text-center">
                <button type="submit" class="btn btn-primary">Log In</button>
</div>
                <div class="mt-3 text-center">
                    <p>Don't have an account? <a href="index.php">Signup</a></p>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#login-form').submit(function(e) {
        e.preventDefault();

        // Get form data
        var formData = $(this).serialize();

        // Send AJAX request
        $.ajax({
            type: 'POST',
            url: 'login.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Redirect the user based on their role
                    if (response.role === 'admin') {
                        window.location.href = 'adminpage.php';
                    } else if (response.role === 'user') {
                        window.location.href = 'userpage.php';
                    }
                } else if (response.status === 'inactive') {
                    $('#response-message').removeClass('alert-success alert-danger').addClass('alert-warning').text(response.message).show();
                } else {
                    $('#response-message').removeClass('alert-success alert-warning').addClass('alert-danger').text(response.message).show();
                }
            },
            error: function() {
                $('#response-message').removeClass('alert-success alert-warning').addClass('alert-danger').text('An error occurred. Please try again.').show();
            }
        });
    });
});
</script>
</body>
</html>
