<?php
include('./config/database.php');
include('./config/functions.php');


$relation = "Users";

// <----------------PHP FOR USERS------------------------------------//
// PHP form handler to register user
if (isset($_POST['register_user'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $selected_account = $_POST['account-type'];
    $confirmPassword = $_POST['confirmPassword'];
    $addedBy = $_POST['added-by-admin'];
    $activity = "";


    // Validating the input
    $firstname = htmlspecialchars($firstname);
    $lastname = htmlspecialchars($lastname);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $username = htmlspecialchars($username);

    // Validate password: Minimum length, at least one uppercase letter, one lowercase letter, and one digit
    $passwordRequirements = "/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/";

    // Checking if all inputs are not empty
    if (!empty($firstname) && !empty($lastname) && !empty($email) && !empty($username) && !empty($password) && !empty($confirmPassword) && !is_numeric($username)) {
        if ($password === $confirmPassword) {
            // Validate password against requirements
            if (preg_match($passwordRequirements, $password)) {
                // Password is valid, proceed with the registration
                $query = "INSERT INTO cashiers (`username`, `first_name`, `last_name`, `email`, `user_status`, `password`, `admin_id`) VALUES ('$username', '$firstname', '$lastname', '$email', 'Active', '$password', '$addedBy')";
                $result = mysqli_query($conn, $query);

                $uniqueId = "LO" . uniqid();
                $activity = "Admin created the cashier with username " . $username;
                $insertLogsQuery = "INSERT INTO logs (`log_id`, `activity`, `relation`) VALUES ('$uniqueId', '$activity', '$relation')";
                mysqli_query($conn, $insertLogsQuery);
                    
                if($result) {
                    $response = [
                        'status' => 200,
                        'message' => 'User created successfully'
                    ];

                    echo json_encode($response);
        
                    return;

                } else {
                    $response = [
                        'status' => 500, // Error
                        'message' => 'User did not added'
                    ];

                    echo json_encode($response);
                    return;
                }
            } else {
                $response = [
                    'status' => 422, // Error
                    'message' => 'Password must consist of 8 characters, at least 1 digit, at least 1 uppercase letter, and at least 1 lowercase letter'
                ];

                echo json_encode($response);
                return;
            }
        } else {
            $response = [
                'status' => 422, // Error
                'message' => 'Password does not match'
            ];

            echo json_encode($response);
            return;
        }
    } else {
        $response = [
            'status' => 422, // Error
            'message' => 'Please enter valid information'
        ];

        echo json_encode($response);
        return;
    }

}


//PHP form handler to delete cashier
if(isset($_POST['deactivate_cashier']))
{
    $cashier_id = $_POST['cashier_id'];

    $deleteQuery = "UPDATE `cashiers` SET `user_status`='Inactive' WHERE cashier_id = '$cashier_id'";
    $result= mysqli_query($conn, $deleteQuery);

    $uniqueId = "LO" . uniqid();
    $activity = "Admin deactivated the cashier with id " . $cashier_id;
    $deactivateLogsQuery = "INSERT INTO logs (`log_id`, `activity`, `relation`) VALUES ('$uniqueId', '$activity', '$relation')";
    mysqli_query($conn, $deactivateLogsQuery);

    if($result)
    {
        $response = [
            'status' => 200,
            'message' => 'Cashier Deactivated Successfully'
        ];
        echo json_encode($response);
        return;
    }
    else
    {
        $response = [
            'status' => 500,
            'message' => 'Cashier Not Deactivated'
        ];
        echo json_encode($response);
        return;
    }
}

if(isset($_POST['activate_cashier']))
{
    $cashier_id = $_POST['cashier_id'];

    $deleteQuery = "UPDATE `cashiers` SET `user_status`='Active' WHERE cashier_id = '$cashier_id'";
    $result= mysqli_query($conn, $deleteQuery);

    $uniqueId = "LO" . uniqid();
    $activity = "Admin activated the cashier with id " . $cashier_id;
    $activateLogsQuery = "INSERT INTO logs (`log_id`, `activity`, `relation`) VALUES ('$uniqueId', '$activity', '$relation')";
    mysqli_query($conn, $activateLogsQuery);

    if($result)
    {
        $response = [
            'status' => 200,
            'message' => 'Cashier Activated Successfully'
        ];
        echo json_encode($response);
        return;
    }
    else
    {
        $response = [
            'status' => 500,
            'message' => 'Cashier Not Activated'
        ];
        echo json_encode($response);
        return;
    }
}

// <----------------END PHP FOR USERS------------------------------------//


?>