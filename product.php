<?php 
   $pageName = "Products";
   include './inc/opening.php';

   //Creating table if not created
   $query = "CREATE TABLE IF NOT EXISTS `products` (
      `product_id` int(11) NOT NULL AUTO_INCREMENT,
      `product` varchar(100) NOT NULL,
      `category` varchar(100) NOT NULL,
      `unit` varchar(100) NOT NULL,
      `price` int(11) NOT NULL,
      `quantity` varchar(100) NOT NULL,
      `stock_status` varchar(100) NOT NULL,
      `supplier` varchar(100) NOT NULL,
      `date_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (product_id) 
    )";

   mysqli_query($conn, $query);
?>

<div class="h-100 flex-column justify-content-center" id="main-content"> 
   <div class="d-flex justify-content-between search-buttons m-3 mt-5">
      <input class="p-2 w-50 form-control border-outline-dark" type="search" placeholder="Search" id="search-input" name="search-input" autocomplete="off">
      <button class="btn btn-success btn-sm px-4 ms-5" onclick="printProductTable()"><i class="bi bi-printer"></i></button>
      <button type="button" class="btn btn-danger add-btn px-4 fw-3" data-bs-toggle="modal" data-bs-target="#addProductModal" tabindex="-1" >
         Add Product
      </button>
      
      <!-- Modal For Adding Product-->
      <div class="modal fade modal-lg" id="addProductModal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
         <div class="modal-dialog d-flex justify-content-center p-4 ">
            <div class="modal-content" id="modal-form">
               <div class="modal-header">
                  <h4 class="modal-title textMaroon fw-bold" id="modal-title">Add Product</h4>
               </div>
               <div class="modal-body w-100">
                  <div class="alert alert-warning d-none" id="errorMessage"></div>
                  <form id="add-product">
                        <div class="row m-auto">
                              <div class="col">
                                 <div class="form-group row mb-1">
                                    <label for="product_name" class="col-sm-3 col-form-label label">Product Name: </label>
                                    <input class="col-sm-5" type="text" name="product-name" >
                                 </div>
                                 <div class="form-group row mb-1">
                                    <label for="supplier" class="col-sm-3 col-form-label label">Supplier: </label>
                                    <select class="col-sm-5" name="supplier">
                                    <!-- Getting data from database -->
                                       <option selected></option>
                                       <?php                         
                                       $query = "SELECT business_name FROM suppliers";
                                       $result = mysqli_query($conn, $query);
                                       while ($fetch = mysqli_fetch_array($result)) {
                                       ?>
                                       <option><?php echo $fetch['business_name'];?></option>
                                       <?php
                                          }
                                       ?>
                                    </select>
                                 </div> 
                                 <div class="form-group row mb-1">
                                    <label for="category" class="col-sm-3 col-form-label label">Category: </label>
                                    <select class="col-sm-5" name="category">
                                       <!-- Getting data from database -->
                                       <option selected></option>
                                       <?php                         
                                       $query = "SELECT category_name FROM category";
                                       $result = mysqli_query($conn, $query);
                                       while ($fetch = mysqli_fetch_array($result)) {
                                       ?>
                                       <option><?php echo $fetch['category_name'];?></option>
                                       <?php
                                          }
                                       ?>
                                    </select>
                                 </div> 
                                 <div class="form-group row mb-1">
                                    <label for="price" class="col-sm-3 col-form-label label">Price: </label>
                                    <input class="col-sm-5" type="number" name="price">
                                 </div>
                                 <div class="form-group row mb-1">
                                    <label for="unit" class="col-sm-3 col-form-label label">Unit: </label>
                                    <select class="col-sm-5" type="text" name="unit">
                                       <!-- Getting data from database -->
                                       <option selected></option>
                                       <?php                         
                                       $query = "SELECT unit FROM units";
                                       $result = mysqli_query($conn, $query);
                                       while ($fetch = mysqli_fetch_array($result)) {
                                       ?>
                                       <option><?php echo $fetch['unit'];?></option>
                                       <?php
                                          }
                                       ?>
                                    </select>
                                 </div>
                                 <div class="form-group row mb-1">
                                    <label for="stocks" class="col-sm-3 col-form-label label">Stocks: </label>
                                    <input class="col-sm-5" type="number" name="stocks">
                                 </div>
                              </div>               
                        </div>
                        <div class="row form-down text-center mt-3">
                              <button type="submit" class="add-btn rounded-2 py-3 px-4 w-25 mx-auto action-btn" name="add-inventory" id="submit">ADD</button>
                        </div>
                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary close-modal-btn" data-bs-dismiss="modal">Close</button>
               </div>
            </div>
         </div>
      </div>
      <!-- End of Modal For Adding Product-->
   </div>

   <!----------PRODUCT TABLE LIST----------->
   <div class="container h-100 overflow-y-auto">
      <div class="mt-1 overflow-y-scroll" id="table" style="height: 360px">
         <table class="table table-warning table-hover text-center default-table table-sm table-bordered align-middle px-3" id='table-content'>
            <thead class="table-dark">
               <tr>
                  <th>Code</th>
                  <th>Product</th>
                  <th>Category</th>
                  <th>Unit</th></th>
                  <th>Price</th> 
                  <th>Stocks</th>
                  <th>Stock Level Status</th>
                  <th>Supplier</th>
                  <th class="action-column">Action</th>
               </tr> 
            </thead>                   
            <tbody id="product-details">
               <?php 
                  //Getting the data from database
                  $query = "SELECT * FROM products WHERE product_status!='Phaseout' ORDER BY product ASC ";
                  $result = mysqli_query($conn, $query);
                  while ($fetch = mysqli_fetch_array($result)) {
                     ;
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
                     createPurchase($fetch['product_id'], $conn);
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
      </div>
      <div class="mt-3">
            <button class="btn btn-outline-dark btn-sm mt-4" data-bs-toggle="modal" data-bs-target="#phaseoutProductModal">
            phaseout products >>
            </button>

            <!-- Phaseout Product Modal -->
            <div class="modal fade modal-lg" id="phaseoutProductModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
               <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h5 class="modal-title" id="exampleModalLabel">Phaseout Products</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                           <div class="mt-3 border" id="phaseouttable">
                              <table class="table table-warning text-center table-striped default-table table-bordered align-middle table-sm">
                                    <thead class="table-dark">
                                       <th>Code</th>
                                       <th>Product</th>
                                       <th>Category</th>
                                       <th>Unit</th></th>
                                       <th>Price</th> 
                                       <th>Supplier</th>
                                    </thead>
                                    <tbody id="purchase-details">
                                       <?php
                                       // Getting the data from the database
                                       $query = "SELECT * FROM products WHERE product_status='Phaseout' ORDER BY product ASC ";
                                       $result = mysqli_query($conn, $query);
                                       while ($fetch = mysqli_fetch_array($result)) {
                                       ?>
                                          <tr>
                                             <td><?php echo $fetch['product_id']?></td>
                                             <td><?php echo $fetch['product']?></td>
                                             <td><?php echo $fetch['category']?></td>
                                             <td><?php echo $fetch['unit']?></td>
                                             <td>&#x20B1; <?php echo $fetch['price']?></td>
                                             <td><?php echo $fetch['supplier']?></td>
         
                                          </tr>
                                       <?php } ?>
                                    </tbody>
                              </table>
                           </div>
                           
                        </div>
                        <div class="modal-footer">
                           <button class="btn btn-success btn-sm px-4" onclick="printPhaseOutProductTable()"><i class="bi bi-printer"></i></button>
                           <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        
                        </div>
                     </div>
               </div>
            </div>
      </div>
   </div>
   <!----------END OF PRODUCT TABLE LIST----------->

   <!-- Modal For Updating the Product -->
   <div class="modal fade modal-lg" id="editProductModal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
      <div class="modal-dialog d-flex justify-content-center p-4 ">
         <div class="modal-content" id="modal-form">
            <div class="modal-header">
               <h4 class="modal-title textMaroon fw-bold text-center" id="modal-title">Update Product</h4>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="'close"></button>
            </div>
            <div class="modal-body w-100">
               <div class="alert alert-warning d-none" id="errorMessage_update"></div>
            <form id="update-product">
                  <div class="row m-auto">
                        <input type="hidden" name="product_id" id="product_id">
                        <div class="col">
                           <div class="form-group row mb-1">
                              <label for="product_name" class="col-sm-3 col-form-label label">Product Name: </label>
                              <input class="col-sm-5" type="text" name="product-name" id="product-name">
                           </div>
                           <div class="form-group row mb-1">
                              <label for="supplier" class="col-sm-3 col-form-label label">Supplier: </label>
                              <select class="col-sm-5" name="supplier" id="supplier">
                              <!-- Getting data from database -->
                                 <option selected></option>
                                 <?php                         
                                 $query = "SELECT business_name FROM suppliers";
                                 $result = mysqli_query($conn, $query);
                                 while ($fetch = mysqli_fetch_array($result)) {
                                 ?>
                                 <option><?php echo $fetch['business_name'];?></option>
                                 <?php
                                    }
                                 ?>
                              </select>
                           </div> 
                           <div class="form-group row mb-1">
                              <label for="category" class="col-sm-3 col-form-label label">Category: </label>
                              <select class="col-sm-5" name="category" id="category">
                                 <!-- Getting data from database -->
                                 <option selected></option>
                                 <?php                         
                                 $query = "SELECT category_name FROM category";
                                 $result = mysqli_query($conn, $query);
                                 while ($fetch = mysqli_fetch_array($result)) {
                                 ?>
                                 <option><?php echo $fetch['category_name'];?></option>
                                 <?php
                                    }
                                 ?>
                              </select>
                           </div> 
                           <div class="form-group row mb-1">
                              <label for="price" class="col-sm-3 col-form-label label">Price: </label>
                              <input class="col-sm-5" type="number" name="price" id="price">
                           </div>
                           <div class="form-group row mb-1">
                              <label for="unit" class="col-sm-3 col-form-label label">Unit: </label>
                              <select class="col-sm-5" type="text" name="unit" id="unit">
                                 <!-- Getting data from database -->
                                 <option selected></option>
                                 <?php                         
                                 $query = "SELECT unit FROM units";
                                 $result = mysqli_query($conn, $query);
                                 while ($fetch = mysqli_fetch_array($result)) {
                                 ?>
                                 <option><?php echo $fetch['unit'];?></option>
                                 <?php
                                    }
                                 ?>
                              </select>
                           </div>
                           <div class="form-group row mb-1">
                              <label for="stocks" class="col-sm-3 col-form-label label">Stocks: </label>
                              <input class="col-sm-5" type="number" name="stocks" id="stocks">
                           </div>
                        </div>               
                  </div>
                  <div class="row form-down text-center mt-3">
                        <button type="submit" class="add-btn rounded-2 py-3 px-4 w-25 mx-auto action-btn" name="update-inventory" id="submit">UPDATE</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>
   <!-- End of Modal For Updating the Product -->
   



</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
//Adding Product
$(document).on('submit', '#add-product', function(e) 
{
   e.preventDefault();

   var formData = new FormData(this);
   formData.append('add_product', true);

   $.ajax({
   type: 'POST',
   url: 'productCode.php',
   data: formData,
   processData: false,
   contentType: false,
   success: function(response){
      console.log(response);
      var res = jQuery.parseJSON(response);
      if (res.status == 422) 
      {
         // Show error message inside the modal
         $('#errorMessage').removeClass('d-none');
         $('#errorMessage').text(res.message);

      } 

      else if (res.status == 404) {
         // Show error message inside the modal
         $('#errorMessage').removeClass('d-none');
         $('#errorMessage').text(res.message);
      }
      else if (res.status == 200) 
      {
         //Removing error message (if visible) and hiding modal
         $('#errorMessage').addClass('d-none');
         $('#addProductModal').modal('hide');
         $('#add-product')[0].reset();

         // Reload the table
         $('#table-content').load(location.href + ' #table-content');
      }
   },
   error: function(jqXHR, textStatus, errorThrown) {
   console.log("AJAX request failed: " + errorThrown);
   }
   });
});

//Getting id of the product
$(document).on('click', '.editProductBtn', function()
{
   var product_id = $(this).val();

   $.ajax({
      type: 'GET',
      url: 'productCode.php',
      data: { editProduct: true, product_id: product_id }, // Send 'editProduct' parameter
      success: function(response) {
         var res = jQuery.parseJSON(response);
         console.log(res);
         if (res.status == 404) 
         {
               alert(res.message);

         } else if (res.status == 200) 
         {
            // assigning value to the edit product form
            $('#product_id').val(res.data.product_id);
            $('#product-name').val(res.data.product);
            $('#supplier').val(res.data.supplier);
            $('#category').val(res.data.category);
            $('#price').val(res.data.price);
            $('#unit').val(res.data.unit);
            $('#stocks').val(res.data.stocks);
         }
      }
   });
});

 //Updating the product
 $(document).on('submit', '#update-product', function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append('update_product', true);

    $.ajax({
        type: 'POST',
        url: 'productCode.php',
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
                $('#editProductModal').modal('hide');
                $('#update-product')[0].reset();

                // Reload the table
                $('#table-content').load(location.href + ' #table-content');
            }else if(res.status == 500) {
               alert(res.message);
            }
        }
    });
});

//Deleting product
$(document).on('click', '.deleteProductBtn', function (e) {
   e.preventDefault();

   if(confirm('Are you sure you want to set the product as phaseout?'))
   {
         var product_id = $(this).val();
         $.ajax({
            type: "POST",
            url: "productCode.php",
            data: {
               'phaseout_product': true,
               'product_id': product_id
            },
            success: function (response) {
               var res = jQuery.parseJSON(response);
               if(res.status == 500) {

                     alert(res.message);
               }else{
                  alert(res.message);
                  // Reload the table
                  $('#table-content').load(location.href + ' #table-content');
                  $('#phaseouttable').load(location.href + ' #phaseouttable');
                  
               }
            }
         });
   }
});

// Search 
$(document).ready(function() {
   $('#search-input').keyup(function() {
      var searchInput = $(this).val();
      //alert(searchInput);

      if(searchInput != "") {
         $('.default-table').hide();
         $.ajax({
            url:'productCode.php',
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


function printProductTable() {
   var printWindow = window.open('_blank');
   printWindow.document.write('<html><head><title>Product Table</title>');

   // Include Bootstrap CSS
   printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
   
   var hiddenColumns = document.getElementsByClassName('action-column');
   for (var i = 0; i < hiddenColumns.length; i++) {
      hiddenColumns[i].classList.add('d-none');
   }
   printWindow.document.write('</head><body>');
   // Centered logo with smaller size
   printWindow.document.write('<div class="text-center"><img src="../img/companyLogo2.svg" class="img-fluid" style="max-width: 200px;"></div>');
   printWindow.document.write('<h2 class="text-center">Raydon Construction Trading</h2>');
   printWindow.document.write('<hr><hr>');
   printWindow.document.write('<h2 class="text-center mb-3 mt-1 fw-bolder">Product Stock Report</h2>');
   printWindow.document.write('<div class="container-fluid fs-4">');
   printWindow.document.write(document.getElementById('table').innerHTML);
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

function printPhaseOutProductTable() {
   var printWindow = window.open('_blank');
   printWindow.document.write('<html><head><title>Phaseout Product List</title>');

   // Include Bootstrap CSS
   printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');

   var hiddenColumns = document.getElementsByClassName('action-column');
   for (var i = 0; i < hiddenColumns.length; i++) {
      hiddenColumns[i].classList.add('d-none');
   }
   printWindow.document.write('</head><body>');
   printWindow.document.write('<h2 class="text-center"><img src="../img/companyLogo2.svg" class="w-25 text-center">Raydon Construction Trading</h2>');
   printWindow.document.write('<hr><hr>');
   printWindow.document.write('<h2 class="text-center mt-2" mb-3>Phaseout Product Report</h2>');
   printWindow.document.write('<div class="container-fluid fs-4">');
   printWindow.document.write(document.getElementById('phaseouttable').innerHTML);
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