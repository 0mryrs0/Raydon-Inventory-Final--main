<?php 
   $pageName = "Dashboard";
   include './inc/opening.php';
?>

   <div class= "h-100 overflow-scroll flex-column justify-content-center" id="main-content"> 
      <div class="container d-flex justify-content-center gap-1 flex-wrap mt-5">
         <div class="card bg-success dashboard-card">
            <div class="card-body text-center text-white">
               <h5 class="card-title"><i class="bi bi-gift me-2"></i>Total Products</h5>
               <?php 
                  $query = "SELECT COUNT(*) as total_rows FROM products";
                  $result = mysqli_query($conn, $query);
                  $row = mysqli_fetch_assoc($result);
                  $total_rows = $row['total_rows'];
               ?>
               <h3 class="card-text"><?php echo $total_rows; ?></h3>
            </div>
         </div>
         <div class="card bg-success dashboard-card">
            <div class="card-body text-center text-white">
               <h5 class="card-title"><i class="bi bi-truck me-2"></i>Total Suppliers</h5>
               
               <?php 
                  $query = "SELECT COUNT(*) as total_rows FROM suppliers";
                  $result = mysqli_query($conn, $query);
                  $row = mysqli_fetch_assoc($result);
                  $total_rows = $row['total_rows'];
               ?>
               <h3 class="card-text"><?php echo $total_rows; ?></h3>
            </div>
         </div>
         <div class="card bg-success dashboard-card">
            <div class="card-body text-center text-white">
               <h5 class="card-title"><i class="bi bi-cart2 me-2"></i>Total Purchase Order</h5>
               <?php 
                  $query = "SELECT COUNT(*) as total_rows FROM purchase_orders";
                  $result = mysqli_query($conn, $query);
                  $row = mysqli_fetch_assoc($result);
                  $total_rows = $row['total_rows'];
               ?>
               <h3 class="card-text"><?php echo $total_rows; ?></h3>
            </div>
         </div>
         <div class="card bg-success dashboard-card">
            <div class="card-body text-center text-white">
               <h5 class="card-title"><i class="bi bi-bar-chart me-2"></i>Total Sales Order</h5>
               <?php 
                  $query = "SELECT COUNT(*) as total_rows FROM sales_orders";
                  $result = mysqli_query($conn, $query);
                  $row = mysqli_fetch_assoc($result);
                  $total_rows = $row['total_rows'];
               ?>
               <h3 class="card-text"><?php echo $total_rows; ?></h3>
            </div>
         </div>     
      </div>
      <div class="container h-100">
         <div class="container mt-4">
            <div> <!-- Add margin top to create space -->
               <h4>Sales History</h4>
               <canvas id="salesChart"></canvas>
            </div>
         </div>
         <?php
         // Modify the SQL query to group by week
         $sql = "SELECT CONCAT(YEAR(item_dateAdded), '-', WEEK(item_dateAdded)) as week_number, SUM(item_subtotal) AS total_sales FROM sales GROUP BY week_number";
         $result = $conn->query($sql);

         $data = array();
         while ($row = $result->fetch_assoc()) {
            $data[] = $row;
         }
         ?>
      </div>
   </div>
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script>
   var ctx = document.getElementById('salesChart').getContext('2d');
   var data = <?php echo json_encode($data); ?>;

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
</script>
<?php 
   include './inc/closing.php';
?>