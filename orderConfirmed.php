<?php
session_start();
include("header.php"); // Include the Page Layout header
include_once("mysql_conn.php"); 
include_once("cartFunctions.php");

// Check if checkout was successful and an OrderID is set
if (isset($_SESSION['OrderID'])) {
    // Display the order confirmation details
    echo "<h2>Checkout Successful</h2>";
    echo "<p>Your order number is: <strong>" . $_SESSION['OrderID'] . "</strong>. Please keep this for your records.</p>";
    echo "<p>Thank you for your purchase.</p>";

    // Display the shipping charge
    if ($_SESSION["SubTotal"] > 200) {
        echo "<p>Shipping Charge: <strong>Free Express Shipping</strong></p>";
    } else {
        echo "<p>Shipping Charge: <strong>$" . number_format($_SESSION["ShipCharge"], 2) . "</strong></p>";
    }

    // Display the number of products
    echo "<p>Number of Products: <strong>" . count($_SESSION['Items']) . "</strong></p>";

    // Display the product details
    echo "<p>Products Purchased:</p>";
    foreach ($_SESSION['Items'] as $item) {
        echo "<p>- " . htmlspecialchars($item['name']) . " (Product ID: " . htmlspecialchars($item['productId']) . ", Quantity: " . htmlspecialchars($item['quantity']) . ", Price: $" . number_format($item['price'], 2) . ")</p>";
    }

    // Display a link to continue shopping
    echo '<p><a href="index.php">Continue Shopping</a></p>';

    // Optionally, clear the cart session variables if the order is finalized
    unset($_SESSION['Items'], $_SESSION['SubTotal'], $_SESSION['OrderID'], $_SESSION['ShipCharge']);
} else {
    // Redirect or display a message if OrderID isn't set (which means checkout might not have been completed successfully)
    echo "<p>Order processing was not completed. Please try again or contact support if you need assistance.</p>";
}

include("footer.php"); // Include the Page Layout footer
?>
