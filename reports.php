<?php 
   $pageName = "Reports";
   include './inc/opening.php';
?>

<div class= "container-fluid p-4 d-flex-column h-100 overflow-y-auto" id="main-content">
   <div class="w-100 d-flex justify-content-between align-items-center mt-5">
        <h4>Sales Chart</h4> 
        <button class="btn btn-outline-success mt-3 btn-sm" onclick="printCharts()">Print Charts</button>
   </div>
   
   <div class="container mt-4">
    <!-- three bar charts that shows (daily,weekly, and monthly) sales stack in columns (3)-->
      <div class="row">
         <div class="col">
               <h5>Daily Sales</h5>
               <canvas id="dailySalesChart"></canvas>
         </div>
         <?php
         // Modify the SQL query to group by day
         $sql = "SELECT DATE(item_dateAdded) as sale_date, SUM(item_subtotal) AS total_sales FROM sales GROUP BY sale_date";
         $result = $conn->query($sql);

         $dailydata = array();
         while ($row = $result->fetch_assoc()) {
            $dailydata[] = $row;
         }
         ?>
         <div class="col">
               <h5>Weekly Sales</h5>
               <canvas id="weeklySalesChart"></canvas>
         </div>
         <?php
         // Modify the SQL query to group by week
         $sql = "SELECT WEEK(item_dateAdded) as week_number, SUM(item_subtotal) AS total_sales FROM sales GROUP BY week_number";
         $result = $conn->query($sql);

         $weeklydata = array();
         while ($row = $result->fetch_assoc()) {
            $weeklydata[] = $row;
         }
         ?>

      </div>
      <div class="row">
        <div class="col">
                <h5>Monthly Sales</h5>
                <canvas id="monthlySalesChart"></canvas>
            </div>
            <?php
            // Modify the SQL query to group by month
            $sql = "SELECT DATE_FORMAT(item_dateAdded, '%Y-%m') as sale_month, SUM(item_subtotal) AS total_sales FROM sales GROUP BY sale_month";
            $result = $conn->query($sql);

            $monthlydata = array();
            while ($row = $result->fetch_assoc()) {
                $monthlydata[] = $row;
            }
            ?>
            <div class="col">
                <h5>Sales Per Category</h5>
                <canvas id="categorySalesChart"></canvas>
            </div>
            <?php
                // Modify the SQL query to get sales per category
                $sql = "SELECT p.category, SUM(s.item_subtotal) AS total_sales
                        FROM sales s
                        JOIN products p ON s.product_id = p.product_id
                        GROUP BY p.category";
                $result = $conn->query($sql);

                $categorydata = array();
                while ($row = $result->fetch_assoc()) {
                    $categorydata[] = $row;
                }
            ?>
      </div>
   </div>

    <div>
        <div class="modal fade" id="salesPerCategoryModal" tabindex="-1" aria-labelledby="salesPerCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesPerCategoryModalLabel">Sales Per Product Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Content of the modal goes here -->
                    <!-- You can add the pie chart here -->
                    <canvas id="salesPieChart"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    </div>
        <h4 class="mt-5">All Sales</h4>
        <button class="btn btn-success btn-sm px-4 ms-5 float-end ms-3 mb-3" onclick="prinSalesTable()"><i class="bi bi-printer" data-bs-toggle="tooltip" title="print here"></i></button>
        
        <hr>
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">From:</span>
            <input type="date" class="form-control" id="startDate" aria-label="Date" aria-describedby="basic-addon1">
            <span class="input-group-text" id="basic-addon2">To:</span>
            <input type="date" class="form-control" id="endDate" aria-label="Date" aria-describedby="basic-addon2">
            <button id="submitDates" class="btn btn-outline-success">Submit</button>
        </div>
        <div class="alert alert-warning d-none" id="errorMessage_update"></div>
        <div class="container h-100 overflow-y-auto" id="table-container">
            <div class="mt-1 overflow-y-auto" id="table" style="height: 400px">
                <table class="table table-warning table-hover text-center default-table table-sm table-bordered align-middle px-3">
                    <thead class="table-dark">
                    <tr>
                        <th>Item Number</th>
                        <th>Item Name</th>
                        <th>Item Quantity</th>
                        <th>Item Subtotal</th>
                        <th>Date Added</th>
                        <th>Sales Order Number</th>
                    </tr> 
                    </thead>              
                    <tbody>
                    <?php 
                        //Getting the data from database
                        $query = "SELECT * from sales";
                        $result = mysqli_query($conn, $query);
                        while ($fetch = mysqli_fetch_array($result)) {
                    
                    ?>
                    <tr>
                        <td><?php echo $fetch['item_no']?></td>
                        <td><?php echo $fetch['item_name']?></td>
                        <td><?php echo $fetch['item_quantity']?></td>
                        <td><?php echo $fetch['item_subtotal']?></td>
                        <td><?php echo $fetch['item_dateAdded']?></td>
                        <td><?php echo $fetch['sales_orderNumber']?></td>
                    </tr>

                    <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(document).ready(function() {
    $('#submitDates').click(function() {
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();

        $.ajax({
            type: 'POST',
            url: 'reportsCode.php',
            data: {
                reportFromTo: true,
                startDate: startDate,
                endDate: endDate
            },
            success: function(response) {
                var res = jQuery.parseJSON(response);
                if (res.status === 200) {
                    // Hide the default table
                    $('.default-table').hide();

                    $('.default-table').html('');
                    $('#errorMessage_update').addClass('d-none');

                    // Create a new table structure
                    var newTable = '<div class="mt-1 overflow-y-scroll" id="table" style="height: 400px">' +
                        '<table class="table table-warning table-hover text-center default-table table-sm table-bordered align-middle px-3">' +
                        '<thead class="table-dark">' +
                        '<tr>' +
                        '<th>Item Number</th>' +
                        '<th>Item Name</th>' +
                        '<th>Item Quantity</th>' +
                        '<th>Item Subtotal</th>' +
                        '<th>Date Added</th>' +
                        '<th>Sales Order Number</th>' +
                        '</tr>' +
                        '</thead>' +
                        '<tbody class="default-table">';

                    // Loop through res.data and create rows in the new table
                    $.each(res.data, function(index, item) {
                        newTable += '<tr>' +
                            '<td>' + item.item_no + '</td>' +
                            '<td>' + item.item_name + '</td>' +
                            '<td>' + item.item_quantity + '</td>' +
                            '<td>' + item.item_subtotal + '</td>' +
                            '<td>' + item.item_dateAdded + '</td>' +
                            '<td>' + item.sales_orderNumber + '</td>' +
                            '</tr>';
                    });

                    // Close the new table structure
                    newTable += '</tbody></table></div>';

                    // Append the new table to the container
                    $('#table-container').prepend(newTable);
                } else {
                    // Handle the error
                    console.error(res.message);
                    $('#errorMessage_update').removeClass('d-none');
                    $('#errorMessage_update').text(res.message);
                }
            },
            error: function(error) {
                // Handle other errors if any
                console.error(error);
            }
        });
    });
});

$(document).ready (function() {
   var ctx = document.getElementById('weeklySalesChart').getContext('2d');
   var data = <?php echo json_encode($weeklydata); ?>;

   var labels = data.map(function(item) {
      return 'Week ' + item.week_number;
   });

   var values = data.map(function(item) {
      return item.total_sales;
   });

   var salesChart = new Chart(ctx, {
      type: 'bar',
      data: {
         labels: labels,
         datasets: [{
               label: 'Total Sales',
               data: values,
               backgroundColor: [
                  'rgb(2, 123, 58)',
                  'rgb(139, 48, 49)',
                  'rgb(238, 214, 71)'
               ],
               borderColor: [
                  'rgb(2, 123, 58)',
                  'rgb(139, 48, 49)',
                  'rgb(238, 214, 71)'
               ],
               borderWidth: 1
         }]
      },
      options: {
         scales: {
               y: {
                  beginAtZero: true
               }
         },
         plugins: {
               legend: {
                  display: false
               }
         },
         layout: {
               padding: {
                  bottom: 20
               }
         }
      }
   });


   var ctx = document.getElementById('dailySalesChart').getContext('2d');
    var data = <?php echo json_encode($dailydata); ?>;

    var labels = data.map(function(item) {
        return item.sale_date;
    });

    var values = data.map(function(item) {
        return item.total_sales;
    });

    var salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Sales',
                data: values,
                backgroundColor: [
                    'rgb(2, 123, 58)',
                    'rgb(139, 48, 49)',
                    'rgb(238, 214, 71)'
                ],
                borderColor: [
                    'rgb(2, 123, 58)',
                    'rgb(139, 48, 49)',
                    'rgb(238, 214, 71)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            layout: {
                padding: {
                    bottom: 20
                }
            }
        }
    });


    var ctx = document.getElementById('monthlySalesChart').getContext('2d');
    var data = <?php echo json_encode($monthlydata); ?>;

    var labels = data.map(function(item) {
        return item.sale_month;
    });

    var values = data.map(function(item) {
        return item.total_sales;
    });

    var salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Sales',
                data: values,
                backgroundColor: [
                    'rgb(2, 123, 58)',
                    'rgb(139, 48, 49)',
                    'rgb(238, 214, 71)'
                ],
                borderColor: [
                    'rgb(2, 123, 58)',
                    'rgb(139, 48, 49)',
                    'rgb(238, 214, 71)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            layout: {
                padding: {
                    bottom: 20
                }
            }
        }
    });


    var ctx = document.getElementById('categorySalesChart').getContext('2d');
    var data = <?php echo json_encode($categorydata); ?>;

    var labels = data.map(function(item) {
        return item.category;
    });

    var values = data.map(function(item) {
        return item.total_sales;
    });

    var salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Sales',
                data: values,
                backgroundColor: [
                    'rgb(2, 123, 58)',
                    'rgb(139, 48, 49)',
                    'rgb(238, 214, 71)'
                ],
                borderColor: [
                    'rgb(2, 123, 58)',
                    'rgb(139, 48, 49)',
                    'rgb(238, 214, 71)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            },
            layout: {
                padding: {
                    bottom: 20
                }
            }
        }
    });

})


