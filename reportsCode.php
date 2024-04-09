<?php
include('./config/database.php');
include('./config/functions.php');

if (isset($_POST['reportFromTo'])) {
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];

    // Validate and sanitize dates (you should implement proper validation)
    $startDate = mysqli_real_escape_string($conn, $startDate);
    $endDate = mysqli_real_escape_string($conn, $endDate);

    // Construct the SQL query to fetch data based on date range
    $query = "SELECT * FROM sales WHERE item_dateAdded >= '$startDate' AND item_dateAdded <= '$endDate 23:59:59'";
    
    $result = mysqli_query($conn, $query);

    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            $data = array();

            // Loop through the result set and build an array of data
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }

            // Send the data as JSON
            $response = [
                'status' => 200,
                'message' => 'Data found',
                'data' => $data
            ];

            echo json_encode($response);

        } else {
            // No data found
            $response = [
                'status' => 404,
                'message' => 'No sales data in this range'
            ];

            echo json_encode($response);
        }
    } else {
        // Handle query errors
        $response = [
            'status' => 500,
            'message' => 'Error: ' . mysqli_error($conn)
        ];

        echo json_encode($response);
    }
}
?>