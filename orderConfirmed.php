<?php
include("header.php"); // Ensure this includes the necessary HTML structure
include_once("mysql_conn.php"); // Make sure this contains the connection logic
include_once("cartFunctions.php"); // Assumes this file contains relevant cart and order functions

echo "<div class='order-confirmation card container text-center'>"; // Added for potential CSS styling
// Check if checkout was successful and an OrderID is set
if (isset($_SESSION['OrderID'])) {
    echo "<h2><b>Checkout Successful</b></h2>";
    echo "<p>Your order number is: <strong>" . $_SESSION['OrderID'] . "</strong>. Please keep this for your records.</p>";
    echo "<p>Thank you for your purchase.</p>";

    // Retrieve the latest GST rate from the database
    $gstQuery = "SELECT TaxRate FROM GST ORDER BY EffectiveDate DESC LIMIT 1";
    $gstStmt = $conn->prepare($gstQuery);
    $gstStmt->execute();
    $gstResult = $gstStmt->get_result();
    $gstRow = $gstResult->fetch_assoc();
    $currentGSTRate = isset($gstRow['TaxRate']) ? $gstRow['TaxRate'] : 0;
    $gstStmt->close();

    // Calculate shipping based on subtotal
    $shippingCharge = $_SESSION["SubTotal"] > 200 ? 0 : $_SESSION["ShipCharge"];
    $shippingMsg = $_SESSION["SubTotal"] > 200 ? "Free Express Shipping" : "$" . number_format($shippingCharge, 2);
    
    // Display shipping charge and GST
    echo "<p>Shipping Charge: <strong>" . $shippingMsg . "</strong></p>";
    echo "<p>GST: <strong>$" . number_format($_SESSION["SubTotal"] * ($currentGSTRate / 100), 2) . "</strong></p>";

    // Display number of products and details
    echo "<p>Number of Products: <strong>" . count($_SESSION['Items']) . "</strong></p>";
    echo "<p>Products Purchased:</p>";
    foreach ($_SESSION['Items'] as $item) {
        echo "<p>- " . htmlspecialchars($item['name']) . " (Product ID: " . htmlspecialchars($item['productId']) . ", Quantity: " . htmlspecialchars($item['quantity']) . ", Price: $" . number_format($item['price'], 2) . ")</p>";
    }

    // Calculate and display the total amount paid
    $totalPaid = $_SESSION["SubTotal"] + ($_SESSION["SubTotal"] * ($currentGSTRate / 100)) + $shippingCharge;
    echo "<p>Total Amount Paid: <strong>$" . number_format($totalPaid, 2) . "</strong></p>";
    echo '<p><a href="index.php">Continue Shopping</a></p>';
    echo "</div>"; // End of order-confirmation div

    // Clear the cart session variables if the order is finalized
    unset($_SESSION['Items'], $_SESSION['SubTotal'], $_SESSION['OrderID'], $_SESSION['ShipCharge'], $_SESSION['Tax']);
} else {
    // Handle cases where the OrderID is not set, indicating checkout wasn't properly completed
    echo "<p>Order processing was not completed. Please try again or contact support if you need assistance.</p>";
}
echo "</div>";
echo "<br />";
include("footer.php"); // Ensure this includes the closing HTML structure
?>
