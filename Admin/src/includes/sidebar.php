<?php
$current_page = basename($_SERVER['PHP_SELF']);
// Fetch the logged-in admin's details



?>
<!-- sidebar.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<!-- Sidebar CSS -->

<style><?php include("sidebar.css"); ?></style>

<link rel="stylesheet" href="sidebar.css">

<!-- Sidebar HTML -->
<div class="sidebar">
    <div class="sidebar-header">
        <h3>Admin</h3>
<!-- 
        <?php echo htmlspecialchars($username); ?>

        -->

    </div>
    <ul class="sidebar-menu">
        <!-- Other Menu Items -->
 
        <li>
            <a class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="../pages/dashboard.php">
                <i class="fas fa-th"></i>
                <span>Dashboard</span>
            </a>
        </li>
                <!-- Other Menu Items -->
          

        
        <li>
            <a class="<?php echo ($current_page == 'ordertotal.php') ? 'active' : ''; ?>" href="../pages/ordertotal.php">
            <i class="fa-solid fa-cart-shopping"></i>
                <span> ordertotal.php</span>
            </a>
        </li>

        <li>
            <a class="<?php echo ($current_page == 'model.php') ? 'active' : ''; ?>" href="../pages/model.php">
            <i class="fa-sharp fa-solid fa-car"></i>
                <span> Model</span>
            </a>
        </li>

        <li>
            <a class="<?php echo ($current_page == 'Customer Reviews.php') ? 'active' : ''; ?>" href="../pages/Customer Reviews.php">
            <i class="fas fa-comment-dots"></i>
                <span>Reviews</span>
            </a>
        </li>
<!--
<hr style="  height: 1px;
            background-color: aliceblue; 
            border: none; ">

