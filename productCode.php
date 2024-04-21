<?php
include('./config/database.php');
include('./config/functions.php');

$relation = "Products";
// <----------------PHP FOR PRODUCT------------------------------------//

// <________ADDING PRODUCT FORM HANDLER_______________>
if(isset($_POST['add_product'])) {
    $productName = $_POST['product-name'];
    $category = $_POST['category'];
    $unit = $_POST['unit'];
    $price = $_POST['price'];
    $stocks = $_POST['stocks'];
    $supplier = $_POST['supplier'];
    $status= "";

    //Getting supplier id based on the selected business/supplier
    $query = "SELECT supplier_id, business_name FROM suppliers WHERE business_name = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $supplier);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            $supplier_id = $row['supplier_id'];
            // Further processing if needed
        } else {
            // Handle the case when no rows are returned
            $response = [
                'status' => 404, // Not Found
                'message'=> 'All inputs should be completed'
            ];
            echo json_encode($response);
            return;
        }
    } else {
        // Handle the case when the query fails
        $response = [
            'status' => 500, // Server Error
            'message'=> 'Error: ' . mysqli_error($conn)
        ];
        echo json_encode($response);
        return;
    }

    //Checking if all inputs are not empty
    if(!empty($productName) && !empty($category) && !empty($unit) && !empty($price) && !empty($stocks) && !empty($supplier)) 
    {
        $stock_status = check_stock_status($stocks);

        //Inserting to database
        $query = "INSERT INTO products (`product`, `category`, `unit`, `price`, `stocks`, `stock_status`, `supplier`, `product_status`, `supplier_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssssssssi', $productName, $category, $unit, $price, $stocks, $stock_status, $supplier, $status, $supplier_id);
        if (mysqli_stmt_execute($stmt)) {
            $response = [
                'status' => 200, 
                'message'=> 'Product Updated Successfully'
            ];
            echo json_encode($response);
            return;
        } else {
            $response = [
                'status' => 500, // Server Error
                'message'=> 'Failed to update product'
            ];
            echo json_encode($response);
            return;
        }
    } else {
        $response = [
            'status' => 422, // Unprocessable Entity
            'message'=> 'All product details should be completed'
        ];
        echo json_encode($response);
        return;
    }
}



// <________ END OF ADDING PRODUCT FORM HANDLER_______________>


// <________GETTING PRODUCT ID FORM HANDLER_______________>
if (isset($_GET['editProduct'])) 
{
    $product_id = $_GET['product_id'];

    $query = "SELECT * FROM products WHERE product_id='$product_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) 
    {
        $product = mysqli_fetch_array($result);
        $response = [
            'status' => 200,
            'message' => 'Product Details',
            'data' => $product
        ];
        echo json_encode($response);
    } 
    else 
    {
        $response = [
            'status' => 404,
            'message' => 'Product Id did not found'
        ];

        echo json_encode($response);
    }
}
// <________END OF GETTING PRODUCT ID FORM HANDLER_______________>


// <________UPDATING PRODUCT FORM HANDLER_______________>
if(isset($_POST['update_product']))
{
    $product_id = $_POST['product_id'];
    $productName = $_POST['product-name'];
    $category = $_POST['category'];
    $unit = $_POST['unit'];
    $price = $_POST['price'];
    $stocks = $_POST['stocks'];
    $supplier = $_POST['supplier'];
    $status= "";

    //Getting supplier id based on the selected business/supplier
    $query = "SELECT supplier_id, business_name FROM suppliers WHERE business_name = '$supplier'";
    $result = mysqli_query($conn, $query);



        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $supplier_id = $row['supplier_id'];
        } else {
            // Handle the case when the query fails
            echo "Error: " . mysqli_error($conn);
        }

    //Checking if all inputs are not empty
    if(!empty($productName) && !empty($category) && !empty($unit) && !empty($price) && !empty($stocks) && !empty($supplier)) 
    {
        $stock_status = check_stock_status(intval($stocks));

        //Inserting to database
        $query = "UPDATE products SET product='$productName',category='$category', unit='$unit', price='$price',stocks='$stocks', stock_status='$stock_status', supplier='$supplier', supplier_id='$supplier_id' WHERE product_id= '$product_id'";

        $uniqueId = "LO" . uniqid();
        $activity = "Admin updated the product " . $productName;
        $insertLogsQuery = "INSERT INTO logs (`log_id`, `activity`, `relation`) VALUES ('$uniqueId', '$activity', '$relation')";
        mysqli_query($conn, $insertLogsQuery);

        if (mysqli_query($conn, $query)) 
        {
            $response = [
                'status' => 200, 
                'message'=> 'Product Updated Successfully'
                
            ];
    
            echo json_encode($response);
            return;
        }
        else 
        {
            $response = [
                'status' => 500, //Error
                'message'=> 'Update product is not successful'
            ];
    
            echo json_encode($response);
            return;
        }

    }
    else
    {
        $response = [
            'status' => 422, //Error
            'message'=> 'All product details should be completed'
        ];

        echo json_encode($response);
        return;
    }
}
// <________END OF UPDATING PRODUCT FORM HANDLER_______________>


