<?php
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<!-- Create a container, 90% width of viewport, centered -->
<div style='width:60%; margin:auto;'>

    <?php
    $pid = $_GET["pid"]; // Read Product ID from the query string

    // Include the PHP file that establishes the database connection handle: $conn
    include_once("mysql_conn.php");
    $qry = "SELECT * from product where ProductID=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $pid);     // "i" - integer 
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    // Display Product information
    while ($row = $result->fetch_array()) {
        echo "<div class='row' >"; // Start a new row
        // Left column - display the product's name
        echo "<div class='col-sm-12 text-center' style='padding:5px'>";
        echo "<span class='page-title'>$row[ProductTitle]</span>";
        echo "</div>";
        echo "</div>"; // End of row

        echo "<div class='row'>"; // Start a new row

        // Left column - display the product's description and specifications
        echo "<div class='col-sm-9' style='padding:5px'>";
        echo "<p>$row[ProductDesc]</p>";
        $qry = "SELECT s.SpecName, ps.SpecVal FROM productspec ps 
                INNER JOIN specification s ON ps.SpecID=s.SpecID 
                WHERE ps.ProductID=?
                ORDER BY ps.Priority";
        $stmt = $conn->prepare($qry);
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $result2 = $stmt->get_result();
        $stmt->close();
        while ($row2 = $result2->fetch_array()) {
            echo $row2["SpecName"] . ": " . $row2["SpecVal"] . "<br />";
        }
        echo "</div>"; // End of left column

        // Right column - display the product's image and price
        $img = "./Images/products/$row[ProductImage]";
        echo "<div class='col-sm-3 text-center' style='padding:5px'>";
        echo "<p><img src='$img' style='width: 100%; max-width: 200px;' /></p>";
        echo "Price: ";
        if ($row["Quantity"] <= 0) {
            echo "<button disabled style='background-color: red; color: white;'>Add to Cart</button><br>";
            echo "<b style='color:red;'>Out Of Stock</b>";
        } 
        else {
            if ($row["Offered"] == 1) {
                $formattedPrice = number_format($row["Price"], 2);
                $formattedPrice2 = number_format($row["OfferedPrice"], 2);
                echo "<span style='font-weight:bold; color:red; text-decoration: line-through;'>S$ $formattedPrice</span>";
                echo "<span style='font-weight:bold; color:red;'>&nbsp S$ $formattedPrice2</span>";
            } else if ($row["Offered"] == 0) {
                $formattedPrice = number_format($row["Price"], 2);
                echo "<span style='font-weight:bold; color:red;'>S$ $formattedPrice</span>";
            }
            // Create a Form for adding the product to the shopping cart
            echo "<form action='cartFunctions.php' method='post'>";
            echo "<input type='hidden' name='action' value='add' />";
            echo "<input type='hidden' name='product_id' value='$pid' />";
            echo "Quantity: <input type='number' name='quantity' value='1' 
                    min='1' max='10' style='width: 40px' required />";
            echo "<button type='submit' ";
            if ($row["Quantity"] <= 0) {
                echo "<button disabled style='background-color: red; color: white;'>Add to Cart</button><br>";
                echo "<b style='color:red;'>Out Of Stock</b>";
            } else {
                echo "<button style='background-color: red; color: white;'>Add to Cart</button>";
            }
            echo "</form>";
        }
        echo "</div>"; // End of right column
        echo "</div>"; // End of row
    }

    $conn->close(); // Close the database connection
    echo "</div>"; // End of container
    include("footer.php"); // Include the Page Layout footer
    ?>
