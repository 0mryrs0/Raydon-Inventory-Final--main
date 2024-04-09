<?php

function check_login($conn){

    // Checking if session is set
    if(isset($_SESSION['admin_id'])) {
        $user_id = $_SESSION['admin_id'];

        //Creating a query for checking if the current user id match any user_id in the database
        $query = "SELECT * FROM admins WHERE admin_id= '$user_id' LIMIT 1";
        $result= mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0){
            $user_data = mysqli_fetch_assoc($result);
            return $user_data;
        }
    }
    //redirect to login page
    header("Location: admin.php");
    die;
}

function check_stock_status($quantity) {
    //For status; based on the available quantity
    $status = "";
    if ($quantity >= 20) {
        $status = "GOOD";
    } else if ($quantity >= 1 && $quantity <= 19) {
        $status = "LOW STOCK";
    } else if ($quantity == 0){
        $status = "NOT AVAILABLE";
    }

    return $status;
}

