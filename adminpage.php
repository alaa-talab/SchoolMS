<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .container {
            padding: 20px;
            background-color: white;
        }
        h1 {
            margin-top: 40px;
            margin-bottom: 20px;
        }
        .btn {
            margin-right: 10px;
        }
        table {
            margin-top: 20px;
        }
        #logout-button {
            margin-top: 20px;
        }
        /* Add User Modal */
        #add-user-modal {
            z-index: 9999;
        }
        /* Edit User Modal */
        #edit-user-modal {
            z-index: 9999;
        }
        /* Add Subject Modal */
        #add-subject-modal {
            z-index: 9999;
        }
        /* Set Mark Modal */
        #set-mark-modal {
            z-index: 9999;
        }
        /* Delete User Modal */
        #delete-user-modal {
            z-index: 9999;
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
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to the database
    $conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

    // Check if the "action" POST variable is set and perform the appropriate action
    if (isset($_POST['action'])) {
     
           
        if ($_POST['action'] == 'edit') {
            // Get the user information from the form
            $userId = $_POST['user_id'];
            $userName = $_POST['name'];
            $userEmail = $_POST['email'];
            $userPassword = $_POST['password'];
            $userRole = $_POST['role'];
            $userStatus = $_POST['status'];
        
            // Check if any changes are made
            $hasChanges = false;
            
            // Check if the name field is changed
            if ($userName != $_POST['original_name']) {
                $hasChanges = true;
            }
            
            // Check if the email field is changed
            if ($userEmail != $_POST['original_email']) {
                $hasChanges = true;
            }
            
            // Check if the role field is changed
            if ($userRole != $_POST['original_role']) {
                $hasChanges = true;
            }
            
            // Check if the status field is changed
            if ($userStatus != $_POST['original_status']) {
                $hasChanges = true;
            }
            
            // Check if the password field is changed
            if (!empty($userPassword)) {
                $hasChanges = true;
            }
        
            // Update the user's information in the database if changes are made
            if ($hasChanges) {
                // Check if the password field is empty
                if (empty($userPassword)) {
                    // Password field is empty, fetch the existing hashed password from the database
                    $sql = "SELECT password FROM users WHERE id=?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $userId);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $row = mysqli_fetch_assoc($result);
                    $hashedPassword = $row['password'];
                } else {
                    // Password field is not empty, hash the new password
                    $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);
                }
        
                // Update the user's information in the database
                $sql = "UPDATE users SET name=?, email=?, password=?, role=?, status=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sssssi", $userName, $userEmail, $hashedPassword, $userRole, $userStatus, $userId);
                mysqli_stmt_execute($stmt);
        
                // Display success message
                $message = "User information updated successfully.";
            }
        
            // Get the selected subjects from the form
            $selectedSubjects = isset($_POST['subjects']) ? $_POST['subjects'] : [];
        
            // Get the existing subjects assigned to the user
            $existingSubjects = [];
            $sql = "SELECT subject_n FROM user_subject WHERE user_name=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $userName);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                $existingSubjects[] = $row['subject_n'];
            }
        
            // Filter out already assigned subjects from the selected subjects
            $selectedSubjects = array_diff($selectedSubjects, $existingSubjects);
        
            // Prepare the placeholders for the bulk insert query
            $placeholders = [];
            $values = [];
            foreach ($selectedSubjects as $subjectName) {
                $placeholders[] = "(?, ?)";
                $values[] = $userName;
                $values[] = $subjectName;
            }
        
            // Insert the user-subject relations into the user_subject table using a single query
            if (!empty($placeholders)) {
                $sql = "INSERT INTO user_subject (user_name, subject_n) VALUES " . implode(", ", $placeholders);
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, str_repeat("ss", count($selectedSubjects)), ...$values);
                mysqli_stmt_execute($stmt);
            }
        
            $message = "Subjects added successfully.";
        }


        
    

    }

    if (isset($_POST['save'])) {
    
   
       foreach ($_POST['id'] as $id => $mark) {
               
           // Perform the update operation for each mark
           // Update the 'mark' field in the 'user_subject' table
           // Use $username and $subject to identify the record
   
           // Example code to update the mark
          
           $sql = "UPDATE user_subject SET mark=? WHERE id=? ";
           
           $stmt = mysqli_prepare($conn, $sql);
           mysqli_stmt_bind_param($stmt, "ii",$mark , $id);
           mysqli_stmt_execute($stmt);
           
       
   }
   
   
        // Redirect or display a success message after updating the marks
           // Redirect to the same page or display a success message
           $message = "Marks updated successfully.";
   
     
   
      
        
     }



    // Close the database connection
    mysqli_close($conn);

    // Refresh the page to show the updated user information
    header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message));
    exit();
}

// Check if a success message is set and display it
if (isset($_GET['message'])) {
    $message = $_GET['message'];
    echo '<div class="container"><div class="alert alert-success">' . $message . '</div></div>';
}
?>


