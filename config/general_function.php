<?php 



function createPurchase($productId, $conn) {

    // Check if there is an ongoing purchase of a product
    $query = "SELECT product_id FROM purchases";
    $result = mysqli_query($conn, $query);

    $productIdOfOngoingPurchases = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $productIdOfOngoingPurchases[] = $row['product_id'];
    }

    if (!in_array($productId, $productIdOfOngoingPurchases)) {
        $purchaseOrderNumber = "";

        $selectQuery = "SELECT COUNT(*) as num_rows FROM purchases";
        $result = mysqli_query($conn, $selectQuery);
    
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $num_rows = $row['num_rows'] + 1;
            // Use $num_rows as needed
            $purchaseOrderNumber = "O" . strval($num_rows);
        } else {
            $message = "Error: " . mysqli_error($conn);
        } 
    
        $query = "SELECT * FROM products WHERE product_id=$productId";
    
        $result = mysqli_query($conn, $query);
        $fetch = mysqli_fetch_assoc($result);
      
        $name = $fetch['product'];
        $supplier = $fetch['supplier'];
           
        if ($fetch['stocks'] < 20) {
            $query = "INSERT INTO purchases (`order_number`, `product`, `quantity`, `supplier_name`, `delivery_status`, `product_id`, `purchase_OrderNumber`) VALUES ('$purchaseOrderNumber','$name', 100, '$supplier', 'NOT COMPLETE','$productId', 'No purchase yet')";
            $result = mysqli_query($conn, $query);
            // Check if the insertion was successful
            if (!$result) {
                // Handle the case when the query fails
                $message = "Error: " . mysqli_error($conn);
            }else {
                $notificationMessage = "The product {$name} is in low stock.";
                $query = "INSERT INTO notifications (`notification`, `isClicked`, `order_number`) VALUES ('$notificationMessage', 0, '$purchaseOrderNumber')";
                $result = mysqli_query($conn, $query);
                
            }
        }
    }
    } 

function checkCashier($cashierId, $conn) {
    $query = "SELECT first_name, last_name FROM `cashiers` WHERE cashier_id= $cashierId";

    $result = mysqli_query($conn, $query);

    if($result) {
        $fetch = mysqli_fetch_assoc($result);
        return "Cashier " . $fetch['first_name'] . " " . $fetch['last_name'];
    } 
    else {
        return "Error: " . mysqli_error($conn);
    }
   
}

