<?php
include('./config/database.php');
include('./config/functions.php');

$relation = "Purchases";
// <----------------PHP FOR PURCHASES------------------------------------//

// <________ADDING PURCHASES FORM HANDLER_______________>
if (isset($_POST['new_purchase'])) {
    $productName = htmlspecialchars($_POST['product-name']);
    $quantity = htmlspecialchars($_POST['quantity']);

    // Check if product name and quantity are not empty
    if (empty($productName) && empty($quantity) || ($quantity <= 0)) {
        $message = "Product name and quantity cannot be empty, and quantity should be greater than zero.";
        $response = ['status' => 422, 'message' => $message];
        echo json_encode($response);
        return;
    }

    // Check if there is an ongoing purchase of a product
    $queryCheckPurchase = "SELECT product_id FROM purchases";
    $resultCheckPurchase = mysqli_query($conn, $queryCheckPurchase);

    $productIdOfOngoingPurchases = [];
    while ($row = mysqli_fetch_assoc($resultCheckPurchase)) {
        $productIdOfOngoingPurchases[] = $row['product_id'];
    }

    if (!in_array($productName, $productIdOfOngoingPurchases)) {
        // Generate a purchase order number
        $selectQuery = "SELECT COUNT(*) as num_rows FROM purchases";
        $resultSelectQuery = mysqli_query($conn, $selectQuery);

        if ($resultSelectQuery) {
            $row = mysqli_fetch_assoc($resultSelectQuery);
            $num_rows = $row['num_rows'] + 1;
            $purchaseOrderNumber = "O" . strval($num_rows);
        } else {
            $message = "Error: " . mysqli_error($conn);
            $response = ['status' => 500, 'message' => $message];
            echo json_encode($response);
            return;
        }

        // Fetch product details from the products table
        $queryFetchProduct = "SELECT * FROM products WHERE `product` = '$productName'";
        $resultFetchProduct = mysqli_query($conn, $queryFetchProduct);

        if ($fetch = mysqli_fetch_assoc($resultFetchProduct)) {
            $productId = $fetch['product_id'];
            $supplier = $fetch['supplier'];

            // Insert the purchase details into the purchases table
            $queryInsertPurchase = "INSERT INTO purchases (`order_number`, `product`, `quantity`, `supplier_name`, `delivery_status`, `product_id`, `purchase_OrderNumber`)
                                   VALUES ('$purchaseOrderNumber', '$productName', '$quantity', '$supplier', 'NOT COMPLETE', '$productId', 'No purchase yet')";
            $resultInsertPurchase = mysqli_query($conn, $queryInsertPurchase);

            $uniqueId = "LO" . uniqid();
            $activity = "Admin has set the product " . $productName . " for purchase";
            $insertLogsQuery = "INSERT INTO logs (`log_id`, `activity`, `relation`) VALUES ('$uniqueId', '$activity', '$relation')";
            mysqli_query($conn, $insertLogsQuery);

            // Check if the insertion was successful
            if (!$resultInsertPurchase) {
                $message = "Error: " . mysqli_error($conn);
                $response = ['status' => 500, 'message' => $message];

            } else {
                $message = "Purchase Added";
                $response = ['status' => 200, 'message' => $message];
            }
        } else {
            $message = "Error fetching product details: " . mysqli_error($conn);
            $response = ['status' => 500, 'message' => $message, 'data' => $fetch];
        
        }
    } else {
        $message = "Purchase for this product has been recorded. Please see purchase table.";
        $response = ['status' => 422, 'message' => $message];
    }

    // Send JSON response
    echo json_encode($response);
}


// <________UPDATING PURCHASE FORM HANDLER_______________>

if (isset($_GET['editPurchase'])) {
    $purchase_id = $_GET['purchase_id'];

    $query = "SELECT * FROM purchases WHERE purchase_id='$purchase_id'";
    $result = mysqli_query($conn, $query);


    if (mysqli_num_rows($result) == 1) {
        $purchase = mysqli_fetch_array($result);
        $response = [
            'status' => 200,
            'message' => 'Purchase Order Details',
            'data' => $purchase
        ];

        echo json_encode($response);
    } else {
        $response = [
            'status' => 404,
            'message' => 'Purchase ID did not found'
        ];

        echo json_encode($response);
    }
}

