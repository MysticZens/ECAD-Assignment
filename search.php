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
        <div class="col-sm-3">
            <button type="submit">Search</button>
        </div>
    </div>  <!-- End of 2nd row -->
</form>

<?php

include_once("mysql_conn.php");
// The non-empty search keyword is sent to server
if (isset($_GET["keywords"]) && trim($_GET['keywords']) != "") {
    // To Do (DIY): Retrieve list of product records with "ProductTitle" 
    $keywords = '%' . $_GET["keywords"] . '%';
    $result = $_GET["keywords"];
    echo "Search results for <b>" . $result . "</b>: ";
    
    $qry = "SELECT ProductID, ProductTitle, ProductDesc FROM product WHERE ProductTitle 
    LIKE '%$keywords%' OR ProductDesc LIKE '%$keywords%' ORDER BY ProductTitle";
 
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

echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>