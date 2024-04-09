<?php
session_start(); 
    include("./config/database.php");
    include("./config/functions.php");
    include("./config/general_function.php");
    $user_data = check_login($conn);

    if (!($user_data['admin_id'])) {
      header('Location: admin.php');
      die;
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory</title>
    <link rel="stylesheet" href="css/header_sidebar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css" integrity="sha384-b6lVK+yci+bfDmaY1u0zE8YYJt0TZxLEAFyYSLHId4xoVvsrQu3INevFKo+Xir8e" crossorigin="anonymous">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="css/receipt.css">
    <link type="stylesheet" media="print" href="/css/printData.css">
</head>
<body class="h-100">
  <div class="d-flex h-100" id="wrapper">
    <!-- Sidebar -->
    <div class="sidebar-menu bg-success" id="sidebar">
      <div class="sidebar-heading ms-3 text-white py-5 fs-5 fw-bold text-uppercase bg-success"><span class="py-5 text-white" id="menu-close"><i class="fa-solid fa-circle-xmark fa-2xl"></i></span></div>
      <div class="list-group list-group-flush my-1 bg-success" id="list-link">
        <a href="dashboard.php" class="list-group-item bg-success text-white ms-3 mb-4  border border-end-0 rounded-2 rounded-end-0"><i class="me-3 fa-solid fa-gauge"></i>Dashboard</a>
        <a href="product.php" class="list-group-item bg-success text-white ms-3 mb-2  border border-end-0 rounded-2 rounded-end-0"><i class="me-3 fa-solid fa-gift"></i>Products</a>
        <a href="supplier.php" class="list-group-item bg-success text-white ms-3 mb-2  border border-end-0 rounded-2 rounded-end-0"><i class="me-3 fa-solid fa-truck-field"></i>Supplier</a>
        <a href="purchases.php" class="list-group-item bg-success text-white ms-3 mb-2  border border-end-0 rounded-2 rounded-end-0"><i class="me-3 fa-solid fa-cart-shopping"></i>Purchases</a>
        <a href="salesOrders.php" class="list-group-item bg-success text-white ms-3 mb-2  border border-end-0 rounded-2 rounded-end-0"><i class="me-3 bi bi-card-checklist"></i>Sales</a>
        <a href="reports.php" class="list-group-item bg-success text-white ms-3 mb-2  border border-end-0 rounded-2 rounded-end-0"><i class="me-3 fa-solid fa-arrow-trend-up"></i>Reports</a>
        <a href="users.php" class="list-group-item bg-success text-white ms-3 mb-2  border border-end-0 rounded-2 rounded-end-0"><i class="me-3 fa-regular fa-user"></i>Users</a>
        <a href="admin_logs.php" class="list-group-item bg-success text-white ms-3 mb-2  border border-end-0 rounded-2 rounded-end-0"><i class="me-3 fa-regular fa-user"></i>Activity Log</a>
      </div>
      <a href="/logout.php" class="list-group-item bg-dark text-white p-3 rounded-0"><i class="me-3 fa-solid fa-right-from-bracket"></i>Logout</a>   
    </div>

    <!-- Page Content -->
    <div id="page-content-wrapper">
    <nav class="navbar navbar-light bg-transparent py-2 px-4 pe-4 d-flex align-items-center">
      <div class="d-flex align-items-center">
          <i class="fa-solid fa-bars primary-text fs-3 me-5 text-warning" id="menu-toggle"></i>
          <div>
              <h3 class="m-0 text-success fw-bold sm-none"><?php echo $pageName;?></h3>
              <h6 class="fs-6 fw-light " id="date"></h6>
          </div>
      </div>
      <div class="d-flex align-item-center justify-item-center">
          <div class="dropdown me-5">
            <button class="btn btn-success dropdown-toggle" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-bell"></i>
              <?php
                  $selectQuery = "SELECT COUNT(*) as num_rows FROM notifications WHERE isClicked=0";
                  $result = mysqli_query($conn, $selectQuery);
              
                  if ($result) {
                      $row = mysqli_fetch_assoc($result);
                      $num_rows = $row['num_rows'];
                      // Use $num_rows as needed
                  } else {
                      $message = "Error: " . mysqli_error($conn);
                  } 

                  if ($num_rows != 0) {
                  ?>
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?php echo $num_rows?></span>
                  <?php
                  }
                
                
                ?>

            </button>


            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                <h4 class="text-center">Notifications</h4>
                <?php 
                $query = "SELECT * FROM notifications ORDER BY date DESC LIMIT 12";
                $result = mysqli_query($conn, $query);

                // Check if the query was successful
                if ($result) {
                    while ($notifications = mysqli_fetch_assoc($result)) {
                        $notificationClass = ($notifications['isClicked'] == 0) ? 'text-danger' : '';

                        echo '<a class="dropdown-item ' . $notificationClass . '" href="purchases.php">' . $notifications['notification'] . '</a>';
                    }
                } else {
                    // Handle the case where the query fails
                    echo "Error: " . mysqli_error($conn);
                }
                ?>
                <a class="dropdown-item text-end" href="purchases.php">View all>></a>    
            </div>
          </div>
          <div class="ms-auto rounded-2 bg-success px-3 py-2 text-white">
            <h6 class="m-auto"><i class="bi bi-person me-2"></i>Admin <span id="admin"><?php echo $user_data['first_name'] . ' ' . $user_data['last_name']?></span></h6>
          </div>
      </div>
    </nav>