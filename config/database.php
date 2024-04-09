<?php 

//Defining constant to the database to access it.
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "inventory");

//Creating connection to the database
$conn = mysqli_connect(DB_HOST, DB_USER,DB_PASS,DB_NAME);

//Check any error when connecting to the database
if($conn->connect_error) {
    die("Connection Failed" . $conn->connect_error);
}

?>