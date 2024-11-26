<?php include('layouts/header.php'); ?>


<!--Home-->
<section id="home" class="my-5 py-5">
    <div class="container">
        <h4>NEW ARRIVALS</h4>
        <h1><span>BEST PRICES</span> THIS SEASON </h1>
        <a href="shop.php" class="text-uppercase">SHOP NOW</a>
    </div>
</section>
 

<!--Brand-->
<section id = "brand" class ="container">
    <div class="row">
        <img class = "img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/banner1.png"/>
        <img class = "img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/banner2.png"/>
        <img class = "img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/banner1.png"/>
        <img class = "img-fluid col-lg-3 col-md-6 col-sm-12" src="assets/imgs/banner2.png"/>
    </div>
</section>


<!--New-->
<section id="new" class="w-100 mb-0 pb-0">
    <div class="row p-0 m-0">
        <!--One-->
        <div class="one col-lg-4 col-md-12 col-sm-12 mb-0">
            <img class="img-fluid" src="assets/imgs/banner4.jpg" />
            <div class="details">
                <h2>GAMES</h2>
            </div>
        </div>
        <!--Two-->
        <div class="one col-lg-4 col-md-12 col-sm-12 mb-0">
            <img class="img-fluid" src="assets/imgs/holostar.jpg" />
            <div class="details">
                <h2>HOLOSTARS</h2>
            </div>
        </div>
        <!--Three-->
        <div class="one col-lg-4 col-md-12 col-sm-12 mb-0">
            <img class="img-fluid" src="assets/imgs/hologen1m.jpg" />
            <div class="details">
                <h2>HOLOGEN 1</h2>
            </div>
        </div>
    </div>
</section>

<!--Featured-->
<section id="featured" class="my-3 py-3">
    <div class="container text-center mt-3 py-3">
        <h3>Our Featured</h3>
        <hr class="mx-auto">
        <p>Here are our best sellers!</p>
    </div>
    <div class="row mx-auto container-fluid">


<!--LoopStartsHere-->
<?php include('server/get_featured_products.php'); ?>
<?php while($row= $featured_products->fetch_assoc()){?>

        <div class="product text-center col-lg-3 col-md-4">
            <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>"/>
            <div class="star">
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
                <i class="fas fa-star"></i>
            </div>
            <h5 class="p-name"><?php echo $row['product_name'];?></h5>
            <h4 class="p-price">$<?php echo $row['product_price'];?></h4>
           <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now</button></a>
        </div>
<?php } ?>

    </div> <!-- Added closing div tag for the row -->
</section>



<!--Banner-->
<section id="banner" class="my-5 py-5">
    <div class="container">
        <h4>MID SEASON'S SALE</h4>
        <h1>Autumn Collection <br> UP to 30% OFF</h1>
        <a href="shop.php" class="text-uppercase">SHOP NOW</a>
    </div>
</section>


<!--Plushies-->
<section id="plushies" class="my-5">
    <div class="container text-center mt-5 py-5">
        <h3>Plushies</h3>
        <hr class="mx-auto">
        <p>Check Our Cute Plushies!!</p>
    </div>
    <div class="row mx-auto container-fluid">
<!--LoopStartshere-->
<?php include('server/get_plushies.php')?>
<?php while($row = $plushies_products->fetch_assoc()){ ?>

    <div class="product text-center col-lg-3 col-md-4">
        <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>"/>
        <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
        <h4 class="p-price">$<?php echo $row['product_price']; ?></h4>
        <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now</button></a>
    </div>

<?php } ?>
</div> <!-- Added closing div tag for the row -->
</section>


<!--Games-->
<section id="games" class="my-5">
    <div class="container text-center mt-5 py-5">
        <h3>Game Collabs</h3>
        <hr class="mx-auto">
        <p>Check Out Our Game Collabs and Gain Physical Copies!</p>
    </div>
    <div class="row mx-auto container-fluid">
<!--LoopStartshere-->
<?php include('server/get_game.php')?>
<?php while($row = $game_products->fetch_assoc()){ ?>

    <div class="product text-center col-lg-3 col-md-4">
        <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>"/>
        <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
        <h4 class="p-price">$<?php echo $row['product_price']; ?></h4>
        <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now</button></a>
    </div>
<?php } ?>
</div> <!-- Added closing div tag for the row -->
</section>


<!--Tapestry-->
<section id="sweaters" class="my-5">
    <div class="container text-center mt-5 py-5">
        <h3>Tapestry</h3>
        <hr class="mx-auto">
        <p>Post Your Favorite in your Wall!</p>
    </div>
    <div class="row mx-auto container-fluid">
        <!--LoopStartshere-->
<?php include('server/get_tapestry.php')?>
<?php while($row = $tapestry_products->fetch_assoc()){ ?>

    <div class="product text-center col-lg-3 col-md-4">
        <img class="img-fluid mb-3" src="assets/imgs/<?php echo $row['product_image']; ?>"/>
        <div class="star">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <h5 class="p-name"><?php echo $row['product_name']; ?></h5>
        <h4 class="p-price">$<?php echo $row['product_price']; ?></h4>
        <a href="<?php echo "single_product.php?product_id=". $row['product_id'];?>"><button class="buy-btn">Buy Now</button></a>
    </div>
<?php } ?>
</div> <!-- Added closing div tag for the row -->
</section>


<?php include('layouts/footer.php'); ?>