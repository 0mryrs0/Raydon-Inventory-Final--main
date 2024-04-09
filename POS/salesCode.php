<?php
    include '../config/database.php';
    include 'functions_cashier.php';
if(isset($_POST['searchInput'])) {

    $searchInput = $_POST['searchInput'];
    $query = "SELECT * FROM products 
    WHERE (product LIKE '%{$searchInput}%' OR category LIKE '%{$searchInput}%') 
    AND stocks >= 1 
    AND product_status != 'Phaseout'";
    $result = mysqli_query($conn, $query);
         if($result) {
             if(mysqli_num_rows($result) > 0) 
             {   

             ?>
             <table class="table search-table table-sm fs-6 table-warning table-bordered text-center table-hover table-stripe">
             <thead class="table-dark">
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Stocks</th>
            </thead>
             
            <tbody>
             
             <?php
                 while($fetch = mysqli_fetch_assoc($result)) {

                    $productId = $fetch['product_id'];
                    $productName = $fetch['product'];
                    $price = $fetch['price'];
                    $stocks= $fetch['stocks'];

                 
             ?>
                <tr class="item-choice">
                    <td><input type="hidden" value="<?php echo $productId ?>" id="product-choice-id"><?php echo $productId ?></td>
                    <td><input type="hidden" value="<?php echo $productName ?>" id="product-choice-name"><?php echo $productName ?></td>
                    <td><input type="hidden" value="<?php echo $price ?>" id="product-choice-price"><?php echo $price ?></td>
                    <td><?php echo $stocks?></td>
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

 $count = 0;
 $totalAmount = 0;
if(isset($_POST['add_item'])) {

    $productChoiceId = $_POST['productChoiceId'];
    $productChoice = $_POST['productChoice'];
    $productChoiceQuantity = $_POST['productChoiceQuantity'];
    $productChoicePrice = $_POST['productChoicePrice'];
    

    if (!empty($productChoice) && !empty($productChoiceQuantity) && !empty($productChoicePrice)) {

        if ($productChoiceQuantity > 0) {
            $subTotalPerItem = $productChoicePrice * $productChoiceQuantity;

            $itemDetails = array($productChoice, $productChoiceQuantity, $productChoicePrice, $subTotalPerItem, $productChoiceId);
            
            
            $response = [
                'status' => 200,
                'message' => 'Item Details',
                'data' => [
                    'itemDetails' => $itemDetails,
                    'totalAmount' => $totalAmount
                ]
            ];
    
            echo json_encode($response);
        } else {

            $response = [
                'status' => 500,
                'message' => 'Quantity should be more than 1'
            ];
            echo json_encode($response);

        }
    } else {
        $response = [
            'status' => 422,
            'message' => 'Please complete all details'
        ];

        echo json_encode($response);
    }
}

$amountPaid = 0;
$totalAmount =0;

//Calculating the change
if(isset($_GET['calculate_change'])){
    $amountPaid = $_GET['amountPaid'];
    $totalAmount = $_GET['totalAmount'];

    if(!empty($amountPaid) && !empty($totalAmount)){
        $change = number_format(($amountPaid - $totalAmount), 2, '.', '');
        $response = [
            'status' => 200,
            'message' => 'Change',
            'data' => $change
        ];

        echo json_encode($response);

    }else {
        $response = [
            'status' => 404,
            'message' => 'Please input amount',
        ];
        echo json_encode($response);
    }

}

//Adding sales order
if(isset($_GET['add_customerOrders'])) {
    $customerOrders = $_GET['customerOrders'];
    $amountPaid = $_GET['amountPaid'];
    $totalAmount = $_GET['totalAmount'];
    $salesOrderNumber = $_GET['salesOrderNumber'];
    $cashier_id = $_GET['cashier_id'];
   

    if((!empty($amountPaid) && !empty($totalAmount)) && !empty($salesOrderNumber) && !empty($cashier_id)) 
    {

        if ($amountPaid > $totalAmount || $amountPaid == $totalAmount) 
        {
            //Inserting customer's order in database    
            foreach ($customerOrders as $item) 
            {
                $itemNo = $item['itemNo'];
                $itemName = $item['itemName'];
                $quantity = $item['quantity'];
                $unitPrice = $item['unitPrice'];
                $subtotal = $item['subtotal'];
                $productId = $item['productId'];

                // Insert data into the database 
                $query = "INSERT INTO sales (`item_no`, `item_name`, `item_quantity`, `item_price`,`item_subtotal`,`product_id`, `sales_orderNumber`) 
                VALUES ('$itemNo', '$itemName', '$quantity', '$unitPrice', '$subtotal', '$productId','$salesOrderNumber')";

                $result = mysqli_query($conn, $query);
                if (!$result) 
                {
                    echo "Error inserting data: " . $mysqli->error;
                }

                // Updating the product stocks
                $query = "UPDATE products SET stocks = stocks - $quantity WHERE product_id = $productId";
                $result = mysqli_query($conn, $query);
                
                if (!$result) 
                {
                    echo "Error inserting data: " . $mysqli->error;
                }

            }

            //Inserting the transaction in sales orders table8uy
            $query = "INSERT INTO sales_orders (`sales_orderNumber`, `cashier_id`, `total_amount`) VALUES ('$salesOrderNumber', '$cashier_id', '$totalAmount')";
            $result = mysqli_query($conn, $query);

            if (!$result) 
            {
                    echo "Error inserting data: " . $mysqli->error;
            }
            
            $response = [
                'status' => 200,
                'message' => 'Transaction successfully completed'
            ];

            echo json_encode($response);
            
        } 
        else 
        {
            $response = [
                'status' => 422,
                'message' => 'Cannot continue the transaction since your payment is not enough',
            ];
            echo json_encode($response);
        }

    } 
    else 
    {
        $response = [
            'status' => 422,
            'message' => 'Please input the amount',
        ];
        echo json_encode($response);
    }
   
}
?>