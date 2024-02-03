<?php
// Include the code that contains shopping cart's functions.
// Current session is detected in "cartFunctions.php", hence need not start session here.
include_once("cartFunctions.php");
include("header.php"); // Include the Page Layout header

if (!isset($_SESSION["ShopperID"])) {
    // Check if the user is logged in
    // Redirect to the login page if the session variable shopperid is not set
    header("Location: login.php");
    exit;
}

echo "<div id='myShopCart' style='margin:auto; text-align:center'>"; // Start a container
if (isset($_SESSION["Cart"])) {
    include_once("mysql_conn.php");
    // Retrieve from the database and display the shopping cart in a table

    $qry = "SELECT *, (Price*Quantity) AS Total 
            FROM shopcartitem WHERE ShopCartID=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $_SESSION["Cart"]); // "i" - integer
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        // Format and display
        // the page header and header row of the shopping cart page
        echo "<p class='page-title' style='text-align:center'>Shopping Cart</p>";
        echo "<div class='table-responsive'>"; // Bootstrap responsive table
        echo "<table class='table table-hover table-striped table-bordered' style='width:88%; margin:auto; border:1px solid white'>"; // Start of the table
        echo "<thead class='cart-header'>"; // Start of the table's header section
        echo "<tr>"; // Start of the header row
        echo "<th width='250px' style='background-color:#ff3f3f; color:white'>Item</th>";
        echo "<th width='90px' style='background-color:#ff3f3f; color:white'>Price (S$)</th>";
        echo "<th width='60px' style='background-color:#ff3f3f; color:white'>Quantity</th>";
        echo "<th width='120px' style='background-color:#ff3f3f; color:white'>Total (S$)</th>";
        echo "<th style='background-color:#ff3f3f; color:white'>&nbsp;</th>";
        echo "</tr>"; // End of the header row
        echo "</thead>"; // End of the table's header section
        // Declare an array to store the shopping cart items in the session variable
        $_SESSION["Items"] = array();
        // Display the shopping cart content
        $subTotal = 0; // Declare a variable to compute subtotal before tax
        $quantity = 0;
        echo "<tbody>"; // Start of the table's body section
        while ($row = $result->fetch_array()) {
            echo "<tr>";
            echo "<td style='width: 50%; background-color:#ffbebe' > $row[Name]<br />";
            echo "Product ID: $row[ProductID]</td>";
            $formattedPrice = number_format($row["Price"], 2);
            echo "<td style='background-color:#ffbebe'>$formattedPrice</td>";
            echo "<td style='background-color:#ffbebe'>"; // Column for updating the quantity of purchase
            echo "<form action='cartFunctions.php' method='post'>";
            echo "<select name='quantity' onChange='this.form.submit()' style='background-color:#ffbebe; border:none'>";
            for ($i = 1; $i <= 10; $i++) { // To populate the drop-down list from 1 to 10
                if ($i == $row["Quantity"]) {
                    // Select the drop-down list item with a value the same as the quantity of purchase
                    $selected = "selected";
                } else {
                    // No specific item is selected
                    $selected = "";
                }
                echo "<option value='$i' $selected>$i</option>";
            }
            echo "</select>";
            echo "<input type='hidden' name='action' value='update' />";
            echo "<input type='hidden' name='product_id' value='$row[ProductID]' />";
            echo "</form>";
            echo "</td>";
            $formattedTotal = number_format($row["Total"], 2);
            echo "<td style='background-color:#ffbebe'>$formattedTotal</td>";
            echo "<td style='background-color:#ffbebe'>"; // Column for removing an item from the shopping cart
            echo "<form action='cartFunctions.php' method='post'>";
            echo "<input type='hidden' name='action' value='remove' />";
            echo "<input type='hidden' name='product_id' value='$row[ProductID]' />";
            echo "<input type='image' src='images/trash-can.png' style='width:20px; height:20px' title='Remove Item' />";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
            // Store the shopping cart items in the session variable as an associative array
            $_SESSION["Items"][] = array(
                "productId" => $row["ProductID"],
                "name" => $row["Name"],
                "price" => $row["Price"],
                "quantity" => $row["Quantity"]
            );
            // Accumulate the running sub-total
            $subTotal += $row["Total"];
            $quantity += $row["Quantity"];
        }
        echo "</tbody>"; // End of the table's body section
        echo "</table>"; // End of the table
        echo "</div>"; // End of Bootstrap responsive table

        // Display the subtotal at the end of the shopping cart
        echo "<br>";
        echo "<p style='text-align:right; font-size: 18px; width:94%'>Subtotal = S$" . number_format($subTotal, 2);
        $_SESSION["SubTotal"] = round($subTotal, 2);

        $_SESSION["NormalShipCharge"] = 5.00; // default normal ship charge rate
        $_SESSION["ExpressShipCharge"] = 10.00; // default express ship charge rate

        if ($_SESSION["SubTotal"] > 200) {
            $_SESSION["ExpressShipCharge"] = 0;
            echo "<p style='text-align:right; font-size: 18px; width:94%; color:green;'>Congratulations! You qualify for free express delivery!</p>";
        }

        // Display a form for delivery mode selection and PayPal checkout button
        echo "<form method='post' action='Checkoutprocess.php'>"; // This action should point to your script that initiates the PayPal checkout
        echo "<div style='text-align:right; margin-right: 5.5%; width:94%'>";
        if ($_SESSION["SubTotal"] <= 200) {
            // Offer delivery mode choice only if subtotal is <= 200
            echo "Choose Delivery Mode: ";
            echo "<select name='deliveryMode' id='deliveryMode'>";
            echo "<option value='Normal'>Normal Delivery (within 2 working days) (\$" . $_SESSION["NormalShipCharge"] . ")</option>";
            echo "<option value='Express'>Express Delivery(delivered within 24 hours) (\$" . $_SESSION["ExpressShipCharge"] . ")</option>";
            echo "</select><br>";
            echo "<br>";
        }
        // Hidden inputs to pass along the subtotal and any other required data
        echo "<input type='hidden' name='subtotal' value='" . $_SESSION["SubTotal"] . "'>";
        echo "<p style='text-align:right; font-size: 18px; width:100%'> 
		    Total Quantity = ". number_format($quantity);
        echo "<br>";
        // PayPal checkout button
        echo "<input type='image' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif' border='0' name='submit' alt='PayPal - The safer, easier way to pay online!'>";
        echo "</div>";
        echo "</form>";

    } else {
        echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
    }
    $conn->close();
} else {
    echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
}

echo "</div>";
include("footer.php");
?>