<div>
<div class="wave"></div>
     <div class="wave"></div>
     <div class="wave"></div>
    <div class="container">
        <h1>Edit User Information</h1>
        <div class="d-flex justify-content-between">
        <button class="btn btn-sm btn-success add-user-button" data-toggle="modal" data-target="#add-user-modal">Add User</button>
        <button class="btn btn-sm btn-success add-user-button" data-toggle="modal" data-target="#add-subject-modal">Add Subject</button>
        <button class="btn btn-sm btn-success add-user-button" data-toggle="modal" data-target="#set-mark-modal">Set Mark</button>
       
    </div>

    <div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="user-list-container">
            <?php
            // Connect to the database
            $conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

            // Retrieve the users from the database
            $sql = "SELECT * FROM users";
            $result = mysqli_query($conn, $sql);

            // Loop through the users and display them in the table
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['name'] . "</td>";
                echo "<td>" . $row['email'] . "</td>";
                echo "<td>" . $row['role'] . "</td>";
                echo "<td>" . $row['status'] . "</td>";
                echo "<td>";
                echo "<button class='btn btn-sm btn-primary edit-user-button' data-toggle='modal' data-target='#edit-user-modal' data-user-id='" . $row['id'] . "' data-user-name='" . $row['name'] . "' data-user-email='" . $row['email'] . "' data-user-password='" . $row['password'] . "' data-user-role='" . $row['role'] . "' data-user-status='" . $row['status'] . "'>Edit</button>";
                echo "<button class='btn btn-sm btn-danger delete-user-button' data-user-id='" . $row['id'] . "' onClick='deleteUser(" . $row['id'] . ")'>Delete</button>";
                echo "</td>";
                echo "</tr>";
            }

            // Close the database connection
            mysqli_close($conn);
            ?>
        </tbody>
    </table>
</div>
<button id="logout-button" type="button" class="btn btn-sm btn-primary">Logout</button>
    </div>
    

   <!-- Add User Modal -->
<div class="modal fade" id="add-user-modal" tabindex="-1" role="dialog" aria-labelledby="add-user-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="add-user-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="add-user-modal-label">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="inactive">Inactive</option>
                            <option value="active">Active</option>
                        </select>
                    </div>
                    <input type="hidden" name="action" value="add">
                    <div id="message" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button id="add-user-submit" type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>


  

<!-- Edit User Modal -->
<div class="modal fade" id="edit-user-modal" tabindex="-1" role="dialog" aria-labelledby="edit-user-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit-user-modal-label">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-name">Name</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email</label>
                        <input type="email" class="form-control" id="edit-email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-password">Password</label>
                        <input type="password" class="form-control" id="edit-password" name="password" >
                    </div>
                    <div class="form-group">
                        <label for="edit-role">Role</label>
                        <select class="form-control" id="edit-role" name="role" required>
                        <option value="user">User</option>
                            <option value="admin">Admin</option>
                            
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-status">Status</label>
                        <select class="form-control" id="edit-status" name="status" required>
                        <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Subjects</label><br>
                        <div id="existing-subjects-container"></div>
        

                    </div>
                    <input type="hidden" name="user_id" id="edit-user-id" value="">
                    <input type="hidden" name="action" value="edit">
                </div>

                <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary">Save Changes</button>
</div>

            </form>
        </div>
    </div>
</div>



    <!-- Add Subject Modal -->
<div class="modal fade" id="add-subject-modal" tabindex="-1" role="dialog" aria-labelledby="add-subject-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="add-subject-form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="add-subject-modal-label">Add Subject</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="subject-name">Subject Name</label>
                        <input type="text" class="form-control" id="subject-name" name="subject_name" required>
                    </div>
                    <div class="form-group">
                        <label for="min-mark">Minimum Mark</label>
                        <input type="number" class="form-control" id="min-mark" name="min_mark" required>
                    </div>
                    <input type="hidden" name="action" value="add_subject">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

 <!-- set mark Modal -->
 <div class="modal fade" id="set-mark-modal" tabindex="-1" role="dialog" aria-labelledby="set-mark-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form  id="set-mark-form" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="set-mark-modal-label">Set Mark</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Subject</th>
                                <th>Mark</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Connect to the database
                            $conn = mysqli_connect("box5552", "ylepvemy_alaa", "Blue@079", "ylepvemy_test");

                            // Fetch user_subject records from the database
                            $sql = "SELECT * FROM user_subject";
                            $result = mysqli_query($conn, $sql);


                            // Display the records in the modal
                            while ($row = mysqli_fetch_assoc($result)) {
                                $username = $row['user_name'];
                                $subject = $row['subject_n'];
                                $mark = $row['mark'];

                                echo '<tr>';
                                echo '<td>' . $username . '</td>';
                                echo '<td>' . $subject . '</td>';
                                echo '<td><input type="text" class="form-control" name="id[' . $row['id'] . '] " value="' . $mark . '"></td>';
                                echo '</tr>';
                            }

                            // Close the database connection
                            mysqli_close($conn);
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="save" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>






    <!-- Delete User Modal -->
