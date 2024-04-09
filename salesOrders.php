<?php 
   $pageName = "Sales";
   include './inc/opening.php';
?>

<div class= "container-fluid d-flex-column h-100" id="main-content">
   <div class='w-100 d-flex align-items-center mt-5 ms-3'>
   <input class="p-2 mt-1 mb-1 w-50 form-control border-outline-dark" type="search" placeholder="Search Sales Order" id="search-sales" name="search-sales" autocomplete="off">
      <button class="btn btn-success btn-sm px-4 ms-5 float-end" onclick="prinSalesOrderTable()"><i class="bi bi-printer"></i></button>
   </div>
   <div class="container h-100 overflow-y-auto">
      <div class="mt-2 overflow-y-scroll" id="table" style="height: 400px">
         <table class="table table-warning table-hover text-center default-table table-sm table-bordered align-middle px-3">
            <thead class="table-dark">
               <tr>
                  <th>Sales Order Number</th>
                  <th>Total Amount</th>
                  <th>Date of Transaction</th>
                  <th>Cashier</th>
               </tr> 
            </thead>                      
         <tbody>
            <?php 
               //Getting the data from database
               $query = "SELECT * from sales_orders";
               $result = mysqli_query($conn, $query);
               while ($fetch = mysqli_fetch_array($result)) {
            
            ?>

            <tr >
               <td><?php echo $fetch['sales_orderNumber']?></td>
               <td><?php echo $fetch['total_amount']?></td>
               <td><?php echo $fetch['date_ofTransaction']?></td>
               <td><?php echo checkCashier($fetch['cashier_id'], $conn)?></td>
            </tr>

            <?php 
            } 
            ?>

         </tbody>
      </table>
      <div id="not-found-message" class="text-danger text-center" style="display: none; font-size:40px;  ">Data not found</div>
   </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
<script src="../js/receipt_function.js"></script>

<script>
   function prinSalesOrderTable() {
      var printWindow = window.open('_blank');
      printWindow.document.write('<html><head><title>Sales Transaction</title>');

      // Include Bootstrap CSS
      printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
      var hiddenColumns = document.getElementsByClassName('action-column');
      for (var i = 0; i < hiddenColumns.length; i++) {
         hiddenColumns[i].classList.add('d-none');
      }
      printWindow.document.write('</head><body>');
      printWindow.document.write('<h2 class="text-center"><img src="../img/companyLogo2.svg" class="w-25 text-center">Raydon Construction Trading</h2>');
      printWindow.document.write('<hr><hr>');
      printWindow.document.write('<h2 class="text-center mt-2" mb-3>Sales Transaction Report</h2>');
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
      //printWindow.print();
   }
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get the input element, table rows, and the message element
        var searchInput = document.getElementById('search-sales');
        var tableRows = document.querySelectorAll('#table tbody tr');
        var notFoundMessage = document.getElementById('not-found-message');

        // Add event listener for input change
        searchInput.addEventListener('input', function () {
            var searchTerm = searchInput.value.toLowerCase();
            var found = false;

            // Loop through each table row and hide/show based on search term
            tableRows.forEach(function (row) {
                var salesOrderNumber = row.querySelector('td:first-child').textContent.toLowerCase();

                if (salesOrderNumber.includes(searchTerm)) {
                    row.style.display = '';
                    found = true;
                } else {
                    row.style.display = 'none';
                }
            });

            // Display "Data not found" message if no matching data is found
            if (found) {
                notFoundMessage.style.display = 'none';
            } else {
                notFoundMessage.style.display = '';
            }
        });
    });
</script>
   
<?php 
   include './inc/closing.php';
?>

