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

        if ($_SESSION["SubTotal"] > 200) {
            // Waive the delivery charge for orders over S$200
            $_SESSION["ShipCharge"] = 0;
            echo "<p style='text-align:right; font-size: 18px; width:94%; color:green;'>Congratulations! You qualify for free express delivery!";
        } else {
            // Calculate delivery charge based on the chosen delivery mode
            if (isset($_POST['deliveryMode'])) {
                $deliveryMode = $_POST['deliveryMode'];
                if ($deliveryMode == 'Express') {
                    $_SESSION["ShipCharge"] = $_SESSION["ExpressShipCharge"];
                } else {
                    $_SESSION["ShipCharge"] = $_SESSION["NormalShipCharge"];
                }
            }

            // Allow the user to choose their preferred delivery mode
            echo "<p style='text-align:right; font-size: 18px; width:94%'>Choose Delivery Mode:";
            echo "<div style='text-align:right; margin-right: 5.5%; width:94%'>";
            echo "<form method='post' action='checkoutProcess.php'>";
            if ($_SESSION["SubTotal"] > 200) {
                echo "<input type='hidden' name='deliveryMode' value='Express'>"; // Automatically select express delivery for them
            } else {
                // Display the dropdown for choosing delivery mode
                echo "<select name='deliveryMode' id='deliveryMode'>";
                echo "<option value='Normal'>Normal Delivery (\$5)</option>";
                echo "<option value='Express'>Express Delivery (\$10)</option>";
                echo "</select>";
            }
            echo "<input type='image' style='float:right; margin-right:5.5%' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif'>";
            echo "</form></div>";
            echo "<br>";
            echo "<br>";
            echo "<br>";
        }

    } else {
        echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
    }
    $conn->close(); // Close the database connection
} else {
    echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
}

echo "</div>"; // End of the container

include("footer.php"); // Include the Page Layout footer
?>
