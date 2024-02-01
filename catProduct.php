<?php
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<!-- Create a container, 60% width of viewport, centered -->
<div style="width:60%; margin:auto;">
    <!-- Display Page Header - Category's name is read 
     from the query string passed from the previous page -->
    <div class="row" style="padding:5px">
        <div class="col-12 text-center">
            <span class="page-title"><?php echo "$_GET[catName]"; ?></span>
        </div>
    </div>
    <br>

    <?php
    // Include the PHP file that establishes the database connection handle: $conn
    include_once("mysql_conn.php");

    // To Do:  Starting ....
    $cid = $_GET["cid"]; // Read Category ID from the query string
    // Form SQL to retrieve a list of products associated with the Category ID
    $qry = "SELECT p.ProductID, p.ProductTitle, p.ProductImage, p.Price, p.Quantity, p.Offered, p.OfferedPrice
            FROM CatProduct cp INNER JOIN product p ON cp.ProductID=p.ProductID 
            WHERE cp.CategoryID=? Order by p.ProductTitle ASC";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $cid); // "i" integer
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    // Display each product in a row
    while ($row = $result->fetch_array()) {
        echo "<div class='row' style='padding:5px'>"; // Start a new row
        // Left column - display a text link showing the product's name, 
        // display the selling price in red in a new paragraph
        $product = "productDetails.php?pid=$row[ProductID]";
        $formattedPrice = number_format($row["Price"], 2);
        echo "<div class='col-6 text-center'>"; // 67% of row width, centered
        echo "<p><a href=$product>$row[ProductTitle]</a></p>";
        if ($row["Offered"] == 0) {
            echo "Price: <span style='font-weight: bold; color: red;'>S$ $formattedPrice</span>";
        } else if ($row["Offered"] == 1) {
            $formattedPrice2 = number_format($row["OfferedPrice"], 2);
            echo "Price: <span style='font-weight: bold; color: red; text-decoration: line-through;'>S$ $formattedPrice</span>";
            echo "<span style='font-weight: bold; color: red;'>&nbsp now <b>S$ $formattedPrice2</b></span>";
        }
        echo "</div>";

        // Right column display the product's image 
        $img = "./Images/products/$row[ProductImage]";
        echo "<div class='col-6 text-center'>"; // 33% of row width, centered
        echo "<img src='$img' style='width: 100%; max-width: 200px;' />";
        echo "</div>";
        echo "</div>"; // End of a row
    }
    // To Do:  Ending ....

    $conn->close(); // Close the database connection
    echo "</div>"; // End of the container
    include("footer.php"); // Include the Page Layout footer
    ?>
