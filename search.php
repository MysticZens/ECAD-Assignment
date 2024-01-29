<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>

<!-- HTML Form to collect search keyword and submit it to the same page in server -->
<div style="width:80%; margin:auto;"> <!-- Container -->
<form name="frmSearch" method="get" action="">
    <div class="mb-3 row"> <!-- 1st row -->
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Product Search</span>
        </div>
    </div> <!-- End of 1st row -->
    <div class="mb-3 row"> <!-- 2nd row -->
        <label for="keywords" 
               class="col-sm-3 col-form-label">Product Title:</label>
        <div class="col-sm-6">
            <input class="form-control" name="keywords" id="keywords" 
                   type="search" />
        </div>
    </div>
    <div class="mb-3 row"> <!-- 2nd row -->
        <label for="keywords1" 
               class="col-sm-3 col-form-label">Occasion:</label>
        <div class="col-sm-6">
            <input class="form-control" name="keywords1" id="keywords1" 
                   type="search" />
        </div>
    </div>
    <div class="mb-3 row"> <!-- 2nd row -->
        <label for="keywords2" 
               class="col-sm-3 col-form-label">Max Price:</label>
        <div class="col-sm-6">
            <input class="form-control" name="keywords2" id="keywords2" 
                   type="number" />
        </div>
    </div>
    <div class="mb-3 row"> <!-- 2nd row -->
        <label for="keywords3" 
               class="col-sm-3 col-form-label">Min Price:</label>
        <div class="col-sm-6">
            <input class="form-control" name="keywords3" id="keywords3" 
                   type="number" />
        </div>
    </div>
    <div class="mb-3 row">
        <div class="col-sm-3">
            <button class="submitbutton" type="submit">Search</button>
        </div>
    </div>  <!-- End of 2nd row -->
</form>

<?php

include_once("mysql_conn.php");
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
    echo "Search results for Title/Description : <b>" . $result . "</b> Occasion :<b>" 
    . $result1 ."</b> Max Price :<b>" . $result2 ."</b> Min Price :<b>" . $result3;
    
    $qry = "SELECT p.ProductID, p.ProductTitle, p.ProductDesc, 
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
            $product = "productDetails.php?pid=$row[ProductID]";
            echo "<tr><td><p><a style = 'text-decoration:none' href=$product>$row[ProductTitle]</a></p></td></tr>";
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