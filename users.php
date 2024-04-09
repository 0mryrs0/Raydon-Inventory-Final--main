<?php 
   $pageName = "Users";
   include './inc/opening.php';
   $messageError  = $passwordError = "";
   $user_data = check_login($conn);

   $addedByAdmin = $user_data['admin_id'];

   $query = "CREATE TABLE IF NOT EXISTS`cashiers` (
      `cashier_id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(100) NOT NULL,
      `first_name` varchar(100) NOT NULL,
      `last_name` varchar(100) NOT NULL,
      `email` varchar(100) NOT NULL,
      `password` varchar(100) NOT NULL,
      `user_status` varchar(100) NOT NULL,
      `date_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      `admin_id` int(11) NOT NULL,
      
      PRIMARY KEY (`cashier_id`) 
  )";
  
  mysqli_query($conn, $query);
   
?>

   <div class= "container-fluid p-4 d-flex-column" id="main-content">
            <div class="d-flex justify-content-between">
                  <div class="w-100">
                     <button type="button" class="btn btn-danger add-btn px-4 float-end" data-bs-toggle="modal" data-bs-target="#addUserModal" tabindex="-1" >
                        Add user
                     </button>
                  </div>

                  <!-- Modal for Adding a User-->
                  <div class="modal fade modal-lg p-5" id="addUserModal" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
                     <div class="modal-dialog d-flex justify-content-center p-4">
                        <div class="modal-content" id="modal-form">
                           <div class="modal-header text-center">
                              <h4 class="modal-title textMaroon fw-bold" id="modal-title">Add Another User</h4>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="'close"></button>
                           </div>
                           <div class="modal-body">
                              <div class="alert alert-warning d-none" id="errorMessage"></div>
                           <form id="register-user" >
                                 <div class="row">
                                       <div class="col">
                                          <div class="form-group mb-1">
                                             <label class="w-50 label">First Name </label>
                                             <input class="w-100 px-2 py-1" type="text" name="firstname">
                                          </div>
                                          <div class="form-group mb-1">
                                             <label class="w-50 label">Last name</label>
                                             <input class="w-100 px-2 py-1" type="text" name="lastname">
                                          </div>
                                          <div class="form-group mb-1">
                                             <label class="w-50 label">Email</label>
                                             <input class="w-100 px-2 py-1" type="email" name="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}">
                                          </div>
                                          <div class="form-group mb-1">
                                             <label class="w-50 label">Username </label>
                                             <input class="w-100 px-2 py-1" type="text" name="username">
                                          </div>
                                       </div>
                                       <div class="col">
                                          <div class="form-group mb-1">
                                             <label class="w-50 label">Password</label>
                                             <input class="w-100 px-2 py-1" type="password" name="password">
                                          </div>
                                          <div class="form-group mb-1">
                                             <label class="w-75 label">Confirm Password</label>
                                             <input class="w-100 px-2 py-1" type="password" name="confirmPassword"> 
                                             
                                          </div>
                                          <div class="form-group mb-1">
                                              <label class="w-75 label">Account Type:</label>
                                             <input class="w-100 px-2 py-1" type="text" name="account-type" value="Cashier" readonly='true'> 
                                          </div>
                                       </div>               
                                 </div>
                                 <input type="hidden" value="<?php echo $addedByAdmin; ?>" name="added-by-admin">
                                 <div class="row form-down text-center">
                                       <button type="submit" class="add-btn rounded-4 py-2 w-25 mx-auto mt-5" name="register">REGISTER</button>
                                 </div>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
            </div>
            <div id="tables">
               <div class="table-responsive-md pt-3">
                     <!-- Admin Table -->
                     <h5 class="fw-bold text-success">ADMIN</h5>
                     <table class="table table-warning text-center">
                        <thead class="table-dark">
                           <tr>
                                 <th>Admin Id</th>
                                 <th>Username</th>
                                 <th>Name</th>
                                 <th>Email</th>
                           </tr> 
                        </thead>
                        <tbody>
                           <?php 
                           //Getting the admin's data from databases
                           $query = "SELECT * FROM admins";
                           $result = mysqli_query($conn, $query);
                           while ($fetch = mysqli_fetch_array($result)) {

                           ?>
                           <tr>
                              <!-- //Displayng admin's data -->
                              <td><?php echo $fetch['admin_id']?></td>
                              <td><?php echo $fetch['username']?></td>
                              <td><?php echo "{$fetch['first_name']} {$fetch['last_name']}"?></td>
                              <td><?php echo $fetch['email']?></td>
                           </tr>

                           <?php }?>
                        </tbody>
                     </table>                 
               </div> 
               <div class="table-responsive-md pt-3">
                     <!-- Cashier Table -->
                     <h5 class="fw-bold text-success">CASHIERS</h5>
                     <table class="table table-warning text-center table-sm" id="table2">
                        <thead class="table-dark">
                           <tr>
                                 <th>Cashier Id</th>
                                 <th>Username</th>
                                 <th>Name</th>
                                 <th>Email</th>
                                 <th>Status</th>
                                 <th>Action</th>
                           </tr> 
                        </thead>
                        <tbody>
                           <?php 
                           $account_type = "";
                           //Getting the from cashier's database
                           $query = "SELECT * FROM cashiers";
                           $result = mysqli_query($conn, $query);
                           while ($fetch = mysqli_fetch_array($result)) {

                           ?>
                                 <!-- Displaying cashier's data in table form -->
                                 <tr>
                                 <td> <?php echo $fetch['cashier_id']?></td>
                                 <td> <?php echo $fetch['username'] ?></td>
                                 <td> <?php echo $fetch['first_name'] . ' ' . $fetch['last_name'] ?></td>
                                 <td> <?php echo $fetch['email'] ?></td>
                                 <td> <?php echo $fetch['user_status'] ?></td>
                                 <td>
                                 <?php 
                                    if($fetch['user_status'] == "Active") {
                                 ?>
                                       <button type='button' value='<?php echo $fetch['cashier_id'] ?>' class='btn btn-danger btn-sm deactivate-btn'>
                                         DEACTIVATE
                                       </button>
                                 <?php
                                    } else {
                                 ?>
                                       <button type='button' value='<?php echo $fetch['cashier_id'] ?>' id="change-status-btn" class='btn btn-success btn-sm activate-btn'>
                                         ACTIVATE
                                       </button>
                                 <?php
                                    }
                                 ?>

                                 </td>
                                 </tr>
                                 
                           <?php } ?>
                           
                        </tbody>
                     </table>                 
               </div> 
            </div>  
   </div>

   </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   //Register users
   $(document).on('submit', '#register-user' , function(e) {
      e.preventDefault();

      var formData = new FormData(this);
      formData.append('register_user', true);

      $.ajax ( {
         type: 'POST',
         url: 'usersCode.php',
         data: formData,
         processData: false,
         contentType: false,
         success:function(response) {
            var res = jQuery.parseJSON(response);
            if(res.status == 422) {
               $('#errorMessage').removeClass('d-none');
               $('#errorMessage').text(res.message);
            } else if(res.status == 200) {
               $('#errorMessage').addClass('d-none');
               $('#addUserModal').modal('hide');
               $('#register-user')[0].reset();
               $('#tables').load(location.href + ' #tables');
            }
         }
      }
      )
   });

   //Deleting Admin
   $(document).on('click', '.deleteAdminBtn', function (e) {
               e.preventDefault();

               if(confirm('Are you sure you want to delete this admin?'))
               {
                  var admin_id = $(this).val();
                  $.ajax({
                     type: "POST",
                     url: "usersCode.php",
                     data: {
                           'delete_admin': true,
                           'admin_id': admin_id
                     },
                     success: function (response) {

                           var res = jQuery.parseJSON(response);
                           if(res.status == 500) {

                              alert(res.message);
                           }else
                           {
                              alert(res.message);
                              $('#tables').load(location.href + " #tables");
                           }
                     }
                  });
               }
         });

   //Deleting cashier
   $(document).on('click', '.deactivate-btn', function (e) {
    e.preventDefault();
    var button = $(this);
    console.log(button);
    var cashier_id =$(this).val();

    if (confirm('Are you sure you want to deactivate this user?')) {
        $.ajax({
            type: "POST",
            url: "usersCode.php",
            data: {
                'deactivate_cashier': true,
                'cashier_id': cashier_id
            },
            success: function (response) {
                var res = jQuery.parseJSON(response);

                if (res.status == 500) {
                    alert(res.message);
                } else {
                    alert(res.message);
                    $('#tables').load(location.href + " #tables");
                }
            }
        });
    }
});

$(document).on('click', '.activate-btn', function (e) {
    e.preventDefault();
    var cashier_id =$(this).val();

    if (confirm('Are you sure you want to activate this user?')) {
        $.ajax({
            type: "POST",
            url: "usersCode.php",
            data: {
                'activate_cashier': true,
                'cashier_id': cashier_id
            },
            success: function (response) {
                var res = jQuery.parseJSON(response);

                if (res.status == 500) {
                    alert(res.message);
                } else {
                    alert(res.message);
                    $('#tables').load(location.href + " #tables");
                }
            }
        });
    }
});
</script>
<?php include './inc/closing.php';?>