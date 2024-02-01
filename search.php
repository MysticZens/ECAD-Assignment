<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>

<!-- HTML Form to collect search keyword and submit it to the same page in server -->
<div style="width: 80%; margin: auto;"> <!-- Container -->
    <form name="frmSearch" method="get" action="">
        <div class="mb-3 row text-center"> <!-- 1st row -->
            <div class="col-sm-12">
                <h2 class="page-title">Product Search</h2>
            </div>
        </div> <!-- End of 1st row -->

        <div class="mb-3 row"> <!-- 2nd row -->
            <label for="keywords" class="col-sm-3 col-form-label text-end">Product Title:</label>
            <div class="col-sm-6">
                <input class="form-control" name="keywords" id="keywords" type="search" />
            </div>
        </div>

        <div class="mb-3 row"> <!-- 3rd row -->
            <label for="keywords1" class="col-sm-3 col-form-label text-end">Occasion:</label>
            <div class="col-sm-6">
                <input class="form-control" name="keywords1" id="keywords1" type="search" />
            </div>
        </div>

        <div class="mb-3 row"> <!-- 4th row -->
            <div class="col-sm-3 col-form-label text-end">Price Range:</div>
            <div class="col-sm-3">
                <label for="keywords3" class="visually-hidden">Min Price:</label>
                <input class="form-control" name="keywords3" id="keywords3" type="number" placeholder="Min Price" />
            </div>
            <div class="col-sm-3">
                <label for="keywords2" class="visually-hidden">Max Price:</label>
                <input class="form-control" name="keywords2" id="keywords2" type="number" placeholder="Max Price" />
            </div>
        </div>

        <div class="mb-3 row"> <!-- 5th row -->
            <div class="col-sm-12 text-center">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </div> <!-- End of 5th row -->
    </form>
</div>


<?php

include_once("mysql_conn.php");
echo "<div style='text-align: center;'>";
// The non-empty search keyword is sent to server
if ((isset($_GET["keywords"]) && trim($_GET['keywords']) != "") & 
    (isset($_GET["keywords1"]) && trim($_GET['keywords1']) != "") &
    (isset($_GET["keywords2"]) && trim($_GET['keywords2']) != "") &
    (isset($_GET["keywords3"]) && trim($_GET['keywords3']) != "")){
    // To Do (DIY): Retrieve list of product records with "ProductTitle" 
    $keywords = '%' . $_GET["keywords"] . '%';
    $result = $_GET["keywords"];
    $keywords1 = '%' . $_GET["keywords1"] . '%';
    $result1 = $_GET["keywords1"];
    $keywords2 = intval($_GET["keywords2"]);  // Convert to integer
    $result2 = $_GET["keywords2"];
    $keywords3 = intval($_GET["keywords3"]);  // Convert to integer
    $result3 = $_GET["keywords3"];
    echo "Search results for Title/Description : <b>" . $result . "</b> Occasion : <b>" 
    . $result1 ."</b> Max Price : $<b>" . $result2 ."</b>   Min Price : $<b>" . $result3 . "</br>";
    
    $qry = "SELECT p.ProductID, p.ProductTitle, p.ProductDesc, p.ProductImage, p.Offered, p.Price, p.OfferedPrice,
    CASE WHEN p.Offered = 1 THEN p.OfferedPrice ELSE p.Price END AS CurrentPrice,
    ps.SpecVal, ps.SpecID 
    FROM product p 
    INNER JOIN productspec ps ON p.ProductID = ps.ProductID
    WHERE (p.ProductTitle LIKE '%$keywords%' OR p.ProductDesc LIKE '%$keywords%') 
    AND ps.SpecID = 1
    AND (ps.SpecVal LIKE '%$keywords1%' OR p.ProductDesc LIKE '%$keywords1%')
    AND ((p.Offered = 1 AND p.OfferedPrice <= $keywords2) OR (p.Offered = 0 AND p.Price <= $keywords2))
    AND ((p.Offered = 1 AND p.OfferedPrice >= $keywords3) OR (p.Offered = 0 AND p.Price >= $keywords3))
    ORDER BY ProductTitle";

 
    $result = mysqli_query($conn, $qry);

    if (mysqli_num_rows($result) > 0) {     
        // Output the results in a table
        echo "<table>";
        while ($row = mysqli_fetch_assoc($result)) {
            $productDetailsLink = "productDetails.php?pid=$row[ProductID]";
            $img = "./Images/products/$row[ProductImage]";

            echo "<img src='$img' style='width: 300px; max-width: 200px;' />";
            echo "<div style='padding: 3%; margin-bottom: 20px;'>";
            echo "<h3><a style='text-decoration:none' href='$productDetailsLink'>$row[ProductTitle]</a></h3>";
            echo "<p><strong>Description:</strong> $row[ProductDesc]</p>";
        
            // Displaying discounted price with strike-through for the original price
            if ($row['Offered'] == 1) {
                echo "<p><strong>Original Price </strong> <del>$$row[Price]</del></p>";
                echo "<p style='color:red;'><b>NOW</b> <strong>$$row[OfferedPrice]</strong></p>";
            } else {
                echo "<p><strong>Price</strong> $$row[Price]</p>";
            }
        
            echo "<p><strong>Specification Value:</strong> $row[SpecVal]</p>";
            echo "</div>";
        }
        echo "</table>";
    } else {
        echo "<br />";
        echo "<span style = color:red>No products found.</span>";
    }
	// To Do (DIY): End of Code
}
else {
    echo "<br />";
    echo "<span>Please Enter Values into all the Fields!</span>";
}

echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>