-->
        <li>
            <a class="<?php echo ($current_page == 'tesing.php') ? 'active' : ''; ?>" href="../pages/tesing.php">
                <span> tesiing</span>
            </a>
        </li>

        


     
              
    

                        <!-- reprot -->

                        <li class="has-submenu <?php echo ($current_page == 'register.php' || $current_page == 'login.php') ? 'active' : ''; ?>">
               
               
                        <a href="#" class="<?php echo (
                            $current_page == 'aaaaaaaaa.php' ||
                            $current_page =='a.php' || 
                            $current_page =='aa.php'  || 
                            $current_page =='aaa.php'  || 
                            $current_page =='aaaa.php' ||
                            $current_page =='aaaaaa.php' ||
                            $current_page =='aaaaaaa.php' ||
                            $current_page =='aaaaaaaa.php' || 
                             
                            $current_page =='aaaaa.php'  ) ? 'active' : ''; ?>">
              
                <i class="fas fa-chart-pie"></i> <span>reprot</span>
                <i class="fas fa-chevron-right" style="margin-left:auto;"></i>
            </a>
            <ul class="sidebar-submenu">
                <li>
                    <a class="<?php echo ($current_page == 'aaaaaaaaa.php') ? 'active' : ''; ?>" href="../pages/aaaaaaaaa.php">
                    <i class="fas fa-chart-line"></i> <!-- for analytics -->
                    <span>ក្រាប</span>
                    </a>
                </li>

                <li>
                    <a class="<?php echo ($current_page == 'a.php') ? 'active' : ''; ?>" href="../pages/a.php">
                    <span>Top-Selling Car Models</span>
                    <i class="fas fa-sort-amount-down"></i> 
                    </a>
                </li>

                <li>
                    <a class="<?php echo ($current_page == 'aaaa.php') ? 'active' : ''; ?>" href="../pages/aaaa.php">
                        <span>Top Selling Car Makers</span>
                    </a>
                </li>
                
                <li>
                    <a class="<?php echo ($current_page == 'aa.php') ? 'active' : ''; ?>" href="../pages/aa.php">
                        <span>Top Buyers</span>
                    </a>
                </li>
                <li>
                    <a class="<?php echo ($current_page == 'aaa.php') ? 'active' : ''; ?>" href="../pages/aaa.php">
                        <span>Top Buyers</span>
                    </a>
                </li>
          
                <li>
                    <a class="<?php echo ($current_page == 'aaaaa.php') ? 'active' : ''; ?>" href="../pages/aaaaa.php">
                        <span>Daily Car Sales</span>
                    </a>
                </li>
                <li>
                    <a class="<?php echo ($current_page == 'aaaaaa.php') ? 'active' : ''; ?>" href="../pages/aaaaaa.php">
                        <span>Monthly Car Sales</span>
                    </a>
                </li>
                <li>
                    <a class="<?php echo ($current_page == 'aaaaaaa.php') ? 'active' : ''; ?>" href="../pages/aaaaaaa.php">
                        <span>Monthly Revenue</span>
                    </a>
                </li>
                <li>
                    <a class="<?php echo ($current_page == 'aaaaaaaa.php') ? 'active' : ''; ?>" href="../pages/aaaaaaaa.php">
                        <span>Daily Revenue Chart</span>
                    </a>
                </li>
            </ul>
        </li>








        


        
        

       

                <!-- add -->

                <li class="has-submenu <?php echo ($current_page == 'register.php' || $current_page == 'login.php') ? 'active' : ''; ?>">
               
               

               
                <a href="#" class="<?php echo ($current_page == 'add_model.php' || $current_page == 'add_car_maker.php') ? 'active' : ''; ?>">
                <i class="fas fa-plus addmore"></i>
                <span>add car</span>
                <i class="fas fa-chevron-right" style="margin-left:auto;"></i>
            </a>
            <ul class="sidebar-submenu">
                <li>
                    <a class="<?php echo ($current_page == 'add_model.php') ? 'active' : ''; ?>" href="../pages/add_model.php">
                   

                        <span>model</span>
                    </a>
                </li>
    
                <li>
            <a class="<?php echo ($current_page == 'add_car_maker.php') ? 'active' : ''; ?>" href="../pages/add_car_maker.php">
             
                <span>maker</span>
            </a>
        </li>
            </ul>
        </li>






                 <!-- Parent Menu Item for Register/Login -->
                 <li class="has-submenu <?php echo ($current_page == 'register.php' || $current_page == 'login.php') ? 'active' : ''; ?>">
           

                <a href="#" class="<?php echo ($current_page == 'view_models_action.php'  || $current_page == 'view_car_makers_action.php') ? 'active' : ''; ?>">
          <i class="fa-solid fa-marker"></i>

                <span>view&edit</span>
                <i class="fas fa-chevron-right" style="margin-left:auto;"></i>
            </a>
            <ul class="sidebar-submenu">
            <li>
                    <a class="<?php echo ($current_page == 'view_models_action.php') ? 'active' : ''; ?>" href="../pages/view_models_action.php">

                        <span>models</span>
                    </a>
                </li>


                <li>
                <a class="<?php echo ($current_page == 'view_car_makers_action.php') ? 'active' : ''; ?>" href="../pages/view_car_makers_action.php">
             
             <span>makers</span>
                    </a>
                </li>

            
            </ul>
        </li>




        <li>
            <a class="<?php echo ($current_page == 'manage_users.php') ? 'active' : ''; ?>" href="../pages/manage_users.php">
                <i class="fas fa-users-cog"></i>
                <span>Manage Admin</span>
            </a>
        </li>


        <li>
            <a class="<?php echo ($current_page == 'pending_registrations.php') ? 'active' : ''; ?>" href="../pages/pending_registrations.php">
                <i class="fas fa-clock"></i>
                <span>Pending </span>
            </a>
        </li>





        <!-- Parent Menu Item for Register/Login -->

        <li>
                    <a class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>" href="#">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>setting</span>
                    </a>
                </li>








        <li>
                    <a class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>" href="../pages/logout.php">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>






    </ul>
</div>