function printCharts() {
        // Print daily sales chart
        var dailyCanvas = document.getElementById('dailySalesChart');
        var dailyDataUrl = dailyCanvas.toDataURL('image/png');

        // Print weekly sales chart
        var weeklyCanvas = document.getElementById('weeklySalesChart');
        var weeklyDataUrl = weeklyCanvas.toDataURL('image/png');

        // Print monthly sales chart
        var monthlyCanvas = document.getElementById('monthlySalesChart');
        var monthlyDataUrl = monthlyCanvas.toDataURL('image/png');

        // Print category sales chart
        var categoryCanvas = document.getElementById('categorySalesChart');
        var categoryDataUrl = categoryCanvas.toDataURL('image/png');

        // Create a new window for printing
        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Sales Charts</title>');
        printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">');
        printWindow.document.write('</head><body class="p-4">');
        printWindow.document.write('<h2 class="text-center"><img src="../img/companyLogo2.svg" class="w-25 text-center">Raydon Construction Trading</h2>');
        printWindow.document.write('<hr><hr>');
        printWindow.document.write('<h2 class="text-center mb-2">Sales History Charts</h2>');

        // Create Bootstrap grid layout
        printWindow.document.write('<div class="row">');

        // Append images to the first column
        printWindow.document.write('<div class="col-md-6 mb-4">');
        printWindow.document.write('<h4 class="text-center">Daily Sales</h4><img src="' + dailyDataUrl + '" class="img-fluid">');
        printWindow.document.write('<h4 class="text-center mt-2">Weekly Sales</h4><img src="' + weeklyDataUrl + '" class="img-fluid">');
        printWindow.document.write('</div>');

        // Append images to the second column
        printWindow.document.write('<div class="col-md-6 mb-4">');
        printWindow.document.write('<h4 class="text-center">Monthly Sales</h4><img src="' + monthlyDataUrl + '" class="img-fluid">');
        printWindow.document.write('<h4 class="text-center mt-2">Sales Per Category</h4><img src="' + categoryDataUrl + '" class="img-fluid">');
        printWindow.document.write('</div>');

        var adminElement = document.getElementById('admin');
        var dateElement = document.getElementById('date');

        // Include the HTML content of adminElement
        printWindow.document.write(`<h6 class="text-center mt-3">Prepared by: ${adminElement ? adminElement.innerHTML : ''}</h6>`);
        var currentDate = new Date();
        var formattedDate = `${currentDate.toLocaleString('default', { month: 'long' })} ${currentDate.getDate()}, ${currentDate.getFullYear()}`;
        printWindow.document.write(`<br><h6 class="fs-6 fw-light" id="date">Prepared on: ${formattedDate}</h6>`);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
}


function prinSalesTable() {
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
   printWindow.document.write('<h2 class="text-center mt-2" mb-3>Sales Report</h2>');
   printWindow.document.write('<div class="container-fluid fs-4">');
   printWindow.document.write(document.getElementById('table').innerHTML);
   printWindow.document.write('</div>');

   var adminElement = document.getElementById('admin');
   var dateElement = document.getElementById('date');

   // Include the HTML content of adminElement
   printWindow.document.write(`<h6 class="text-center mt-3">Prepared by: ${adminElement ? adminElement.innerHTML : ''}</h6>`);
   var currentDate = new Date();
   var formattedDate = `${currentDate.toLocaleString('default', { month: 'long' })} ${currentDate.getDate()}, ${currentDate.getFullYear()}`;
   printWindow.document.write(`<br><h6 class="fs-6 fw-light text-center" id="date">Prepared on: ${formattedDate}</h6>`);
   printWindow.document.write('</body></html>');
   printWindow.document.close();
   //printWindow.print();
}




</script>
<?php 
   include './inc/closing.php';
?>