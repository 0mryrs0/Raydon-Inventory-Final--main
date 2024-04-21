<?php 
   $pageName = "Supplier";
   include './inc/opening.php';

   //Creating a table for suppliers
   $query = "CREATE TABLE IF NOT EXISTS `suppliers` (
      `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
      `business_name` varchar(100) NOT NULL,
      `contact_number` varchar(100) NOT NULL,
      `email` varchar(100) NOT NULL,
      `total_order` int(11) NOT NULL,
      `data_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
       PRIMARY KEY (supplier_id) 
     )";


     mysqli_query($conn, $query);
?>

<!-------------------------------------- Main Page Content-------------------------------- -->
<div class= "container-fluid p-4 d-flex-column" id="main-content">
   <div class="d-flex justify-content-between mt-5">
      <div class="w-100">
         <button class="btn btn-success btn-sm px-4 ms-3" onclick="printSupplierTable()"><i class="bi bi-printer"></i></button>
         <button type="button" class="btn btn-danger add-btn px-4 fw-3 float-end mb-3" data-bs-toggle="modal" data-bs-target="#addSupplierModal" tabindex="-1" >
            Add Supplier
         </button>
      </div>
      <!-- Modal For Adding Supplier-->
      <div class="modal fade modal-lg" id="addSupplierModal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
         <div class="modal-dialog d-flex justify-content-center p-4 ">
            <div class="modal-content" id="modal-form">
               <div class="modal-header">
                  <h4 class="modal-title textMaroon fw-bold text-center" id="modal-title">Add Supplier</h4>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="'close"></button>
               </div>
               <div class="modal-body w-100">
                  <div class="alert alert-warning d-none" id="errorMessage"></div>
                  <form id="add-supplier">
                     <div class="row m-auto">
                        <div class="col">
                           <div class="form-group row mb-1">
                              <label for="business-name" class="col-sm-3 col-form-label label">Business Name: </label>
                              <input class="col-sm-5" type="text" name="business-name" >
                           </div>
                           <div class="form-group row mb-1">
                              <label for="contact" class="col-sm-3 col-form-label label">Contact Number: </label>
                              <input class="col-sm-5" type="text" name="contact">
                           </div>
                           <div class="form-group row mb-1">
                              <label for="email" class="col-sm-3 col-form-label label">Email: </label>
                              <input class="col-sm-5" type="email" name="email">
                           </div>
                        </div>
                     </div>
                     <div class="row form-down text-center mt-3">
                        <button type="submit" class="add-btn rounded-2 py-3 px-4 w-25 mx-auto action-btn" id="add-supplier">ADD SUPPLIER</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!----------SUPPLIER TABLE LIST----------->
   <div class="container h-100 overflow-y-auto">
      <div class="mt-1 overflow-y-scroll" id="table" style="height: 400px">
         <table class="table table-warning table-hover text-center default-table table-sm table-bordered align-middle px-3" id='table-content'>
            <thead class="table-dark">
               <tr>
                  <th>Supplier Id</th>
                  <th>Business Name</th>
                  <th>Contact Number</th>
                  <th>Email</th>
                  <th class="action-column">Action</th>
               </tr>
            </thead>
            <tbody>
               <?php
               //Getting the data from the database
               $query = "SELECT * FROM suppliers WHERE supplier_status!='INACTIVE' ORDER BY business_name ASC";
               $result = mysqli_query($conn, $query);
               while ($fetch = mysqli_fetch_array($result)) {

               ?>
                  <tr>
                     <!--Displaying the data in table form -->
                     <td><?php echo $fetch['supplier_id']?></td>
                     <td><?php echo $fetch['business_name']?></td>
                     <td><?php echo $fetch['contact_number']?></td>
                     <td><?php echo $fetch['email']?></td>
                     <td class='action action-column'>
                        <button type='button' value='<?php echo $fetch['supplier_id']?>' class='editSupplierBtn action-btn opacity-btn'  data-bs-toggle='modal' data-bs-target='#editSupplierModal' tabindex='-1' >
                           <i class='fa-regular fa-pen-to-square p-2 bgYellow text-white' data-bs-toggle="tooltip" title="Edit supplier details"></i>
                        </button>
                        <button type='button' value='<?php echo $fetch['supplier_id']?>' class='deleteSupplierBtn delete-btn action-btn opacity-btn'>
                           <i class='bi bi-x-circle-fill p-2  bgMaroon text-white' data-bs-toggle="tooltip" title="Set supplier as inactive"></i>
                        </button>
                     </td>                          
                  </tr>

               <?php }?>                                
            </tbody>
         </table>
      </div>
      <div>
         <button class="btn btn-outline-dark btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#inactiveSupplierModal">
            inactive suppliers >>
         </button>
         
               <!-- Modal -->
               <div class="modal fade modal-xl" id="inactiveSupplierModal" tabindex="-1" aria-labelledby="inactiveSupplierModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                     <div class="modal-content">
                           <div class="modal-header">
                              <h5 class="modal-title" id="inactiveSupplierModalLabel">Inactive Suppliers</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                           </div>
                           <div class="modal-body">
                              <div class="mt-3 border" id="inactiveSupplierTable">
                                 <table class="table table-warning text-center table-striped default-table table-bordered align-middle table-sm">
                                       <thead class="table-dark">
                                       <th>Supplier Id</th>
                                          <th>Business Name</th>
                                          <th>Contact Number</th>
                                          <th>Email</th>
                                          <th>Action</th>
                                       </thead>
                                       <tbody id="purchase-details">
                                          <?php
                                          // Getting the data from the database
                                          $query = "SELECT * FROM suppliers WHERE supplier_status='INACTIVE' ORDER BY business_name ASC";
                                          $result = mysqli_query($conn, $query);
                                          while ($fetch = mysqli_fetch_array($result)) {
                                          ?>
                                             <tr>
                                                <!--Displaying the data in table form -->
                                                <td><?php echo $fetch['supplier_id']?></td>
                                                <td><?php echo $fetch['business_name']?></td>
                                                <td><?php echo $fetch['contact_number']?></td>
                                                <td><?php echo $fetch['email']?></td>
                                                <td><button class="btn btn-success btn-sm active" value="<?php echo $fetch['supplier_id']?>">active</button></td>
                                             </tr>
                                          <?php } ?>
                                       </tbody>
                                 </table>
                              </div>
                           </div>
                           <div class="modal-footer">
                              <button class="btn btn-success btn-sm px-4" onclick="printInactiveSupplierTable()"><i class="bi bi-printer"></i></button>     
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                           </div>
                     </div>
                  </div>
               </div>
      </div>
   </div>


   <!-- Modal For Updating the Supplier -->
   <div class="modal fade modal-lg" id="editSupplierModal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
            <div class="modal-dialog d-flex justify-content-center p-4 ">
               <div class="modal-content" id="modal-form">
                  <div class="modal-header">
                     <h4 class="modal-title textMaroon fw-bold text-center" id="modal-title">Update Supplier</h4>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="'close"></button>
                  </div>
                  <div class="modal-body w-100">
                     <div class="alert alert-warning d-none" id="errorMessage-update"></div>
                  <form id="update-supplier">
                        <div class="row m-auto">
                                 <input type="hidden" name="supplier_id" id="supplier_id">
                                 <div class="col">
                                    <div class="form-group row mb-1">
                                       <label for="business-name" class="col-sm-3 col-form-label label">Business Name: </label>
                                       <input class="col-sm-5" type="text" name="business-name" id="business-name">
                                    </div>
                                    <div class="form-group row mb-1">
                                       <label for="contact" class="col-sm-3 col-form-label label">Contact Number: </label>
                                       <input class="col-sm-5" type="text" name="contact" id="contact">
                                    </div> 
                                    <div class="form-group row mb-1">
                                       <label for="email" class="col-sm-3 col-form-label label">Email: </label>
                                       <input class="col-sm-5" type="email" name="email" id="email">
                                    </div>
                                 </div>              
                        </div>
                        <div class="row form-down text-center mt-3">
                              <button type="submit" class="add-btn rounded-2 py-3 px-4 w-25 mx-auto action-btn">UPDATE SUPPLIER</button>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
   </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
   //Adding supplier
   $(document).ready(function() {
    // Initialize form submission event for the add-supplier form
    $(document).on('submit', '#add-supplier', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('add_supplier', true);

        $.ajax({
            type: 'POST',
            url: 'supplierCode.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
         
                var res = jQuery.parseJSON(response);
                if (res.status == 422) {
                    // Show error message inside the modal
                    $('#errorMessage').removeClass('d-none');
                    $('#errorMessage').text(res.message);

                } else if (res.status == 200) {
                    alert(res.message);
                    $('#errorMessage').addClass('d-none');
                    $('#addSupplierModal').modal('hide');
                    $('#add-supplier')[0].reset();

                    // Reload the table
                    $('#table-content').load(location.href + ' #table-content');
                }
             },

            error: function(jqXHR, textStatus, errorThrown) {
                console.log("AJAX request failed: " + errorThrown);
             }
           });
    });
});

 //Getting id of supplier
 $(document).on('click', '.editSupplierBtn', function() {
    var supplier_id = $(this).val();
    $.ajax({
        type: 'GET',
        url: 'supplierCode.php',
        data: { editSupplier: true, supplier_id: supplier_id }, // Send 'editProduct' parameter
        success: function(response) {
            console.log("Response:", response); 
            var res = jQuery.parseJSON(response);
            if (res.status == 404) {
                alert(res.message);
            } else if (res.status == 200) {
                // Data added successfully, close the modal and reset the form
                
                $('#supplier_id').val(res.data.supplier_id);
                $('#business-name').val(res.data.business_name);
                $('#contact').val(res.data.contact_number);
                $('#email').val(res.data.email);
                $('#editSupplierModal').modal('show');
            }
         }
    });
});

 //Updating the supplier
 $(document).on('submit', '#update-supplier', function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    formData.append('update_supplier', true);

    $.ajax({
        type: 'POST',
        url: 'supplierCode.php',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log("Response:", response); 
            var res = jQuery.parseJSON(response);
            if (res.status == 422) {
                // Show error message inside the modal
                $('#errorMessage-update').removeClass('d-none');
                $('#errorMessage-update').text(res.message);
            } else if (res.status == 200) {
                alert(res.message);
                $('#errorMessage-update').addClass('d-none');
                $('#editSupplierModal').modal('hide');
                $('#update-supplier')[0].reset();

                // Reload the table
                $('#table-content').load(location.href + ' #table-content');
                
            }else if(res.status == 500) {
               alert(res.message);
            }
        }
    });
});

