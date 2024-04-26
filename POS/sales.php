<?php 

session_start();
    $pageName = "Point of Sale";
    include '../config/database.php';
    include 'functions_cashier.php';
    $user_data = check_login($conn);
    if($user_data['user_status'] == 'Inactive') {
        header("Location: cashier.php");
    }

    $discount = [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80];
    $count

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Point of Sale</title>
    <link rel="stylesheet" href="pos.css"> 
    <link rel="stylesheet" href="../css/receipt.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css" integrity="sha384-b6lVK+yci+bfDmaY1u0zE8YYJt0TZxLEAFyYSLHId4xoVvsrQu3INevFKo+Xir8e" crossorigin="anonymous">
</head>
<body class="overflow-y-hidden">
<div id="page-content-wrapper ">
      <nav class="navbar navbar-light bg-success px-4">
          <div class="d-flex align-items-center py-2">
              <div>
                <h3 class="m-0 text-white fw-bold"><?php echo $pageName;?></h3>
                <h6 class="fs-6 fw-light" id="date"></h6>
              </div>
          </div>
          <div class="position-absolute end-0 me-5 zindex-dropdown-1">
              <ul class="navbar-nav ms-auto mb-2 mb-lg-0 rounded-2 bg-white px-3">
                  <li class="nav-item dropdown position-relative end-0">
                      <a class="nav-link dropdown-toggle second-text fw-bold text-success" href="#" id="navbarDropdown"
                          role="button" data-bs-toggle="dropdown">
                          <i class="fas fa-user me-2"></i><?php  echo $user_data['first_name']?>
                      </a>
                      <ul class="dropdown-menu position-absolute end-50">
                          <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
                      </ul>
                  </li>
              </ul>
          </div>
      </nav>
      <div class= "container-fluid p-4 d-flex-column row mt-2" id="main-content">
        <div class="col my-2">
            <div class="row sales-search bg-dark text-white p-4 rounded">
                <form id="product-choice-sales">
                    <input class="form-control form-control border-outline-dark" type="search" placeholder="Search" id="search-input" name="search-input" autocomplete="off">
                    <div class="row table-container my-2 " id="table-container">
                        <!-- .search-table here -->

                    </div>
                    <input type="hidden" placeholder="" id="productChoiceId" name="productChoiceId" autocomplete="off">
                    <div class="form-group mb-1 row">
                        <label for="sales-product" class="form-label col">Product:</label>
                        <input class="form-control form-control form-control-sm border-outline-dark col" readonly=true type="text" placeholder="" id="productChoice" name="productChoice" autocomplete="off">
                    </div>
                    <div class="form-group-sm mb-1 row">
                        <label for="sales-quantity" class="form-label col">Quantity:</label>
                        <input type="number" class="form-control form-control-sm col" id="productChoiceQuantity" name="productChoiceQuantity">
                    </div>
                    <div class="form-group mb-1 row">
                        <label for="sales-quantity" class="form-label col mb-2">Price:</label>
                        <input type="number" class="form-control form-control-sm col" readonly=true id="productChoicePrice" name="productChoicePrice">
                    </div>
                    <div class="alert alert-danger d-none mt-2" id="errorMessage">
                        
                    </div>
                    <div class="row">
                        <button type="submit" class="add-item btn btn-outline-light mx-auto py-3 mt-2 fw-bold">Add item</button>
                    </div> 
                </form> 
            </div>
        </div>
        <div class="col col-md-7 ms-4" id="receipt">
            <div>
                <?php
                    // Get the current date in the format of MonthDateYear
                    $currentDate = date('mdY');

                    // Query to count the number of customer transactions for the current day
                    $query = "SELECT COUNT(*) as num_rows FROM sales_orders WHERE DATE(date_ofTransaction) = CURDATE()";
                    $result = mysqli_query($conn, $query);

                    if ($result) {
                        $row = mysqli_fetch_assoc($result);
                        $num_rows = $row['num_rows'] + 1;

                        // Use $num_rows as needed
                        $salesOrderNumber = "SO" . $currentDate . $num_rows;
                        
                    } else {
                        // Handle the case where the query fails
                        echo "Error: " . mysqli_error($conn);
                    }
                ?>
                <h6 class="text-center">Sales Order Number:<?php echo $salesOrderNumber?><input type="hidden" value="<?php echo $salesOrderNumber?>" name="sales-order-number" id="sales-order-number"></h6>
                <input type="hidden" value="<?php echo $user_data['cashier_id'];?>" id="cashier_id" name="cashier_id"> 
            </div>
            <div id="sales-table" class="row">
                <div class="mt-1 overflow-y-scroll w-100 border" id="table" style="height: 250px">
                    <table class="table table-warning table-hover text-center default-table fs-6 table-bordered align-middle " id='table-content'>
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">Item No.</th>
                                <th scope="col">Item</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Unit Price</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="t-body">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row payment-total bg-dark text-white p-2">
                <div class="container ">
                    <div class="alert alert-warning d-none" id="errMessage"></div>
                    <div class="form-group mb-1 row">
                        <label for="total-amount" class="form-label col">Total Amount:</label>
                        &#8369;<input class="form-control form-control-sm border-outline-dark col ms-2" type="number" readonly=true id="total-amount" name="total-amount" autocomplete="off">
                    </div>
                    <div class="form-group mb-1 row">
                        <label for="amount-paid" class="form-label col">Amount Paid:</label>
                        &#8369;<input class="form-control form-control-sm border-outline-dark col ms-2" type="number"  id="amount-paid" name="amount-paid" autocomplete="off">
                    </div>
                    <div class="form-group mb-1 row d-none">
                        <label for="subtotal" class="form-label col">Change:</label>
                        &#8369;<input class="form-control form-control-sm border-outline-dark col ms-2" type="number" readonly=true id="change" name="change" autocomplete="off">
                    </div>
                </div>
            </div>
            
            <div class="row mt-4 btn-div">
                <button class="btn btn-success py-3 fw-bold " id="confirm-btn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">Confirm</button>
                <button class="btn btn-outline-dark py-3 mt-2 d-none" data-bs-toggle="modal" data-bs-target="#printReceiptModal" id="printReceipt">
                    VIEW RECEIPT
                </button>
                <br>
                <div class="">
                    <h1 class="fw-bold d-none mt-3 text-center  text-danger py-3" id="customer-change">CHANGE: </h1>
                </div>


                <div class="modal" id="printReceiptModal" tabindex="-1" aria-labelledby="purchaseOrderLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                           <h5 class="modal-title" id="">Receipt</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                           <!-- Purchase Order Container -->
                           <div class="container" id="purchaseOrder">
                                 <div class="purchase-header text-left">
                                    <div class="w-100">
                                       <img src="../img/companyLogo2.svg" class="w-25">
                                       <h2 class="fw-bold">Raydon Construction Trading</h2>
                                    </div>
                                    <h3 class="text-center">Receipt</h3>
                                    <hr><hr>
                                    <h4 id="supplier-name">Address: #9 Imelda Avenue, Brgy. San Isidro, Cainta Rizal, 1900</h4>
                                    <hr><hr>
                                 </div>
                                 <div class="sales-body">
                                    <h1 class="mt-4 fw-bold d-none">Sales Order #: <?php echo $salesOrderNumber?></h1>
                                    <table class="w-100 table">
                                            <thead>
                                                <tr>
                                                    <th>Qty</th>
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                    <th>Subtotal</th>
                                                    
                                                </tr>
                                            </thead>
                                            <tbody id="table-receipt-body">

                                            </tbody>
                                        </table>
                                    <h1 class="fw-bold" id="total">TOTAL: </h1>
                                    <h2 id="cash">CASH: </h2>
                                    <h1 class="fw-bold" id="receipt-change">CHANGE: </h1>
                                    <hr>
                                    <h2 >Cashier: <?php echo $user_data['first_name'] . " " . $user_data['last_name']?></h2>
                                    <h2 id="currentDate"></h2>
                                    </div>

                                 <hr>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="printSaleseOrder2()" class="btn btn-success">Print</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-btn">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!--             <div>
                <button class="btn btn-dark mt-4 w-100 p-3" id="end-btm">END TRANSACTION</button>
            </div> -->
            </div>
            

        </div>
      </div>

