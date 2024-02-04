<!-- Done by Isaac -->
<!-- Student ID: S10242151E -->
<?php
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<!-- Create a container, 60% width of viewport, centered -->
<div style="width:60%; margin:auto;">
    <!-- Display Page Header -->
    <div class="row" style="padding:5px"> <!-- Start of header row -->
        <div class="col-12 text-center">
            <span class="page-title">Product Categories</span>
            <p>Select a category listed below:</p>
        </div>
    </div> <!-- End of header row -->

    <?php
    // Include the PHP file that establishes database connection handle: $conn
    include_once("mysql_conn.php");

    $qry = "SELECT * FROM Category Order by CatName ASC"; // Form SQL to select all categories 
    $result = $conn->query($qry); // Execute the SQL and get the result
    // Display each category in a row
    while ($row = $result->fetch_array()) {
        echo "<div class='row' style='padding:5px'>"; // Start a new row
        // Left column display a text link showing the category's name, 
        // display category's description in a new paragraph
        $catname = urlencode($row["CatName"]);
        $catproduct = "catProduct.php?cid=$row[CategoryID]&catName=$catname";
        echo "<div class='col-8 text-center'>"; // 67% of row width, centered
        echo "<p><a href=$catproduct>$row[CatName]</a></p>";
        echo "$row[CatDesc]";
        echo "</div>";
        // Right column - display the category's image
        $img = "./Images/category/$row[CatImage]";
        echo "<div class='col-4 text-center'>"; // 33% of row width, centered
        echo "<img src='$img' />";
        echo "</div>";
        echo "</div>"; // End of a row
    }

    $conn->close(); // Close the database connection
    echo "</div>"; // End of the container
    include("footer.php"); // Include the Page Layout footer
    ?>
