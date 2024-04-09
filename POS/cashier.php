<?php 
session_start();
include '../config/database.php';
include 'functions_cashier.php';

$messageError = "";

    //Checking if the user click submit
    if($_SERVER['REQUEST_METHOD'] == "POST") {
        //
        $username = $_POST['username'];
        $password = $_POST['password'];
        $messageError = "";
         
        //Checking if all inputs are not empty
        if(!empty($username) && !empty($password) && !is_numeric($username)) {

            //reading from database
            $query = "SELECT * FROM cashiers WHERE username = '$username' limit 1";
            $result = mysqli_query($conn, $query);

            //Checking if the results are true
            if ($result) {
                //Checking if the the no. of rows in results are greater than zero which can indicate if the username are associated to an account
                if (mysqli_num_rows($result) > 0)
                    {
                        //Assigning query result to the variable
                        $user_data = mysqli_fetch_assoc($result);

                        if($user_data['user_status'] == 'Inactive') {
                            $messageError = "Your account is not activated. Please contact your admin to activate your account.";
                        }
                        else 
                        {
                            if ($password == $user_data['password']) {
                                var_dump($user_data['password'], $password); // To check if the data are correct
                                $_SESSION['cashier_id'] = $user_data['cashier_id'];
                                // Checking if the user is an admin or not
                                echo "You are logged in";
                                header("Location: sales.php");
                                die();
                            } else {
                                $messageError = "Wrong password or username";
                                var_dump($user_data['password'], $password); // Output for debugging
                            }

                        }
                    }
                    else {
                        $messageError =  "Your username is not associated to any account";
                    }
             } else {
                $messageError = "Error in the SQL query: " . mysqli_error($conn);
            }
        }
        else {
            $messageError = "Please enter some valid information";
        }
} 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="container">
        <div></div>
        <div class="left-container-img">
            <img src="../img/Hardware.svg">
        </div>
        <form method="post">
            <div class="form-header">
                <h2>CASHIER LOGIN</h2>
            </div>
            <div class="form-group">
                <input type="text" name="username" placeholder="Enter your username">
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Enter your password" autocomplete="on">
            </div>
            <p><?php echo $messageError?></p>
            <div class="form-group">
                <button id="submit-btn" name="submit" class="bg-warning">Sign in</button>
            </div>
        </form>
    </div>
</body>
</html>