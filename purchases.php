<?php 
   $pageName = "Purchases";
   include './inc/opening.php';

   //Creating table if not created
   $query = "CREATE TABLE IF NOT EXISTS `purchases` (
      `purchase_id` int(11) NOT NULL AUTO_INCREMENT,
      `order_number` int(11) NOT NULL,
      `product` varchar(100) NOT NULL,
      `category` varchar(100) NOT NULL,
      `quantity` varchar(100) NOT NULL,
      /*`total_stock` int(11) NOT NULL,*/
      `date_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (purchase_id) 
    )";

   mysqli_query($conn, $query);
?>

<!-------------------------------------- Main Page Content-------------------------------- -->
<div class= "container-fluid p-4 d-flex-column" id="main-content">
            <div class="d-flex justify-content-between mt-3" id="purchase-mainPage" >
               <!-- <input class="p-2 w-25 form-control" type="search" placeholder="Search" id="search-input" name="search-input" autocomplete="off"> -->
                  <div class="w-100">
                     <button type="button" class="btn btn-danger add-btn px-4 fw-3 float-end" data-bs-toggle="modal" data-bs-target="#addPurchModal" tabindex="-1" >
                     New Purchase Order
                     </button>
                  </div>
                  <!-- Modal For New Purchase Order-->
                  <div class="modal fade modal-lg" id="addPurchModal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
                     <div class="modal-dialog d-flex justify-content-center p-4 ">
                        <div class="modal-content" id="modal-form">
                           <div class="modal-header">
                              <h4 class="modal-title fw-bold text-center" id="modal-title">Item</h4>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="'close"></button>
                           </div>
                           <div class="modal-body w-100">
                              <div class="alert alert-warning d-none" id="errorMessage"></div>
                           <form id="new-purchase">
                                 <div class="row m-auto">
                                       <div class="col">
                                          <div class="form-group row mb-1">
                                          <label for="product-name" class="col-sm-3 col-form-label label">Product Name:</label>
                                          <select class="col-sm-5" name="product-name">
                                             <!-- Getting data from the database -->
                                             <option ></option>
                                             <?php
                                             $query = "SELECT * FROM products WHERE product_status!='Phaseout' ORDER BY product ASC";
                                             $result = mysqli_query($conn, $query);
                                             while ($fetch = mysqli_fetch_array($result)) {
                                                   ?>
                                                   <option><?php echo $fetch['product']; ?></option>
                                                   <?php
                                             }
                                             ?>
                                          </select>
                                          </div> 
                                          <div class="form-group row mb-1">
                                             <label for="quantity" class="col-sm-3 col-form-label label">Quantity: </label>
                                             <input class="col-sm-5" type="number" name="quantity">
                                          </div>
                                       </div>               
                                 </div>
                                 <div class="row form-down mt-3">
                                       <button type="submit" class="add-order rounded-2 py-2 px-3 w-25 ms-5 me-3 mt-4 mb-4 action-btn btn btn-success" name="add-order" id="submit">Add Order</button>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>

            </div>

            <!----------LOW PRODUCTS TABLE LIST----------->
            <h5 class="fw-bold text-danger mt-2 ">For Purchase<span class="text-dark fw-light ms-3">(Please select product to order)</h5>
            <div class="container h-100 overflow-y-auto" id="low-products-table">
               <div class="mt-1 overflow-y-scroll" id="table" style="height: 300px">
                  <table class="table table-warning table-hover text-center default-table table-sm table-bordered align-middle px-3 update-table" id="for-purchase">
                     <thead class="table-dark">
                           <th>Code</th>
                           <th>Product</th>
                           <th>Quantity</th>
                           <th>Supplier</th>
                           <th>Order Date</th>
                           <th>Action</th>
                           <th>Add</th>
                     </thead>                   
                     <tbody id="purchase-details">
                        <?php 
                           //Getting the data from database
                           $query = "SELECT * from purchases WHERE purchase_orderNumber='No purchase yet'";
                           $result = mysqli_query($conn, $query);
                           while ($fetch = mysqli_fetch_array($result)) {

                        ?>

                        <tr >
                           <td><?php echo $fetch['purchase_id']?></td>
                           <td><?php echo $fetch['product']?></td>
                           <td><?php echo $fetch['quantity']?></td>
                           <td><?php echo $fetch['supplier_name']?></td>
                           <td><?php echo $fetch['date_added']?></td>

                           <td class='action'>
                              <button type='button' value='<?php echo $fetch['purchase_id']?>' class='action-btn opacity-btn editPurchBtn'  data-bs-toggle='modal' data-bs-target='#editPurchModal' tabindex='-1' >
                                 <i class='fa-regular fa-pen-to-square p-2 bgYellow text-white'></i>
                              </button>
                           </td>
                           <td>
                              <button type='button' value='<?php echo $fetch['purchase_id']?>' class='btn btn-success btn-sm btn-create-order'>
                                 <!-- checkbox-here -->
                                 <input type="checkbox" class="purchase-checkbox" data-purchase-id="<?php echo $fetch['purchase_id']?>" />
                              </button>
                           </td>
                        </tr>

                        <?php } ?>

                     </tbody>
                  </table>
               </div>
               <div class="mt-4">
               <button class="btn btn-secondary create-orderBySupplier">
                  create order
               </button>
               <!-- Button to trigger the modal -->
               <button class="btn btn-warning float-end text-white" data-bs-toggle="modal" data-bs-target="#currentOrdersModal">
                  View Current Orders>>
               </button>
               <br>
               <button type="button" class="btn btn-outline-dark btn-sm mt-4" data-bs-toggle="modal" data-bs-target="#purchaseOrderHistoryModal">
                  purchase order history
               </button>

               <!-- Modal -->
               <div class="modal fade modal-lg" id="purchaseOrderHistoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                     <div class="modal-content">
                           <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Purchase Order History</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                           </div>
                           <div class="modal-body">
                              <div class="mt-3 border" id="purchaseOrderHistoryTable">
                                 <table class="table table-warning text-center table-striped default-table table-bordered align-middle table-sm update-table" id="history-purchase">
                                       <thead class="table-dark">
                                          <th>Purchase Order Number</th>
                                          <th>Product</th>
                                          <th>Quantity</th>
                                          <th>Supplier Name</th>
                                          <th>Date Created</th>
                                          <th>Status</th>
                                       </thead>
                                       <tbody id="purchase-details">
                                          <?php
                                          // Getting the data from the database
                                          $query = "SELECT * from purchases WHERE delivery_status='COMPLETED'";
                                          $result = mysqli_query($conn, $query);
                                          while ($fetch = mysqli_fetch_array($result)) {
                                          ?>
                                             <tr>
                                                   <td><?php echo $fetch['purchase_orderNumber'] ?></td>
                                                   <td><?php echo $fetch['product'] ?></td>
                                                   <td><?php echo $fetch['quantity'] ?></td>
                                                   <td><?php echo $fetch['supplier_name'] ?></td>
                                                   <td><?php echo $fetch['date_added'] ?></td>
                                                   <td><?php echo $fetch['delivery_status'] ?></td>
                                             </tr>
                                          <?php } ?>
                                       </tbody>
                                 </table>
                              </div>
                           </div>
                           <div class="modal-footer">
                              <button class="btn btn-success btn-sm px-4" onclick="printOrderHistoryTable()"><i class="bi bi-printer"></i></button>     
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                           </div>
                     </div>
                  </div>
               </div>
               
            </div>
            </div>
            <hr>
            <!-- Current orders -->
            <div class="modal fade" id="currentOrdersModal" tabindex="-1" aria-labelledby="currentOrdersModalLabel" aria-hidden="true">
               <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                        <div class="modal-header">
                           <h5 class="modal-title" id="currentOrdersModalLabel">Current Orders</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                           <div class="mt-3 border" id="currentOrdersTable">
                              <table class="table table-warning text-center table-striped default-table table-bordered align-middle table-sm update-table" id="current-purchase">
                                    <thead class="table-dark">
                                       <th>Purchase Order Number</th>
                                       <th>Supplier</th>
                                       <th>Total Product Order</th>
                                       <th>Date Created</th>
                                       <th>Status</th>
                                       <th class="action-column">Action</th>
                                    </thead>
                                    <tbody id="purchase-details">
                                       <?php
                                       // Getting the data from the database
                                       $query = "SELECT * from purchase_orders WHERE order_status='PENDING'";
                                       $result = mysqli_query($conn, $query);
                                       while ($fetch = mysqli_fetch_array($result)) {
                                       ?>
                                          <tr>
                                                <td><?php echo $fetch['purchase_orderNumber'] ?></td>
                                                <td><?php echo $fetch['supplier'] ?></td>
                                                <td><?php echo $fetch['total_productOrder'] ?></td>
                                                <td><?php echo $fetch['date_created'] ?></td>
                                                <td><?php echo $fetch['order_status'] ?></td>
                                                <td class='action action-column'>
                                                <button type='button' value="<?php echo $fetch['purchase_orderNumber']?>" class='action-btn btn btn-success btn-sm purchase-order-btn' data-bs-toggle='modal' data-bs-target='#purchaseOrderModal' tabindex='-1' name="purchaseOrderNumber" id="purchaseOrderNumber">
                                                   View
                                                </button>
                                                <button type='button' value="<?php echo $fetch['purchase_orderNumber']?>" class='action-btn btn btn-warning text-white btn-sm' id="deliveredBtn">
                                                   Delivered
                                                </button>
                                                </td>
                                          </tr>
                                       <?php } ?>
                                    </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button class="btn btn-success btn-sm px-4" onclick="printCurrentOrdersTable()"><i class="bi bi-printer"></i></button>
                           <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                  </div>
               </div>
            </div>

               <!-- Modal For Updating the Purchase -->
            <div class="modal fade modal-lg" id="editPurchModal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true" >
               <div class="modal-dialog d-flex justify-content-center p-4 ">
                  <div class="modal-content" id="modal-form">
                     <div class="modal-header">
                        <h4 class="modal-title textMaroon fw-bold text-center" id="modal-title">Update Purchase</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="'close"></button>
                     </div>
                     <div class="modal-body w-100">
                        <div class="alert alert-warning d-none" id="errorMessage_update"></div>
                        <form id="update-purchase">
                           <input type="hidden" name="purchase_id" id="purchase_id">
                           <div class="row m-auto">
                                 <div class="col">
                                    <div class="form-group row mb-1">
                                       <label for="order-number" class="col-sm-3 col-form-label label">Order Number: </label>
                                       <input class="col-sm-5" type="text" name="order-number" readonly=true id="order-number">
                                    </div>
                                    <div class="form-group row mb-1">
                                       <label for="product-name" class="col-sm-3 col-form-label label">Product Name: </label>
                                       <input class="col-sm-5" type="text" name="product-name" readonly=true id="product-name">
                                    </div> 
                                    <div class="form-group row mb-1">
                                       <label for="supplier" class="col-sm-3 col-form-label label">Supplier: </label>
                                       <input class="col-sm-5" type="text" name="category" readonly=true id="supplier">
                                    </div>
                                    <div class="form-group row mb-1">
                                       <label for="quantity" class="col-sm-3 col-form-label label">Quantity: </label>
                                       <input class="col-sm-5" type="number" name="quantity" id="quantity">
                                    </div>
                                 </div>               
                           </div>
                           <div class="row form-down mt-3">
                                 <!-- <button type="submit" class="update-order rounded-2 py-2 px-3 w-25 ms-5 mt-4 mb-4 action-btn btn btn-success" name="update-order" id="submit">UPDATE</button> -->
                                 <button type="submit" class="update-order rounded-2 py-2 px-2 w-25 mx-auto mb-3 mt-2 action-btn btn btn-success" name="update-order" id="submit">UPDATE</button>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>

            <!--Modal For Purchase Order-->
            <div class="modal" id="purchaseOrderModal" tabindex="-1" aria-labelledby="purchaseOrderLabel">
               <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h5 class="modal-title" id="">Purchase Order</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                           <!-- Purchase Order Container -->
                           <div class="container" id="purchaseOrder">
                                 <div class="purchase-header text-left">
                                    <div class="w-100">
                                       <img src="../img/companyLogo2.svg" class="w-25">
                                       <text class="h-6">Raydon Construction Trading</text>
                                    </div>
                                    <h3 class="text-center">Purchase Order</h3>
                                    <hr><hr>
                                    <h4 id="supplier-name">Supplier: </h4>
                                    <h5 class="mt-4">Product List:</h5>
                                    <hr><hr>
                                 </div>
                                 <div class="purchase-body">
                                    <table class="w-100 table table-striped">
                                       <thead>
                                             <tr>
                                                <th>Product Name</th>
                                                <th>Quantity</th>
                                             </tr>
                                       </thead>
                                       <tbody id="purchase-details-table">
                                             
                                       </tbody>
                                    </table>
                                 </div>

                                 <hr>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" onclick="printPurchaseOrder2() " class="btn btn-success" id="print-btn">Print</button>
                           <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="close-btn">Close</button>
                        </div>
                     </div>
               </div>
            </div>

      </div>



<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>

 //Purchasing New Order
$(document).on('submit', '#new-purchase', function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append('new_purchase', true);

    $.ajax({
        type: 'POST',
        url: 'purchasesCode.php',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            var res = jQuery.parseJSON(response);
            console.log(response);
            if (res.status == 422) {
                // Show error message inside the modal
                $('#errorMessage').removeClass('d-none');
                $('#errorMessage').text(res.message);
            } else if (res.status == 200) {
                $('#errorMessage').addClass('d-none');
                $('#addPurchModal').modal('hide');
                $('#new-purchase')[0].reset();

                // Reload the table
                location.reload()
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
        console.log("AJAX request failed: " + errorThrown);
    }
    });
});

 //Getting id of the purchase
 $(document).on('click', '.editPurchBtn', function() {
    var purchase_id = $(this).val();

    $.ajax({
        type: 'GET',
        url: 'purchasesCode.php',
        data: { editPurchase: true, purchase_id: purchase_id }, // Send 'editPurchase' parameter
        success: function(response) {
            var res = jQuery.parseJSON(response);
            if (res.status == 404) {
                alert(res.message);
            } else if (res.status == 200) {
                // Data added successfully, close the modal and reset the form

                $('#purchase_id').val(res.data.purchase_id);
                $('#order-number').val(res.data.order_number);
                $('#product-name').val(res.data.product);
                $('#supplier').val(res.data.supplier_name);
                $('#quantity').val(res.data.quantity);
                $('#editPurchModal').modal('show');
            }
        }
    });
});

 //Updating the purchase
 $(document).on('submit', '#update-purchase', function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append('update_purchase', true);

    $.ajax({
        type: 'POST',
        url: 'purchasesCode.php',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log(response);
            var res = jQuery.parseJSON(response);
            if (res.status == 422) {
                // Show error message inside the modal
                $('#errorMessage_update').removeClass('d-none');
                $('#errorMessage_update').text(res.message);
            } else if (res.status == 200) {
                alert(res.message);
                $('#errorMessage_update').addClass('d-none');
                $('#editPurchModal').modal('hide');
                $('#update-purchase')[0].reset();

                // Reload the table
                location.reload();
            }else if(res.status == 500) {
               alert(res.message);
            }
        }
    });
});

$(document).ready(function() {
    // Initialize an empty object to store selected orders by supplier
    var selectedOrdersBySupplier = {};

    function updateButtonState() {
        if (Object.keys(selectedOrdersBySupplier).length > 0) {
            $('.create-orderBySupplier').removeClass('btn-secondary').addClass('btn-primary');
        } else {
            $('.create-orderBySupplier').removeClass('btn-primary').addClass('btn-secondary');
        }
    }

    // Attach click event to the "create order" button
    $('.btn-create-order').on('click', function() {
        // Get the row associated with the clicked button
        var row = $(this).closest('tr');

        // Extract relevant information from the row
        var purchaseId = row.find('td:eq(0)').text();
        var product = row.find('td:eq(1)').text();
        var quantity = row.find('td:eq(2)').text();
        var supplier = row.find('td:eq(3)').text();
        var orderDate = row.find('td:eq(4)').text();

        // Create an order object
        var order = {
            purchaseId: purchaseId,
            product: product,
            quantity: quantity,
            supplier: supplier,
            orderDate: orderDate
        };

        // Check if the supplier exists in the selectedOrdersBySupplier object
        if (!(supplier in selectedOrdersBySupplier)) {
            // If not, initialize an array for the supplier
            selectedOrdersBySupplier[supplier] = [];
        }

        // Check if the purchaseId already exists in the array
        var existingOrder = selectedOrdersBySupplier[supplier].find(function(existingOrder) {
            return existingOrder.purchaseId === purchaseId;
        });

        // Add the order to the corresponding supplier's array if it doesn't exist
        if (!existingOrder) {
            selectedOrdersBySupplier[supplier].push(order);
        }

        // Display the current selected orders by supplier in the console (for testing)
        console.log(selectedOrdersBySupplier);
        updateButtonState();
    });

    // Attach click event to the checkboxes
    $('.purchase-checkbox').on('change', function() {
        // Get the row associated with the clicked checkbox
        var row = $(this).closest('tr');

        // Extract relevant information from the row
        var purchaseId = row.find('td:eq(0)').text();
        var supplier = row.find('td:eq(3)').text();

        // Check if the checkbox is checked
        if ($(this).prop('checked')) {
            // Check if the supplier exists in the selectedOrdersBySupplier object
            if (!(supplier in selectedOrdersBySupplier)) {
                // If not, initialize an array for the supplier
                selectedOrdersBySupplier[supplier] = [];
            }

            // Check if the purchaseId already exists in the array
            var existingOrder = selectedOrdersBySupplier[supplier].find(function(existingOrder) {
                return existingOrder.purchaseId === purchaseId;
            });

            // Add the order to the corresponding supplier's array if it doesn't exist
            if (!existingOrder) {
                selectedOrdersBySupplier[supplier].push({
                    purchaseId: purchaseId
                });
            }
        } else {
            // Remove the corresponding order from the selected orders array for the supplier
            if (supplier in selectedOrdersBySupplier) {
                selectedOrdersBySupplier[supplier] = selectedOrdersBySupplier[supplier].filter(function(order) {
                    return order.purchaseId !== purchaseId;
                });

                // Remove the supplier entry if the array is empty
                if (selectedOrdersBySupplier[supplier].length === 0) {
                    delete selectedOrdersBySupplier[supplier];
                }
            }
        }

        // Display the current selected orders by supplier in the console (for testing)
        
        updateButtonState();
    });

    
        // Attach click event to the "create order by supplier" button
        $('.create-orderBySupplier').on('click', function() {
        // Check if there are selected orders
        if (Object.keys(selectedOrdersBySupplier).length > 0) {
            // Make a POST request to the server
            $.ajax({
                type: 'POST',
                url: 'purchasesCode.php',
                data: { 
                   create_purchaseOrder: true,
                   orders: JSON.stringify(selectedOrdersBySupplier)
                },
                success: function(response) {
                    console.log(response); // Log the server response
                    location.reload();
                },
                error: function(error) {
                    console.error(error); // Log any errors
                    // Handle the error (if needed)
                }
            });
        } else {
            // Handle the case when no orders are selected
            console.log('No orders selected.');
        }
    });

});

$(document).ready(function() {
   $('.purchase-order-btn').click(function(e) {
      e.preventDefault();

      // Get the purchase order number from the button value
      var purchaseOrderNumber = $(this).val();

      // Send the purchase order number to purchaseCode.php using Ajax
      $.ajax({
         type: 'POST',
         url: 'purchasesCode.php',
         data: {
            getPurchaseDetails: true,
            purchaseOrderNumber: purchaseOrderNumber
         },
         success: function(response) {
            var res = jQuery.parseJSON(response);
            var purchaseDetails = res.data;
            console.log('Purchase Details:', purchaseDetails); // Add this line

            // Select the tbody of your table (assuming you have a table structure)
            var tbody = $('#purchase-details-table tbody');

            // Clear existing rows
            tbody.empty();

            var supplierName = purchaseDetails[0].supplier_name;
            $('#supplier-name').html('<h4>Supplier: ' +  supplierName + '</h4>')
            $('#purchase-details-table').empty();

            var i = 0;
               while (i < purchaseDetails.length) {
                  var row = purchaseDetails[i];

                  // Access properties of each row
                  var productName = row.product;
                  var quantity = row.quantity;

                  // Create a new row
                  var newRow = $('<tr>');

                  // Append cells to the new row
                  newRow.append($('<td>').html(productName));
                  newRow.append($('<td>').html(quantity));

                  // Append the new row to the table body
                  $('#purchase-details-table').append(newRow);

                  // Increment the counter
                  i++;
               }
         },
         error: function(error) {
            // Handle errors if any
            console.error(error);
         }
      });
   });
});

// Search 
$(document).ready(function() {
   $('#search-input').keyup(function() {
      var searchInput = $(this).val();
      //alert(searchInput);

      if(searchInput != "") {
         $('.default-table').hide();
         $.ajax({
            url:'purchasesCode.php',
            method:'POST',
            data: {
               'searchInput':searchInput
            },

            success: function(response) {
               $('.search-table').remove();
               $('#table').append(response);
            }
         })
      }else {
        $('.default-table').show();
        $('.search-table').remove();
      }
   });

});

//Updating stocks
$(document).on('click', '#deliveredBtn', function(e) {
   e.preventDefault();

if(confirm('Are you sure that this product has been delivered?'))
{
    var purchaseOrderNumber = $(this).val();
    $.ajax({
        type: "POST",
        url: "purchasesCode.php",
        data: {
            deliveredProduct: true,
            purchaseOrderNumber: purchaseOrderNumber
        },
        success: function (response) {
        var res = jQuery.parseJSON(response);

        location.reload();
        }
    });
 }
});


</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
<script>
/* function printPurchaseOrder() {

   var element = document.getElementById('purchaseOrder');
   var opt = {
      margin: 10,
      filename: 'purchaseOrder.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 1 },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait', autoPrint: true }
   };

   // Use the output option to get the PDF content as a data URL
   html2pdf(element, opt).outputPdf().then(function (pdfDataUrl) {
      // Open the PDF in the current window
      window.location.href = pdfDataUrl;

      element.style.height = 'auto';
   });
}
 */
function printPurchaseOrder2() {
    // Show the purchase order modal
    var purchaseOrderModal = new bootstrap.Modal(document.getElementById('purchaseOrderModal'));

    // Add a class to hide the modal-footer during printing
    document.getElementById('print-btn').classList.add('d-none');
    document.getElementById('close-btn').classList.add('d-none');

    // Trigger the print action
    window.print();

    // Remove the class to show the modal-footer after printing
    document.getElementById('print-btn').classList.remove('d-none');
    document.getElementById('close-btn').classList.remove('d-none');

   
}

function printCurrentOrdersTable() {
   var printWindow = window.open('_blank');
   printWindow.document.write('<html><head><title>Product Table</title>');

   // Include Bootstrap CSS
   printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
   var hiddenColumns = document.getElementsByClassName('action-column');
   for (var i = 0; i < hiddenColumns.length; i++) {
      hiddenColumns[i].classList.add('d-none');
   }
   printWindow.document.write('</head><body>');
   printWindow.document.write('<h2 class="text-center"><img src="../img/companyLogo2.svg" class="w-25 text-center">Raydon Construction Trading</h2>');
   printWindow.document.write('<hr><hr>');
   printWindow.document.write('<h2 class="text-center mt-2">Current Orders Report</h2>');
   printWindow.document.write('<div class="container-fluid fs-4">');
   printWindow.document.write(document.getElementById('currentOrdersTable').innerHTML);
   printWindow.document.write('</div>');

   var adminElement = document.getElementById('admin');
   var dateElement = document.getElementById('date');

   // Include the HTML content of adminElement
   printWindow.document.write(`<h6 class="text-center mt-3">Prepared by: ${adminElement ? adminElement.innerHTML : ''}</h6>`);
   var currentDate = new Date();
   var formattedDate = `${currentDate.toLocaleString('default', { month: 'long' })} ${currentDate.getDate()}, ${currentDate.getFullYear()}`;
   printWindow.document.write(`<h6 class="fs-6 fw-light text-center" id="date">Prepared on: ${formattedDate}</h6>`);
   printWindow.document.write('</body></html>');
   printWindow.document.close();

   var hiddenColumns = document.getElementsByClassName('action-column');
   for (var i = 0; i < hiddenColumns.length; i++) {
      hiddenColumns[i].classList.remove('d-none');
   }
   //printWindow.print();
}

function printOrderHistoryTable() {
   var printWindow = window.open('_blank');
   printWindow.document.write('<html><head><title>Product Table</title>');

   // Include Bootstrap CSS
   printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
   var hiddenColumns = document.getElementsByClassName('action-column');
   for (var i = 0; i < hiddenColumns.length; i++) {
      hiddenColumns[i].classList.add('d-none');
   }
   printWindow.document.write('</head><body>');
   printWindow.document.write('<h2 class="text-center"><img src="../img/companyLogo2.svg" class="w-25 text-center">Raydon Construction Trading</h2>');
   printWindow.document.write('<hr><hr>');
   printWindow.document.write('<h2 class="text-center mt-2">Purchased Product Report</h2>');
   printWindow.document.write('<div class="container-fluid fs-4">');
   printWindow.document.write(document.getElementById('purchaseOrderHistoryTable').innerHTML);
   printWindow.document.write('</div>');

   var adminElement = document.getElementById('admin');
   var dateElement = document.getElementById('date');

   // Include the HTML content of adminElement
   printWindow.document.write(`<h6 class="text-center mt-3">Prepared by: ${adminElement ? adminElement.innerHTML : ''}</h6>`);
   var currentDate = new Date();
   var formattedDate = `${currentDate.toLocaleString('default', { month: 'long' })} ${currentDate.getDate()}, ${currentDate.getFullYear()}`;
   printWindow.document.write(`<h6 class="fs-6 fw-light text-center" id="date">Prepared on: ${formattedDate}</h6>`);
   printWindow.document.write('</body></html>');
   printWindow.document.close();

   var hiddenColumns = document.getElementsByClassName('action-column');
   for (var i = 0; i < hiddenColumns.length; i++) {
      hiddenColumns[i].classList.remove('d-none');
   }
   //printWindow.print();
}
</script>

<?php include './inc/closing.php';?>