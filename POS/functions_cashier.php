<?php

function check_login($conn){

    // Checking if session is set
    if(isset($_SESSION['cashier_id'])) {
        $user_id = $_SESSION['cashier_id'];

        //Creating a query fpr checking if the current user id match any user_id in the database
        $query = "SELECT * FROM cashiers WHERE cashier_id= '$user_id' LIMIT 1";
        $result= mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0){
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }
    //redirect to login page
    header("Location: cashier.php");
    die;
}


