<!DOCTYPE html>
<html lang="en">
<head>



    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <!--Poppins-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
   <!-- Navbar -->
   <nav class="navbar navbar-expand-lg navbar-light bg-body-tertiary justify-content-between fixed-top py-3">
       <div class="container">
           <img class="logo" src="assets/imgs/logo.png" alt="Logo" style="height: 40px;"/> <!-- Add height for better alignment -->
           <h2 class ="brand">Anime and Game Merchandise Shop</h2>
           <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
           </button>
           <div class="collapse navbar-collapse" id="navbarSupportedContent">
               <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                   <li class="nav-buttons">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                   <li class="nav-buttons">
                       <a class="nav-link" href="shop.php">Shop</a>
                   </li>
                   <li class="nav-buttons">
                       <a class="nav-link" href="#">Blog</a>
                   </li>
                   <li class="nav-buttons">
                       <a class="nav-link" href="contact.php">Contact</a>
                   </li>
               </ul>
               <div class="d-flex">
                   <!-- Shopping Bag Icon -->
                   <a href="cart.php" class="nav-link">
                       <i class="fas fa-shopping-bag"></i>
                   </a>
                   <!-- User Icon -->
                   <a href="account.php" class="nav-link">
                       <i class="fas fa-user"></i>
                   </a>
               </div>
           </div>
       </div>
   </nav>
