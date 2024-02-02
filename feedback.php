<?php
session_start();
include("header.php"); // Include the Page Layout header
include_once("mysql_conn.php");
?>

<div class="table-responsive" style="width: 80%; margin: auto">
    <div style="margin-bottom:20px;">
        <!-- Button to navigate to createFeedback.php for adding a new review -->
        <a href="createFeedback.php"><button class="submitbutton">Review</button></a>
        
        <!-- Form for filtering reviews by rating -->
        <form method="post" style="display: flex; align-items: center; float: right;">
            <label for="filterRating">Filter by Rating:</label>
            <select id="filterRating" name="filterRating" style="margin-right: 30px">
                <option value="all">All Ratings</option>
                <option value="1">1 star</option>
                <option value="2">2 stars</option>
                <option value="3">3 stars</option>
                <option value="4">4 stars</option>
                <option value="5">5 stars</option>
            </select>  
            <button class="submitbutton" type="submit">Filter</button>     
        </form>
    </div>
    
    <!-- Table to display feedback details -->
    <table class="table table-striped table-bordered" style="white-space:nowrap;">
        <thead class="table" style="text-align: center;">
            <tr>
                <th style="background-color: #ff3f3f; color: white;">Shopper's Name</th>
                <th style="background-color: #ff3f3f; color: white;">Subject</th>
                <th style="background-color: #ff3f3f; color: white;">Content</th>
                <th style="background-color: #ff3f3f; color: white;">Rating</th>
                <th style="background-color: #ff3f3f; color: white;">Date Created</th>
            </tr>
        </thead>
        <tbody class="table">
            <!-- Table Body -->
            <?php
            // Check if filtering by rating is requested
            if (isset($_POST["filterRating"]) && $_POST["filterRating"] != "all") {
                $qry = "SELECT s.Name, f.* FROM feedback f 
                        INNER JOIN shopper s 
                        ON s.ShopperID = f.ShopperID
                        WHERE Rank=?
                        ORDER BY Rank DESC
                        LIMIT 10";
                $stmt = $conn->prepare($qry);
                $stmt->bind_param("i", $_POST["filterRating"]);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                // Retrieve all feedback records without filtering
                $qry = "SELECT s.Name, f.* FROM feedback f 
                        INNER JOIN shopper s 
                        ON s.ShopperID = f.ShopperID
                        ORDER BY Rank DESC
                        LIMIT 10";
                $result = $conn->query($qry);
            }
            
            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_array()) {
                    echo "<tr>";
                    // Display feedback details in table cells
                    echo "<td style='background-color: #ffbebe'>" . htmlspecialchars($row["Name"]) . "</td>";
                    echo "<td style='background-color: #ffbebe'>" . htmlspecialchars($row["Subject"]) . "</td>";
                    echo "<td style='background-color: #ffbebe'>" . htmlspecialchars($row["Content"]) . "</td>";
                    echo "<td style='background-color: #ffbebe; text-align: center;'>";
                    
                    // Display star ratings based on the rating
                    for ($i = 0; $i < $row["Rank"]; $i++) {
                        echo "<i class='fa-solid fa-star' style='color: #ffe63b; background-color: #ffbebe; margin: 0; padding: 0'></i>";
                    }
                    echo "</td>";
                    
                    // Display the date when the feedback was created
                    echo "<td style='background-color: #ffbebe'>" . htmlspecialchars($row["DateTimeCreated"]) . "</td>";
                    echo "</tr>";
                }
            } else {
                // Display a message if no feedback records are found
                echo "<tr><td style='background-color: #ffbebe' colspan='5'><p style='color: red; font-size: 25px; text-align: center; background-color: #ffbebe'><b>No records found!</b></p></td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
<br />
<?php 
$conn->close();
include("footer.php"); // Include the Page Layout footer
?>
