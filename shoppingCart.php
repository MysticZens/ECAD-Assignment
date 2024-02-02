<?php 
// Include the code that contains shopping cart's functions.
// Current session is detected in "cartFunctions.php, hence need not start session here.
include_once("cartFunctions.php");
include("header.php"); // Include the Page Layout header

if (! isset($_SESSION["ShopperID"])) { // Check if user logged in 
	// redirect to login page if the session variable shopperid is not set
	header ("Location: login.php");
	exit;
}

echo "<div id='myShopCart' style='margin:auto; text-align:center'>"; // Start a container
if (isset($_SESSION["Cart"])) {
	include_once("mysql_conn.php");
	// Retrieve from database and display shopping cart in a table
	
    $qry = "SELECT *, (Price*Quantity) AS Total 
			FROM shopcartitem WHERE ShopCartID=?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("i", $_SESSION["Cart"]); // "i" - integer
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	
	if ($result->num_rows > 0) {
		// Format and display
		// the page header and header row of shopping cart page
		echo "<p class='page-title' style='text-align:center'>Shopping Cart</p>"; 
		echo "<div class='table-responsive'>"; // Bootstrap responsive table
		echo "<table class='table table-hover table-striped table-bordered' style='width:88%; margin:auto; border:1px solid white'>"; // Start of table
        echo "<thead class='cart-header'>"; // Start of table's header section 
		echo "<tr>"; // Start of header row
		echo "<th width='250px' style='background-color:#ff3f3f; color:white'>Item</th>"; 
		echo "<th width='90px' style='background-color:#ff3f3f; color:white'>Price (S$)</th>";
		echo "<th width='60px' style='background-color:#ff3f3f; color:white'>Quantity</th>";
		echo "<th width='120px' style='background-color:#ff3f3f; color:white'>Total (S$)</th>";
		echo "<th style='background-color:#ff3f3f; color:white'>&nbsp;</th>";
		echo "</tr>"; // End of header row
		echo "</thead>"; // End of table's header section
		// Declare an array to store the shopping cart items in session variable 
		$_SESSION["Items"]=array();
		// Display the shopping cart content
		$subTotal = 0; // Declare a variable to compute subtotal before tax
		$quantity = 0;
		echo "<tbody>"; // Start of table's body section
		while ($row = $result->fetch_array()) {
	
			echo "<tr>";
			echo "<td style= 'width: 50%; background-color:#ffbebe' > $row[Name]<br />";
			echo "Product ID: $row[ProductID]</td>";
			$formattedPrice = number_format($row["Price"], 2);
			echo "<td style='background-color:#ffbebe'>$formattedPrice</td>";
			echo "<td style='background-color:#ffbebe'>"; // Column for update quantity of purchase
			echo "<form action= 'cartFunctions.php' method='post'>";
			echo "<select name='quantity' onChange='this.form.submit()' style='background-color:#ffbebe; border:none'>";
			for ($i = 1; $i <= 10; $i++) { // To populate drop-down list from 1 to 10 
				if($i== $row["Quantity"])
					// Select drop-down list item with value same as the quantity of purchase
					$selected = "selected";
				else
					$selected = ""; // No specific item is selected
				echo "<option value='$i' $selected>$i</option>";
			}
			echo "</select>";
			echo "<input type='hidden' name='action' value='update' />";
			echo "<input type='hidden' name='product_id' value='$row[ProductID]' />";
			echo "</form>";
			echo "</td>";
			$formattedTotal = number_format($row["Total"], 2);
			echo "<td style='background-color:#ffbebe'>$formattedTotal</td>";
			echo "<td style='background-color:#ffbebe'>"; // Column for remove item from shopping cart
			echo "<form action = 'cartFunctions.php' method='post'>";
			echo "<input type='hidden' name='action' value='remove' />";
			echo "<input type='hidden' name='product_id' value='$row[ProductID]' />";
			echo "<input type='image' src='images/trash-can.png' style='width:20px; height:20px' title='Remove Item' />"; 
			echo "</form>";
			echo "</td>";
			echo "</tr>";
		    // Store the shopping cart items in session variable as an associate array
			$_SESSION["Items"][] = array("productId" => $row["ProductID"],
										"name" => $row["Name"],
										"price" => $row["Price"],
										"quantity"=> $row["Quantity"]);
			// Accumulate the running sub-total
			$subTotal += $row["Total"];
			$quantity += $row["Quantity"];
		}
		echo "</tbody>"; // End of table's body section
		echo "</table>"; // End of table
		echo "</div>"; // End of Bootstrap responsive table

		// Display the subtotal at the end of the shopping cart
		//echo "<p style='text-align:center; font-size: 18px'> 
		echo "<br>";
		echo "<p style='text-align:right; font-size: 18px; width:94%'> 
			  Subtotal = S$". number_format($subTotal, 2);
		$_SESSION["SubTotal"] = round($subTotal, 2);
		$_SESSION["NormalShipCharge"] = 5.00;	

		// If subtotal exceeds 200
		if ($_SESSION["SubTotal"] > 200) {
			$_SESSION["ExpressShipCharge"] = 0;
			
			// echo "<p style='text-align:right; font-size: 20px'> 
		 	// Your subtotal exceeds $200! You quality for free express shipping!";

			echo "<p style='text-align:right; font-size: 18px; width:94%'> 
		 	Express Delivery Charge = S$". number_format($_SESSION["ExpressShipCharge"], 2);
		}

		// If subtotal less than 200
		else {
			$_SESSION["ExpressShipCharge"] = 10;

			echo "<p style='text-align:right; font-size: 18px; width:94%'> 
			Normal Delivery Charge = S$".number_format($_SESSION["NormalShipCharge"], 2);

			echo "<p style='text-align:right; font-size: 18px; width:94%'> 
		 	Express Delivery Charge = S$". number_format($_SESSION["ExpressShipCharge"], 2);
		}

		echo "<p style='text-align:right; font-size: 18px; width:94%'> 
		Total Quantity = ". number_format($quantity);

		// Add PayPal Checkout button on the shopping cart page
		echo "<form method='post' action=' checkoutProcess.php'>"; 
		echo "<input type='image' style='float:right; margin-right:5.5%'
						src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif'>";
		echo "</form></p>";
		echo "<br>";	
		echo "<br>";
		echo "<br>";		
	}
	else {
		echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
	}
	$conn->close(); // Close database connection
}
else {
	echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
}
echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>