<!-- Done by Team 3 -->
<?php 
//Display guest welcome message, Login and Registration links
//when shopper has yet to login,
$content1 = "Welcome to Giftown Singapore! (<b>Guest</b>)<br />";
$content2 = "<li class='nav-item'>
		     <a class='nav-link' href='register.php'>Register</a></li>
			 <li class='nav-item'>
		     <a class='nav-link' href='login.php'>Login</a></li>";

if(isset($_SESSION["ShopperName"])) { 
    //Display a greeting message, Change Password and logout links 
    //after shopper has logged in.
    $content1 = "Welcome to Giftown Singapore! (<b>$_SESSION[ShopperName]</b>)";
    $content2 = "<li class='nav-item'>
                 <a class='nav-link' href='updateProfile.php'>Update Profile</a></li> 
                 <li class='nav-item'>
                 <a class='nav-link' href='logout.php'>Logout</a></li>
                 <li class='nav-item'>
		         <a class='nav-link' href='shoppingCart.php'><i class='fa-solid fa-cart-shopping' style='font-size: 20px;'></i></a></li>";

    //Display number of item in cart
    if (isset($_SESSION["NumCartItem"])) {
        $content1 .= ", $_SESSION[NumCartItem] item(s) in shopping cart";
    }
}
?>
<!-- Display a navbar which is visible before or after collapsing -->
<nav class="navbar navbar-expand-md navbar-dark bg-custom">
    <div class="container-fluid">
        <!-- Dynamic Text Display -->
        <span class="navbar-text ms-md-2"
          style="color:#ff911b; max-width: 80%;">
           <?php echo $content1; ?>
        </span>
        <!-- Toggler/Collapsible Button -->
        <button class="navbar-toggler" type="button:" data-bs-toggle="collapse"
            data-bs-target="#collapsibleNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>
<!-- Define a collapsible navbar -->
<nav class="navbar navbar-expand-md navbar-dark bg-custom"> 
    <div class="container-fluid">
        <!-- Collapsible part of navbar -->
        <div class="collapse navbar-collapse" id="collapsibleNavbar"> 
            <!-- Left-justified menu items -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item" >
                    <a class="nav-link" href="category.php">Product Categories</a> 
                </li>
                <li class="nav-item" >
                    <a class="nav-link" href="search.php">Product Search</a> 
                </li>
                <li class="nav-item" >
                    <a class="nav-link" href="feedback.php">Feedback</a> 
                </li>
            </ul>
            <!-- Right-justified menu items -->
            <ul class="navbar-nav ms-auto">
                <?php echo $content2; ?>
            </ul>
        </div>
    </div>
</nav>