// <________DELETING  PRODUCT FORM HANDLER_______________>
if(isset($_POST['phaseout_product']))
{
    $product_id = $_POST['product_id'];

    $query = "UPDATE products SET product_status='Phaseout' WHERE product_id='$product_id'";
    $result= mysqli_query($conn, $query);

    $uniqueId = "LO" . uniqid();
    $activity = "Admin has moved the product with product id " . $product_id . " into phaseout";
    $phaseOutLogsQuery = "INSERT INTO logs (`log_id`, `activity`, `relation`) VALUES ('$uniqueId', '$activity', '$relation')";
    mysqli_query($conn, $phaseOutLogsQuery);

    if($result)
    {
        $response = [
            'status' => 200,
            'message' => 'Product has been moved to phaseout'
        ];
        echo json_encode($response);
        return;
    }
    else
    {
        $response = [
            'status' => 500,
            'message' => 'Product not moved to phaseout'
        ];
        echo json_encode($response);
        return;
    }
}
// <________END OF DELETING  PRODUCT FORM HANDLER_______________>


// <________SEARCH  PRODUCT FORM HANDLER_______________>
if(isset($_POST['searchInput'])) 
{
   $searchInput = $_POST['searchInput'];

   $query = "SELECT * FROM products WHERE  (product_id='$searchInput' OR product LIKE '%{$searchInput}%' OR  category LIKE '%{$searchInput}%' OR supplier LIKE '%{$searchInput}%' OR  stock_status LIKE '%{$searchInput}%') AND product_status != 'Phaseout' ORDER BY product ASC";
   $result = mysqli_query($conn, $query);
        if($result) 
        {
            if(mysqli_num_rows($result) > 0) 
            {   
            ?>
            <table class="table table-warning table-hover text-center search-table table-sm table-bordered align-middle px-3" id='table-content'>
                <thead class="table-dark">
                <tr>
                    <th>Code</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Unit</th></th>
                    <th>Price</th> 
                        <th>stocks</th>
                        <th>Status</th>
                    <th>Supplier</th>
                    <th class="action-column">Action</th>
                </tr> 
                </thead>                   
                <tbody>
                
                <?php 
                    while($fetch = mysqli_fetch_assoc($result)) {
                        $productId = $fetch['product_id'];
                        $productName = $fetch['product'];
                        $category = $fetch['category'];
                        $unit = $fetch['unit'];
                        $price = $fetch['price'];
                        $stocks = $fetch['stocks'];
                        $supplier = $fetch['supplier'];

                    ?>

               <tr >
                  <td><?php echo $fetch['product_id']?></td>
                  <td><?php echo $fetch['product']?></td>
                  <td><?php echo $fetch['category']?></td>
                  <td><?php echo $fetch['unit']?></td>
                  <td>&#x20B1; <?php echo $fetch['price']?></td>
                  <td><?php echo $fetch['stocks']?></td>
                  <?php $stock_status = check_stock_status($fetch['stocks']);
                     $updateStocks= "UPDATE products SET stock_status='$stock_status' WHERE product_id={$fetch['product_id']}";
                     mysqli_query($conn, $updateStocks);
                  ?>
                  <td><?php echo $stock_status?></td>
                  <td><?php echo $fetch['supplier']?></td>

                  <td class='action action-column'>
                     <button type='button' value='<?php echo $fetch['product_id']?>' class='editProductBtn action-btn opacity-btn'  data-bs-toggle='modal' data-bs-target='#editProductModal' tabindex='-1' >
                        <i class='fa-regular fa-pen-to-square p-2 bgYellow text-white' data-bs-toggle="tooltip" title="Edit product"></i>
                     </button>
                     <button type='button' value='<?php echo $fetch['product_id']?>' class='deleteProductBtn delete-btn action-btn opacity-btn'>
                        <i class='bi bi-x-circle-fill p-2  bgMaroon text-white' data-bs-toggle="tooltip" title="Move product to phaseout"></i>
                     </button>
                  </td>
               </tr>

               <?php } ?>
                </tbody>

            </table>
            <?php
            }
            else 
            {
                echo "<h3 class='text-danger text-center search-table'> Data not found</h3>";
            }
        
        }
}