</div> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
var customerOrders = [];
var count = 0;
var subTotalAllItems = 0;
var amountPaid = 0;
var totalAmount = 0;
var salesOrderNumber = "";
var change = 0;
// Search 
$(document).ready(function() {
   $('#search-input').keyup(function() {
      var searchInput = $(this).val();
      //alert(searchInput);

      if(searchInput != "") {
         $.ajax({
            url:'salesCode.php',
            method:'POST',
            data: {
               'searchInput':searchInput
            },

            success: function(response) {
                $('.table-container').empty();
                $('.table-container').append(response);


                // Attach click event to each .item-choice
                $('.item-choice').click(function() {
                    var productId = $(this).find('#product-choice-id').val();
                    var productName = $(this).find('#product-choice-name').val();
                    var productPrice = $(this).find('#product-choice-price').val();
                    $('#productChoiceId').val(productId);
                    $('#productChoice').val(productName);
                    $('#productChoicePrice').val(productPrice);
                    /* alert(`${productId} ${productName} ${productPrice}`) */
                });

            }
         })
      }else {
        $('.search-table').remove();
      }
   });

});

//Computing the subtotal and adding it to the table
$(document).on('submit', '#product-choice-sales', function(e){
    e.preventDefault();
    var formData = new FormData(this);
    formData.append('add_item', true);
    
    $.ajax({
        type: 'POST',
        url: 'salesCode.php',
        data: formData,
        processData: false,
        contentType: false,

        success: function(response) {
            console.log(response);
            var res = jQuery.parseJSON(response);
            

            if(res.status == 422) {
            // Show error message inside the modal
            $('#errorMessage').removeClass('d-none');
            $('#errorMessage').text(res.message);
        
            }else if(res.status == 500) {
            // Show error message inside the modal
            $('#errorMessage').removeClass('d-none');
            $('#errorMessage').text(res.message);
        
            }else if(res.status == 200) {
                   $('#errorMessage').addClass('d-none');
                    // Convert the form data into an object
                    count++;
                    
                    var formDataObject = {
                    itemNo: count,
                    itemName: res.data.itemDetails[0],
                    quantity: res.data.itemDetails[1],
                    unitPrice: res.data.itemDetails[2],
                    subtotal: res.data.itemDetails[3],
                    productId: res.data.itemDetails[4]
                };

                customerOrders.push(formDataObject);
                $('#sales-table tbody').append(`
                    <tr class='sales-row'>
                        <td>${count}</td>
                        <td>${res.data.itemDetails[0]}</td>
                        <td>${res.data.itemDetails[1]}</td>
                        <td>${res.data.itemDetails[2]}</td>
                        <td>${res.data.itemDetails[3]}</td>
                        <td><button class="btn btn-danger btn-sm delete-item"><i class="fas fa-trash"></i></button></td>
                        
                    </tr>
                `)

                subTotalAllItems += res.data.itemDetails[3];
                $('#total-amount').val(subTotalAllItems)
                // Log the updated form submissions array
                

                // Clear input values
                $('#productChoiceId').val("");
                $('#productChoice').val("");
                $('#productChoiceQuantity').val("");
                $('#productChoicePrice').val("");
                $('#productChoiceDiscount').val("0");
                
            }
          
        }
    });
})

