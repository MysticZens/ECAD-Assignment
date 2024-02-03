<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header

if (isset($_SESSION["OrderID"])) {
    
// Display the details of each product
    echo "<p>Products Ordered:</p>";
    echo "<ul>";
    foreach ($_SESSION["Items"] as $item) {
        echo "<li>{$item['name']} (Quantity: {$item['quantity']})</li>";
    }
    echo "</ul>";

    // Display the total amount paid
    $totalAmount = $_SESSION["SubTotal"] + $_SESSION["ShipCharge"];
    echo "<p>Total Amount Paid: S$" . number_format($totalAmount, 2) . "</p>";

    echo "<p>Thank you for your purchase.&nbsp;&nbsp;";
    echo '<a href="index.php">Continue shopping</a></p>';
} 

include("footer.php"); // Include the Page Layout footer
?>
