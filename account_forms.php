 <!-- This is for forms-->
    <section class="my-5 py-5">
        <div class="container">
            <!-- Centered Account Info Section -->
            <div class="text-center mb-5">
                <!-- Success/Error Messages -->
                <?php if(isset($_GET['register_success'])): ?>
                    <p class="text-center text-success"><?php echo htmlspecialchars($_GET['register_success']); ?></p>
                <?php endif; ?>
                
                <?php if(isset($_GET['login_success'])): ?>
                    <p class="text-center text-success"><?php echo htmlspecialchars($_GET['login_success']); ?></p>
                <?php endif; ?>
                
                <?php if(isset($_GET['error'])): ?>
                    <p class="text-center text-danger"><?php echo htmlspecialchars($_GET['error']); ?></p>
                <?php endif; ?>
                
                <?php if(isset($_GET['message'])): ?>
                    <p class="text-center text-success"><?php echo htmlspecialchars($_GET['message']); ?></p>
                <?php endif; ?>

                <h3 class="font-weight-bold">Account Info</h3>
                <hr class="mx-auto" style="width: 200px;">
                
                <!-- Profile Image Upload Form -->
                <div class="profile-image-container mb-4">
                    <form id="profile-form" action="account.php" method="POST" enctype="multipart/form-data" class="d-flex justify-content-center">
                        <div class="profile-image-wrapper">
                            <img src="assets/imgs/profiles/<?php echo htmlspecialchars($profile_image); ?>" 
                                alt="Profile" 
                                class="rounded-circle mx-auto d-block"
                                style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="upload-overlay d-flex justify-content-center align-items-center">
                                <label for="profile_image" class="btn btn-sm btn-dark rounded-circle p-2 m-0">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" 
                                    id="profile_image" 
                                    name="profile_image" 
                                    accept=".jpg, .jpeg, .png" 
                                    style="display: none;">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="account-info mb-5">
                    <p>Customer Name: <?php echo htmlspecialchars($_SESSION['user_name']);?></p>
                    <p>Customer Email: <?php echo htmlspecialchars($_SESSION['user_email']);?></p>
                    <p><a href="#orders" id="orders-btn">Your Orders</a></p>
                    <p><a href="account.php?logout=1" id="logout-btn">Logout</a></p>
                </div>
            </div>

            <!-- Two Forms Side by Side -->
            <div class="row">
                <!-- Update Profile Form -->
                <!-- Replace the existing Update Profile form with these two separate forms -->
    <div class="col-md-6 mb-4">
        <div class="card p-4">
            <h4 class="text-center mb-4">Update Profile</h4>
            
            <!-- Name Update Form -->
            <form id="name-form" method="POST" action="account.php" class="mb-4">
                <div class="form-group mb-3">
                    <label>Update Customer Name: </label>
                    <input type="text" 
                        class="form-control" 
                        id="account-name" 
                        name="name" 
                        required>
                </div>
                <div class="form-group text-center">
                    <input type="submit" 
                        value="Update Name" 
                        name="update_name" 
                        class="update-name-btn">
                </div>
            </form>

            <!-- Email Update Form -->
            <form id="email-form" method="POST" action="account.php">
                <div class="form-group mb-3">
                    <label>Update Email:</label>
                    <input type="email" 
                        class="form-control" 
                        id="account-email" 
                        name="email" 
                        required>
                </div>
                <div class="form-group text-center">
                    <input type="submit" 
                        value="Update Email" 
                        name="update_email" 
                        class="update-email-btn">
                </div>
            </form>
        </div>
    </div>

                <!-- Change Password Form -->
                <div class="col-md-6 mb-4">
                    <div class="card p-4">
                        <form id="account-form" method="POST" action="account.php">
                            <h4 class="text-center mb-4">Change Password</h4>
                            <div class="form-group mb-3">
                                <label>Password</label>
                                <input type="password" class="form-control" id="account-password" name="password" placeholder="Password" required />
                            </div>
                            <div class="form-group mb-3">
                                <label>Confirm Password</label>
                                <input type="password" class="form-control" id="account-password-confirm" name="confirmPassword" placeholder="Confirm Password" required />
                            </div>
                            <div class="form-group text-center">
                                <input type="submit" value="Change Password" name="change_password" id="change-pass-btn" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Orders Section -->
    <section id="orders" class="orders container my-5 py-5">
        <div class="container mt-2">
            <h2 class="font-weight-bold text-center">Your Orders</h2>
            <hr class="mx-auto">
        </div>

        <table class="mt-5 pt-5">
            <tr>
                <th>Order ID</th>
                <th>Order Cost</th>
                <th>Order Status</th>
                <th>Order Date</th>
                <th>Order Details</th>
            </tr>

            <?php if($orders->num_rows > 0): ?>
                <?php while($row = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                        <td>$<?php echo htmlspecialchars($row['order_cost']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td>
                            <form method="POST" action="order_details.php">
                                <input type="hidden" value="<?php echo htmlspecialchars($row['order_status']); ?>" name="order_status"/>
                                <input type="hidden" value="<?php echo htmlspecialchars($row['order_id']); ?>" name="order_id"/>
                                <input class="details-btn" name="order_details_btn" type="submit" value="Details"/>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">You don't have any orders yet</td>
                </tr>
            <?php endif; ?>
        </table>
    </section>
