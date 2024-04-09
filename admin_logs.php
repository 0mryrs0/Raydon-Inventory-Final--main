<?php 
   $pageName = "Admin Activity Log";
   include './inc/opening.php';

?>

<div class= "h-100  flex-column justify-content-center" id="main-content">
    <div class="container h-100 overflow-y-auto">
        <h4 class="fw-bold mt-5 ms-2 text-success ">Activity List</h4>
        <div class=" overflow-y-scroll" id="table" style="height: 400px">
            <table class="table table-warning table-hover text-center default-table table-sm table-bordered align-middle px-3">
            <thead class="table-dark">
                <tr>
                    <th>Log Id</th>
                    <th>Activity</th>
                    <th>Category</th>
                    <th>Date</th>
                </tr> 
            </thead>                   
            <tbody>
                <?php 
                    //Getting the data from database
                    $query = "SELECT * from logs ORDER BY date_OfActivity DESC";
                    $result = mysqli_query($conn, $query);
                    while ($fetch = mysqli_fetch_array($result)) {
                
                ?>

                <tr >
                    <td><?php echo $fetch['log_id']?></td>
                    <td><?php echo $fetch['activity']?></td>
                    <td><?php echo $fetch['relation']?></td>
                    <td><?php echo $fetch['date_OfActivity']?></td>
                </tr>

                <?php 
                } 
                ?>

            </tbody>
            </table>
        </div>
    </div>

</div>



<?php 
   include './inc/closing.php';
?>