if(isset($_POST['update_purchase'])){
    $purchase_id = $_POST['purchase_id'];
    $orderNumber = $_POST['order-number'];
    $productName = $_POST['product-name'];
    $category = $_POST['category'];
    $quantity = $_POST['quantity'];

    //Checking if all inputs are not empty
    if(!empty($orderNumber) && !empty($productName) && !empty($category) && !empty($quantity)) 
    {

        //Inserting to database
        $query = "UPDATE purchases SET order_number ='$orderNumber', product='$productName', quantity='$quantity' WHERE purchase_id= '$purchase_id'";
        $result = mysqli_query($conn, $query);

        $uniqueId = "LO" . uniqid();
        $activity = "Admin has updated the order for purchase with the purchase id -" . $purchase_id;
        $insertLogsQuery = "INSERT INTO logs (`log_id`, `activity`, `relation`) VALUES ('$uniqueId', '$activity', '$relation')";
        mysqli_query($conn, $insertLogsQuery);

        if ($result) 
        {
            $response = [
                'status' => 200, 
                'message'=> 'Purchases Updated Successfully'

            ];

            echo json_encode($response);
            return;
        }
        else 
        {
            $response = [
                'status' => 500, //Error
                'message'=> 'Update purchase order is not successful'
            ];

            echo json_encode($response);
            return;
        }

    }
    else
    {
        $response = [
            'status' => 422, //Error
            'message'=> 'All purchase order details should be completed'
        ];

        echo json_encode($response);
        return;
    }
}




if(isset($_POST['searchInput'])) {

   $searchInput = $_POST['searchInput'];

   $query = "SELECT * FROM purchases WHERE  purchase_id='$searchInput' OR order_number LIKE '{$searchInput}%' OR  product LIKE '{$searchInput}%'  OR  quantity LIKE '{$searchInput}%' ";
   $result = mysqli_query($conn, $query);
        if($result) {
            if(mysqli_num_rows($result) > 0) {   

            ?>
            <table class="table search-table table-warning text-center">
            <thead class="">
                <tr>
                <th>Code</th>
                <th>Order Number</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Supplier</th>
                <th>Order Date</th>
                <th>Action</th>
                </tr> 
            </thead>    
            <tbody>

            <?php
                while($fetch = mysqli_fetch_assoc($result)) {
                    $purchaseId = $fetch['purchase_id'];
                    $orderNumber = $fetch['order_number'];
                    $productName = $fetch['product'];
                    $quantity = $fetch['quantity'];
                    $supplier = $fetch['supplier_name'];
                    $date_added = $fetch['date_added'];

            ?>
                <tr>
                    <td><?php echo $purchaseId ?></td>
                    <td><?php echo $orderNumber ?></td>
                    <td><?php echo $productName?></td>
                    <td><?php echo $quantity ?></td>
                    <td><?php echo $supplier?></td>
                    <td><?php echo $date_added ?></td>

                    <td class='action'>
                        <button type='button' value='<?php echo $purchaseId?>' class='editPurchBtn action-btn opacity-btn'  data-bs-toggle='modal' data-bs-target='#editPurchModal' tabindex='-1' >
                        Add to PO
                        </button>

                    </td>
                </tr>

                <?php } ?>
            </tbody>

            </table>
            <?php
            }else {
                echo "<h3 class='text-danger text-center search-table'> Data not found</h3>";
            }
}
}

