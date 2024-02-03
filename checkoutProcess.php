<?php
session_start();
include("header.php"); // Include the Page Layout header
include_once("myPayPal.php"); // Include the file that contains PayPal settings
include_once("mysql_conn.php"); 
include_once("cartFunctions.php");

if ($_POST) // Post Data received from Shopping cart page.
{
    // To Do 6 (DIY): Check to ensure each product item saved in the associative
    //                array is not out of stock
    foreach ($_SESSION['Items'] as $key => $item) {
        $productID = $item["productId"]; // Retrieve Product ID from session
        $quantity = $item["quantity"]; // Retrieve quantity from session

        $qry = "SELECT Quantity FROM product WHERE ProductID=?"; // Prepare query to get available quantity from the database
        $stmt = $conn->prepare($qry);
        $stmt->bind_param("i", $productID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) { // If the query result contains data
            $stockQuantity = $row["Quantity"]; // Retrieve available quantity from the database

            if ($stockQuantity < $quantity) { // If available quantity < requested quantity
                echo "Product $item[productId] : $item[name] is out of stock!<br />";
                echo "Please return to the shopping cart to amend your purchase.<br />";
                include("footer.php");
                exit;
            }
        }
        $stmt->close();
    }
    // End of To Do 6
    // To Do: Add the following code to calculate the delivery charge based on the chosen mode
    if ($_SESSION["SubTotal"] > 200) {
        // Waive the delivery charge for orders over S$200
        $_SESSION["ShipCharge"] = 0;
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
    }

    $paypal_data = '';
    // Get all items from the shopping cart, concatenate to the variable $paypal_data
    // $_SESSION['Items'] is an associative array
    foreach ($_SESSION['Items'] as $key => $item) {
        $paypal_data .= '&L_PAYMENTREQUEST_0_QTY' . $key . '=' . urlencode($item["quantity"]);
        $paypal_data .= '&L_PAYMENTREQUEST_0_AMT' . $key . '=' . urlencode($item["price"]);
        $paypal_data .= '&L_PAYMENTREQUEST_0_NAME' . $key . '=' . urlencode($item["name"]);
        $paypal_data .= '&L_PAYMENTREQUEST_0_NUMBER' . $key . '=' . urlencode($item["productId"]);
    }

    // To Do 1A: Compute GST amount 7% for Singapore, round the figure to 2 decimal places 
    $_SESSION["Tax"] = round($_SESSION["SubTotal"] * 0.07, 2);

    // To Do 1B: Compute Shipping charge S$2.00 per trip
    $_SESSION["ShipCharge"] = 2.00;

    // Data to be sent to PayPal
    $padata = '&CURRENCYCODE=' . urlencode($PayPalCurrencyCode) .
        '&PAYMENTACTION=Sale' .
        '&ALLOWNOTE=1' .
        '&PAYMENTREQUEST_0_CURRENCYCODE=' . urlencode($PayPalCurrencyCode) .
        '&PAYMENTREQUEST_0_AMT=' . urlencode($_SESSION["SubTotal"] +
            $_SESSION["Tax"] +
            $_SESSION["ShipCharge"]) .
        '&PAYMENTREQUEST_0_ITEMAMT=' . urlencode($_SESSION["SubTotal"]) .
        '&PAYMENTREQUEST_0_SHIPPINGAMT=' . urlencode($_SESSION["ShipCharge"]) .
        '&PAYMENTREQUEST_0_TAXAMT=' . urlencode($_SESSION["Tax"]) .
        '&BRANDNAME=' . urlencode("GiftTown Singapore") .
        $paypal_data .
        '&RETURNURL=' . urlencode($PayPalReturnURL) .
        '&CANCELURL=' . urlencode($PayPalCancelURL);

    // We need to execute the "SetExpressCheckOut" method to obtain the PayPal token
    $httpParsedResponseAr = PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername,
        $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);

    // Respond according to the message we receive from Paypal
    if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) ||
        "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])
    ) {
        if ($PayPalMode == 'sandbox')
            $paypalmode = '.sandbox';
        else
            $paypalmode = '';

        // Redirect user to PayPal store with Token received.
        $paypalurl = 'https://www' . $paypalmode .
            '.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=' .
            $httpParsedResponseAr["TOKEN"] . '';
        header('Location: ' . $paypalurl);
    } else {
        // Show error message
        echo "<div style='color:red'><b>SetExpressCheckOut failed : </b>" .
            urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]) . "</div>";
        echo "<pre>" . print_r($httpParsedResponseAr) . "</pre>";
    }
}

// Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
if (isset($_GET["token"]) && isset($_GET["PayerID"])) {
    // we will be using these two variables to execute the "DoExpressCheckoutPayment"
    // Note: we haven't received any payment yet.
    $token = $_GET["token"];
    $playerid = $_GET["PayerID"];
    $paypal_data = '';

    // Get all items from the shopping cart, concatenate to the variable $paypal_data
    // $_SESSION['Items'] is an associative array
    foreach ($_SESSION['Items'] as $key => $item) {
        $paypal_data .= '&L_PAYMENTREQUEST_0_QTY' . $key . '=' . urlencode($item["quantity"]);
        $paypal_data .= '&L_PAYMENTREQUEST_0_AMT' . $key . '=' . urlencode($item["price"]);
        $paypal_data .= '&L_PAYMENTREQUEST_0_NAME' . $key . '=' . urlencode($item["name"]);
        $paypal_data .= '&L_PAYMENTREQUEST_0_NUMBER' . $key . '=' . urlencode($item["productId"]);
    }

    // Data to be sent to PayPal
    $padata = '&TOKEN=' . urlencode($token) .
        '&PAYERID=' . urlencode($playerid) .
        '&PAYMENTREQUEST_0_PAYMENTACTION=' . urlencode("SALE") .
        $paypal_data .
        '&PAYMENTREQUEST_0_ITEMAMT=' . urlencode($_SESSION["SubTotal"]) .
        '&PAYMENTREQUEST_0_TAXAMT=' . urlencode($_SESSION["Tax"]) .
        '&PAYMENTREQUEST_0_SHIPPINGAMT=' . urlencode($_SESSION["ShipCharge"]) .
        '&PAYMENTREQUEST_0_AMT=' . urlencode($_SESSION["SubTotal"] +
            $_SESSION["Tax"] +
            $_SESSION["ShipCharge"]) .
        '&PAYMENTREQUEST_0_CURRENCYCODE=' . urlencode($PayPalCurrencyCode);

    // We need to execute the "DoExpressCheckoutPayment" at this point 
    // to receive payment from the user.
    $httpParsedResponseAr = PPHttpPost('DoExpressCheckoutPayment', $padata,
        $PayPalApiUsername, $PayPalApiPassword,
        $PayPalApiSignature, $PayPalMode);

    // Check if everything went ok..
    if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) ||
        "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])
    ) {
        // To Do 5 (DIY): Update stock inventory in the product table 
        //                after successful checkout
        $cartId = $_SESSION["Cart"]; // Retrieve Cart ID from session
        $qry = "SELECT ProductID, Quantity FROM shopcartitem WHERE ShopCartID=?"; // Select ProductID and Quantity from shopcartitem table
        $stmt = $conn->prepare($qry);
        $stmt->bind_param("i", $cartId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) { // Loop through each row of shop cart items
            $productId = $row["ProductID"]; // Retrieve Product ID for each item
            $quantityPurchased = $row["Quantity"]; // Retrieve Quantity for each item
            $query = "UPDATE Product SET Quantity=Quantity-? WHERE ProductID=?";
            $updatedstmt = $conn->prepare($query); // Prepare a query to update the product quantity in the Product table
            $updatedstmt->bind_param("ii", $quantityPurchased, $productId);
            $updatedstmt->execute();
            $updatedstmt->close();
        }
        $stmt->close();

        // End of To Do 5

        // To Do 2: Update the shopcart table, close the shopping cart (OrderPlaced=1)
        $total = $_SESSION["SubTotal"] + $_SESSION["Tax"] + $_SESSION["ShipCharge"];
        $qry = "UPDATE shopcart SET OrderPlaced=1, Quantity=?,
				SubTotal=?, ShipCharge=?, Tax=?, Total=?
				WHERE ShopCartID=?";
        $stmt = $conn->prepare($qry);
        // "i" - integer, "d" - double
        $stmt->bind_param("iddddi", $_SESSION["NumCartItem"],
            $_SESSION["SubTotal"], $_SESSION["ShipCharge"],
            $_SESSION["Tax"], $total,
            $_SESSION["Cart"]);
        $stmt->execute();
        $stmt->close();
        // End of To Do 2

        // We need to execute the "GetTransactionDetails" API Call at this point 
        // to get customer details
        $transactionID = urlencode(
            $httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
        $nvpStr = "&TRANSACTIONID=" . $transactionID;
        $httpParsedResponseAr = PPHttpPost('GetTransactionDetails', $nvpStr,
            $PayPalApiUsername, $PayPalApiPassword,
            $PayPalApiSignature, $PayPalMode);

        if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) ||
            "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])
        ) {
            // To Do 3 (DIY): Insert an Order record with shipping information
            //               Get the Order ID and save it in the session variable.	
            $ShipName = addslashes(urldecode($httpParsedResponseAr["SHIPTONAME"]));

            $ShipAddress = urldecode($httpParsedResponseAr["SHIPTOSTREET"]);
            if (isset($httpParsedResponseAr["SHIPTOSTREET2"]))
                $ShipAddress .= ' ' . urldecode($httpParsedResponseAr["SHIPTOSTREET2"]);
            if (isset($httpParsedResponseAr["SHIPTOCITY"]))
                $ShipAddress .= ' ' . urldecode($httpParsedResponseAr["SHIPTOCITY"]);
            if (isset($httpParsedResponseAr["SHIPTOSTATE"]))
                $ShipAddress .= ' ' . urldecode($httpParsedResponseAr["SHIPTOSTATE"]);
            $ShipAddress .= ' ' . urldecode($httpParsedResponseAr["SHIPTOCOUNTRYNAME"]) .
                ' ' . urldecode($httpParsedResponseAr["SHIPTOZIP"]);

            $ShipCountry = urldecode(
                $httpParsedResponseAr["SHIPTOCOUNTRYNAME"]);

            $ShipEmail = urldecode($httpParsedResponseAr["EMAIL"]);

            // To Do 3: Insert an Order record with shipping information
            //          Get the Order ID and save it in the session variable.	
            $qry = "INSERT INTO orderdata (ShipName, ShipAddress, ShipCountry, 
											ShipEmail, ShopCartID)
					 VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($qry);
            // "s" - string, "i" - integer
            $stmt->bind_param("ssssi", $ShipName, $ShipAddress, $ShipCountry,
                $ShipEmail, $_SESSION["Cart"]);
            $stmt->execute();
            $stmt->close();
            $qry = "SELECT LAST_INSERT_ID() AS OrderID";
            $result = $conn->query($qry);
            $row = $result->fetch_array();
            $_SESSION["OrderID"] = $row["OrderID"];
            // End of To Do 3

            $conn->close();

            // To Do 4A: Reset the "Number of Items in Cart" session variable to zero. 
            $_SESSION["NumCartItem"] = 0;

            // To Do 4B: Clear the session variable that contains Shopping Cart ID.
            unset($_SESSION["Cart"]);

            // To Do 4C: Redirect the shopper to the order confirmed page.
            header("Location: orderConfirmed.php");
            exit;
        } else {
            echo "<div style='color:red'><b>GetTransactionDetails failed:</b>" .
                urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]) . '</div>';
            echo "<pre>" . print_r($httpParsedResponseAr) . "</pre>";
            $conn->close();
        }
    } else {
        echo "<div style='color:red'><b>DoExpressCheckoutPayment failed : </b>" .
            urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]) . '</div>';
        echo "<pre>" . print_r($httpParsedResponseAr) . "</pre>";
    }
}

include("footer.php"); // Include the Page Layout footer
?>