<div class="modal fade" id="delete-user-modal" tabindex="-1" role="dialog" aria-labelledby="delete-user-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <form id="delete-user-form" method="post" action="javascript:void(0)">

<div class="modal-header">
<h5 class="modal-title" id="delete-user-modal-label">Delete User</h5>
<div id="delete-user-container"></div>
<button type="button" class="close" data-dismiss="modal" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<div class="modal-body">
<p>Are you sure you want to delete this user?</p>
<input type="hidden" name="userId" value="<?php echo $userId; ?>">
<input type="hidden" name="action" value="delete">
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
<button type="button" id="delete-user-button" class="btn btn-danger" data-action="delete">Delete User</button>
</div>
</form>
        </div>
    </div>
</div>
                        </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
  $(document).ready(function() {
    $('.edit-user-button').click(function (e) {
        var userId = $(this).data('user-id');
        var userName = $(this).data('user-name');
        var userEmail = $(this).data('user-email');
        var userPassword = $(this).data('user-password');
        var userRole = $(this).data('user-role');
        var userStatus = $(this).data('user-status');

        $('#edit-user-id').val(userId);
        $('#edit-name').val(userName);
        $('#edit-email').val(userEmail);
        $('#edit-password').val('');
        $('#edit-role').val(userRole);
        $('#edit-status').val(userStatus);

        // Show the modal
    $('#edit-user-modal').modal('show');
    });
});



    
    
    $(document).ready(function() {
        $('#add-subject-form').on('submit', function(e) {
            e.preventDefault(); // Prevent form submission

            // Make an AJAX request to the add_user.php file
            $.ajax({
                type: 'POST',
                url: 'add_subject.php',
                data: $('#add-subject-form').serialize(),
                success: function(response) {
                    // Display the success message
                    $('#message').html(response).show();
                    updateUserList();
                    // Hide the modal after a short delay
                    setTimeout(function() {
                        $('#add-subject-modal').modal('hide');
                        $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                    }, 100);
                },
                error: function() {
                    // Display an error message
                    $('#message').html('Error occurred while adding user.').show();
                }
            });
        });
    });


    $(document).ready(function() {
    $('#add-user-modal').on('submit', 'form', function(e) {
        e.preventDefault(); // Prevent form submission

        // Make an AJAX request to the add_user.php file
        $.ajax({
            type: 'POST',
            url: 'add_user.php',
            data: $(this).serialize(),
            success: function(response) {
                // Display the success message
                $('#message').html(response).show();
                updateUserList();
                // Hide the modal after a short delay
                setTimeout(function() {
                    $('#add-user-modal').modal('hide');
                    $('body').removeClass('modal-open');
                    $('.modal-backdrop').remove();
                }, 100);
            },
            error: function() {
                // Display an error message
                $('#message').html('Error occurred while adding user.').show();
            }
        });
    });
});



   
    var userId;

    function deleteUser(userId) {
    // Confirm the deletion with the user
    if (confirm('Are you sure you want to delete this user?')) {
        // AJAX request to delete the user
        $.ajax({
            type: 'POST',
            url: 'delete_user.php',
            data: { action: 'delete', userId: userId },
            success: function(response) {
                // Display the success message or perform any other actions
                console.log(response);
                
                // Update the user list dynamically
                updateUserList();
            },
            error: function(xhr, status, error) {
                // Handle the error
                console.error(xhr.responseText);
            }
        });
    }
}

function updateUserList() {
    // AJAX request to retrieve the updated user list
    $.ajax({
        type: 'GET',
        url: 'get_user_list.php', // Replace with the appropriate URL to fetch the user list
        success: function(response) {
            // Update the user list container with the updated user list
            $('#user-list-container').html(response);
        },
        error: function(xhr, status, error) {
            // Handle the error
            console.error(xhr.responseText);
        }
    });
}






    $(document).ready(function() {
        // Event handler for opening the edit user modal
        $('#edit-user-modal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var userName = button.data('user-name');

            // AJAX request to retrieve existing subjects and populate the container
            $.ajax({
                type: 'POST',
                url: 'get_existing_subjects.php',
                data: { userName: userName },
                success: function(response) {
                    // Update the subjects checkboxes in the modal
                    $('#existing-subjects-container').html(response);
                },
                error: function(xhr, status, error) {
                    // Handle the error
                    console.error(xhr.responseText);
                }
            });
        });
    });

    $(document).ready(function() {
        $('#edit-user-modal').on('hidden.bs.modal', function() {
            $('.modal-backdrop').remove(); // Remove the modal backdrop
        });
    });

    

    $(document).ready(function() {
    $('#logout-button').on('click', function(e) {
       
        e.preventDefault();
        // Make the AJAX request and handle the logout functionality
        $.ajax({
            type: 'POST',
            url: 'logout.php',
            success: function(response) {

               
                // Check the response
                if (response === 'success') {
                    // Redirect the user to index.php or perform any other actions
                    window.location.href = 'index.php';
                }
                
            },
            error: function() {
                // Handle error if the AJAX request fails
                console.log('Error occurred during logout');
            }
        });
    });
});  



   


</script>

</body>
</html>