//Deleting supplier
$(document).on('click', '.deleteSupplierBtn', function (e) {
   e.preventDefault();

   if(confirm('Are you sure this product is not active anymore?')){
      var supplier_id = $(this).val();
      $.ajax({
         type: "POST",
         url: "supplierCode.php",
         data: {
            'inactive_supplier': true,
            'supplier_id': supplier_id
         },
         success: function (response) {

            var res = jQuery.parseJSON(response);
            if(res.status == 500) {

                  alert(res.message);
            }else if (res.status == 200){
                  alert(res.message);
                  $('#table-content').load(location.href + " #table-content");
                  $('#inactiveSupplierTable').load(location.href + " #inactiveSupplierTable");
            }
         }
         });
   }
});


$(document).on('click', '.active', function (e) {
   e.preventDefault();

   if(confirm('Are you sure that this supplier is active again?')){
      var supplier_id = $(this).val();
      $.ajax({
         type: "POST",
         url: "supplierCode.php",
         data: {
            'active_supplier': true,
            'supplier_id': supplier_id
         },
         success: function (response) {

            var res = jQuery.parseJSON(response);
            if(res.status == 500) {

                  alert(res.message);
            }else if (res.status == 200){
                  alert(res.message);
                  $('#table-content').load(location.href + " #table-content");
                  $('#inactiveSupplierTable').load(location.href + " #inactiveSupplierTable");
            }
         }
         });
   }
});

function printSupplierTable() {
   var printWindow = window.open('_blank');
   printWindow.document.write('<html><head><title>Product Table</title>');

   // Include Bootstrap CSS
   printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
   var hiddenColumns = document.getElementsByClassName('action-column');
   for (var i = 0; i < hiddenColumns.length; i++) {
      hiddenColumns[i].classList.add('d-none');
   }
   printWindow.document.write('</head><body>');
   printWindow.document.write('<h2 class="text-center mt-4 mb-2">List of Supplier Report</h2>');
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

function printInactiveSupplierTable() {
   var printWindow = window.open('_blank');
   printWindow.document.write('<html><head><title>Product Table</title>');

   // Include Bootstrap CSS
   printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
   var hiddenColumns = document.getElementsByClassName('action-column');
   for (var i = 0; i < hiddenColumns.length; i++) {
      hiddenColumns[i].classList.add('d-none');
   }
   printWindow.document.write('</head><body>');
   printWindow.document.write('<h2 class="text-center mt-4 mb-2">Inactive Supplier Report</h2>');
   printWindow.document.write('<div class="container-fluid fs-4">');
   printWindow.document.write(document.getElementById('inactiveSupplierTable').innerHTML);
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