$(document).on('click', '#close-btn, #print-btn', function(e) {
    e.preventDefault();
    endTransaction()

});

$(document).on('contextmenu', '.sales-row', function(e) {
    e.preventDefault();

    var itemId = $(this).find('form.item-details input[name="item-id"]').val();

    // Ask for confirmation
    var isConfirmed = window.confirm('Are you sure you want to delete this row?');

    if (isConfirmed) {
        // Find the index of the item in the array based on the item ID
        var indexToRemove = customerOrders.findIndex(item => item.itemNo == itemId);

        // Remove the item from the array
        var removedItem = customerOrders.splice(indexToRemove, 1)[0];

        // Remove the corresponding table row
        $(this).remove();

        // Recalculate total amount after deletion
        subTotalAllItems -= removedItem.subtotal;
        $('#total-amount').val(subTotalAllItems);
    }
});

$(document).ready(function(){
    $('#amount-paid, #total-amount').on('input', function(){
        amountPaid = $('#amount-paid').val();  // Get amount paid
        totalAmount = $('#total-amount').val();
        

        $.ajax({
            type: 'GET',
            url: 'salesCode.php',
            data: { 
                calculate_change: true,
                amountPaid: amountPaid,
                totalAmount: totalAmount,
            },
            success: function (response) {
                var res = jQuery.parseJSON(response);
                $('#change').val(res.data)
                

            },
            error: function () {
                console.log('An error occurred during the AJAX request.');
            }
        })
    })
})

