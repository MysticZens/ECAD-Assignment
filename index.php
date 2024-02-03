<?php 
// Detect the current session
session_start();

// Include the Page Layout header
include("header.php");
?>

<div class="container" style="text-align: center">
     <h2>Online Gift Shop</h2>
     <p>Buy gifts to send surprises and happiness to your closed ones and family.</p>
</div>

<!-- Display an image related to the gift shop -->
<img src="Images/Gifts.png" class="img-responsive" 
     style="display:block; margin:auto; width: 800px; height: 500px; justify-content: center"/>";

<div class="container" style="text-align: center; margin-top: 20px;">
     <div class="card">
          <div class="card-body">
               <!-- Display the top 3 special discounted offers -->
               <h2><b>Top 3 Special Discounted Offers</b></h2>

               <?php
               // Include database connection
               include_once("mysql_conn.php");

               // Query to retrieve top 3 discounted offers
               $qry = "SELECT * FROM product
                       WHERE DATEDIFF(OfferEndDate, OfferStartDate) >= DATEDIFF(now(), OfferStartDate)
                       AND Offered = 1 AND Quantity > 0
                       ORDER BY OfferedPrice/Price*100 ASC
                       LIMIT 3";

               // Execute the query
               $result = $conn->query($qry);

               // Check if there are any offers available
               if ($result->num_rows > 0) {
                    // Loop through each offer
                    while ($row = $result->fetch_array()) {
                         // Calculate the discount percentage
                         $discount = 100 - ($row["OfferedPrice"] / $row["Price"]) * 100;
                    
                         // Construct the image path
                         $img = "./Images/products/$row[ProductImage]";
                         
                         // Display the price information
                         $formattedPrice = number_format($row["Price"], 2);
                         $formattedPrice2 = number_format($row["OfferedPrice"], 2);
                         $formattedDiscount = number_format($discount, 1);
                         
                         // Display the product details in a card
                         echo "<div class='card mb-3 container text-center' style='max-width: 540px; '>";
                         echo "<div class='row g-0'>";

                         // Left column - display the product's image
                         echo "<div class='col-md-4'>";
                         echo "<img src='$img' class='img-fluid rounded-start' alt='Product Image'>";
                         echo "</div>";

                         // Right column - display the product details
                         echo "<div class='col-md-8'>";
                         echo "<div class='card-body'>";
                         echo "<h5 class='card-title'><a href='productDetails.php?pid=$row[ProductID]'>$row[ProductTitle]</a></h5>";
                         echo "<p class='card-text'>";
                         echo "Price: <span style='font-weight: bold; color: red; text-decoration: line-through;'>S$ $formattedPrice</span>";
                         echo "<span style='font-weight: bold; color: black; padding: 5px;'>     - $formattedDiscount%</span>";
                         echo "<br / ><span style='font-weight: bold; color: red;'>&nbsp NOW! <b>S$ $formattedPrice2</b></span>";
                         echo "</p>";
                         echo "</div>"; // End of card-body
                         echo "</div>"; // End of col-md-8
                         echo "</div>"; // End of row
                         echo "</div>"; // End of card
                    }
               }
               else {
                    // Display a message if no offers are available
                    echo "<p style='font-weight: bold; color: red; font-size: 20px;'> No OFFERS are available right now! Come back for more offers!!</p>";
               }
               ?>

          </div>
     </div>
</div>

<br />

<?php 
// Include the Page Layout footer
include("footer.php"); 
?>
