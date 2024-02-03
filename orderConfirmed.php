<?php
session_start();
include("header.php"); // Include the Page Layout header
include_once("myPayPal.php"); // Include the file that contains PayPal settings
include_once("mysql_conn.php"); 
include_once("cartFunctions.php");

// Check if the form was submitted
if ($_POST) {
    // ... (existing code remains unchanged)

    // Display the order confirmation details
    echo "<p>Checkout successful. Your order number is $_SESSION[OrderID]</p>";
    echo "<p>Thank you for your purchase.</p>";

    // Display the shipping charge
    if ($_SESSION["SubTotal"] > 200) {
        echo "<p>Shipping Charge: Free Express Shipping</p>";
    } else {
        echo "<p>Shipping Charge: $" . number_format($_SESSION["ShipCharge"], 2) . "</p>";
    }

    // Display the number of products
    echo "<p>Number of Products: $_SESSION[NumCartItem]</p>";

    // Display the product details
    echo "<p>Products Purchased:</p>";
    foreach ($_SESSION['Items'] as $item) {
        echo "<p>- $item[name] (Product ID: $item[productId], Quantity: $item[quantity], Price: $" . number_format($item["price"], 2) . ")</p>";
    }

    // Display a link to continue shopping
    echo '<p><a href="index.php">Continue shopping</a></p>';
}

include("footer.php"); // Include the Page Layout footer
?>