$(document).on('click', '#confirm-btn', function(e){
    e.preventDefault();
    amountPaid = $('#amount-paid').val();  // Get amount paid
    totalAmount = $('#total-amount').val();
    salesOrderNumber = $('#sales-order-number').val();
    var cashier_id = $('#cashier_id').val();

    $.ajax({
            type: 'GET',
            url: 'salesCode.php',
            data: { 
                add_customerOrders: true,
                customerOrders: customerOrders,
                amountPaid: amountPaid,
                totalAmount: totalAmount,
                salesOrderNumber: salesOrderNumber,
                cashier_id:cashier_id
            },
            success: function(response) {
            var res = jQuery.parseJSON(response);
            if (res.status == 422) {
                // Show error message inside the modal
                $('#errMessage').removeClass('d-none');
                $('#errMessage').text(res.message);
            } else if (res.status == 200) {
                $('#errMessage').addClass('d-none');
                /* window.location.reload() */
                $('#printReceipt').removeClass('d-none');
                change = $('#change').val()
                $('#customer-change').html(function(index, oldHtml) {
                        return oldHtml + '' + change;
                });
                $('#customer-change').removeClass('d-none');
                $(".add-item").attr("disabled", true);
                $("#confirm-btn").addClass('d-none');
                $("#amount-paid").attr("readonly", true);
                $("#quantity").attr("readonly", true);
                

            } 
        },
            error: function () {
                console.log('An error occurred during the AJAX request.');
            }
        })
})

$(document).on('click', '#printReceipt', function(e){
    var tableReceiptBody = $('#table-receipt-body')
    // Assuming customerOrders is an array
    customerOrders.forEach(function(item) {
        var itemName = item['itemName'];
        var itemQuantity = item['quantity'];
        var itemPrice = item['unitPrice'];
        var itemSubtotal = item['subtotal'];

        // Append a row to the table for each item
        tableReceiptBody.append('<tr><td>' + itemQuantity + '</td><td>' + itemName + '</td><td>' + itemPrice + '</td><td>' + itemSubtotal + '</td></tr>');
    });

    $('#total').html(function(index, oldHtml) {
            return oldHtml + '' + totalAmount;
    });
    $('#cash').html(function(index, oldHtml) {
            return oldHtml + '' + amountPaid;
    });

    change = $('#change').val()
    $('#receipt-change').html(function(index, oldHtml) {
            return oldHtml + '' + change;
    });


});

// New event handler for delete button
$(document).on('click', '.delete-item', function() {
    // Get the row index
    var rowIndex = $(this).closest('tr').index();

    // Remove the corresponding item from the customerOrders array
    var removedItem = customerOrders.splice(rowIndex, 1)[0];

    // Recalculate total amount after deletion
    subTotalAllItems -= removedItem.subtotal;
    $('#total-amount').val(subTotalAllItems);

    // Remove the row from the table
    $(this).closest('tr').remove();
    count--;
});




// Get the current date and time
var currentDateTime = new Date();
// Format the date and time as you need (e.g., "January 27, 2024 15:30:00")
var formattedDateTime = currentDateTime.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: 'numeric',
    minute: 'numeric',
    second: 'numeric',
    hour12: true
});

// Set the content of the #currentDate element
document.getElementById('currentDate').innerText = formattedDateTime;

function printSaleseOrder2() {
    
    var salesOrderModal = new bootstrap.Modal(document.getElementById('printReceiptModal'));
    salesOrderModal.show();

    // Add a class to hide the modal-footer during printing
    document.getElementById('printReceipt').classList.add('no-print-footer');

    // Trigger the print action
    window.print();

    // Remove the class after printing to show the modal-footer again
    document.getElementById('printReceipt').classList.remove('no-print-footer');

    endTransaction()
}

function endTransaction() {
    // Ask the user for confirmation
    var confirmation = confirm("Are you sure you want to end this transaction?");
    
    // Check the user's response
    if (confirmation) {
        // If the user confirms, reload the page
        location.reload();
    } 
}

</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
<script src="../js/receipt_function.js"></script>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>