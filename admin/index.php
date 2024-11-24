<?php
session_start();

// Check if admin is not logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page
    header('location: login.php?error=Please login first');
    exit;
}

include('../server/connection.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css"> <!-- Link to external CSS -->
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <?php include('sidemenu.php'); ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include('header.php'); ?>
        </div>
    </div>
</body>
</html>