if (isset($_POST['create_purchaseOrder'])) {
    $purchaseOrders = json_decode($_POST['orders'], true);

    // Loop through each supplier and their respective orders
    foreach ($purchaseOrders as $supplierName => $orders) {
        // Generate a custom purchase order number (you can adjust this logic as needed)
        $purchaseOrderNumber = "PO_" . uniqid();

        // Calculate the total quantity for all products from the same supplier
        $totalQuantity = 0;

        // Loop through each order and update the purchase_OrderNumber in purchases table
        foreach ($orders as $order) {
            $quantity = $order['quantity'];
            $totalQuantity += $quantity;

            // Update the purchase_OrderNumber column in the purchases table
            $purchaseId = $order['purchaseId'];
            $updateSql = "UPDATE purchases SET purchase_OrderNumber = '$purchaseOrderNumber' WHERE purchase_id = '$purchaseId'";

            if ($conn->query($updateSql) !== TRUE) {
                echo "Error updating record: " . $conn->error;
            }
        }

        // Insert into the purchase_orders table
        $insertSql = "INSERT INTO purchase_orders (purchase_orderNumber, supplier, total_productOrder, order_status)
                VALUES ('$purchaseOrderNumber', '$supplierName', '$totalQuantity', 'PENDING')";

        $uniqueId = "LO" . uniqid();
        $activity = "Admin created a purchase order with the purchase order number -" . $purchaseOrderNumber;
        $insertLogsQuery = "INSERT INTO logs (`log_id`, `activity`, `relation`) VALUES ('$uniqueId', '$activity', '$relation')";
        mysqli_query($conn, $insertLogsQuery);

        if ($conn->query($insertSql) !== TRUE) {
            echo "Error inserting data: " . $conn->error;
        }
    }

    // Close the database connection
    $conn->close();

    $response = [
        'status' => 200,
        'message' => 'Transaction successfully completed'
    ];

    echo json_encode($response);
}


if (isset($_POST['getPurchaseDetails'])) {
    $purchaseOrderNumber = $_POST['purchaseOrderNumber'];

    $query = "SELECT * FROM purchases WHERE purchase_orderNumber='$purchaseOrderNumber'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $purchaseDetails = array();

        while ($row = mysqli_fetch_assoc($result)) {
            $purchaseDetails[] = $row;
        }

        $response = [
            'status' => 200,
            'message' => 'Purchase Order Details',
            'data' => $purchaseDetails
        ];

        echo json_encode($response);
    } else {
        $response = [
            'status' => 404,
            'message' => 'Error retrieving purchase details'
        ];

        echo json_encode($response);
    }
}

if(isset($_POST['deliveredProduct'])) {
    $purchaseOrderNumber = $_POST['purchaseOrderNumber'];

    // Update order status in purchase_orders table
    $updateOrderStatusQuery = "UPDATE purchase_orders SET order_status='DELIVERED' WHERE purchase_orderNumber='$purchaseOrderNumber'";
    $resultOrderStatus = mysqli_query($conn, $updateOrderStatusQuery);



    // Update delivery status in purchases table
    $updateDeliveryStatusQuery = "UPDATE purchases SET delivery_status='COMPLETED' WHERE purchase_orderNumber='$purchaseOrderNumber'";
    $resultDeliveryStatus = mysqli_query($conn, $updateDeliveryStatusQuery);
    

    if ($resultOrderStatus && $resultDeliveryStatus) {
        // Update stock in product table for completed purchases
        // Select data from purchases table
        $selectQuery = "SELECT quantity, product_id, order_number FROM purchases WHERE delivery_status='COMPLETED'";
        $resultSelect = mysqli_query($conn, $selectQuery);



        if ($resultSelect) {
/*                             //Log insertion
                $uniqueId = "LO" . uniqid();
                $activity = "Admin confirmed the successfull delivery of the products with the purchase order number -" . $purchaseOrderNumber;
                $deliveredLogsQuery = "INSERT INTO logs (`log_id`, `activity`, `relation`) VALUES ('$uniqueId', '$activity', '$relation')";
                mysqli_query($conn, $deliveredLogsQuery);  */
            while ($row = mysqli_fetch_assoc($resultSelect)) {
                $quantity = $row['quantity'];
                $product_id = $row['product_id'];
                $orderNumber = $row['order_number'];

                // Update stock in product table
                $updateStockQuery = "UPDATE products SET stocks = stocks + '$quantity' WHERE product_id = '$product_id'";  
                $resultUpdateStock = mysqli_query($conn, $updateStockQuery);


                $updateNotifications = "UPDATE notifications SET isClicked = 1 WHERE order_number = '$orderNumber'";
                $resultUpdateNotification = mysqli_query($conn, $updateNotifications);



                if (!$resultUpdateStock && !$resultUpdateNotification) {
                    $response = [
                        'status' => 500,
                        'message' => 'Failed to update stock'
                    ];
                    // Send only JSON response
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit; // Exit to prevent further processing
                }
            }

            $response = [
                'status' => 200,
                'message' => 'Successfully confirmed the delivered products and updated stock'
            ];
        } else {
            $response = [
                'status' => 404,
                'message' => 'Purchases Not Yet Completed'
            ];
        }

        echo json_encode($response);
    }
}