<?php
session_start();
include("header.php");
if (!isset($_SESSION["ShopperID"])) {
    header("Location: login.php");
    exit();
}
include_once("mysql_conn.php");
$currentDateTime = date('Y-m-d'); 
?>

<!-- Create a cenrally located container -->
<div style="width:50%; margin:auto;">
<form method="post">
    <div class="mb-3 row">
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Add Feedback</span>
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="subject">Subject:</label>
        <div class="col-sm-9">
            <input class="form-control" name="subject" id="subject" 
                   type="text" maxlength="255" />
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="content">Content:</label>
        <div class="col-sm-9">
        <textarea class="form-control" name="content" id="content"
                      cols="25" rows="4"></textarea>
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="rating">Rating:</label>
        <div class="col-sm-9">
            <div class="col-form-label rating">
            <input type="radio" id="star" name="rating" value="1">
            <label for="rating" title="1 star">
            <?php 
                echo "<i class='fa-solid fa-star' style='color: #ffe63b; padding: 0'></i>";
            ?>
            </label>
            <input type="radio" id="star" name="rating" value="2">
            <label for="rating" title="2 stars">
            <?php
                for ($i=0; $i<2; $i++) {
                    echo "<i class='fa-solid fa-star' style='color: #ffe63b; margin: 0; padding: 0'></i>";
                }
            ?> 
            </label>
            <input type="radio" id="star" name="rating" value="3">
            <label for="rating" title="3 stars">
            <?php
                for ($i=0; $i<3; $i++) {
                    echo "<i class='fa-solid fa-star' style='color: #ffe63b; padding: 0'></i>";
                }
            ?> 
            </label>
            <input type="radio" id="star" name="rating" value="4">
            <label for="rating" title="4 stars">
            <?php
                for ($i=0; $i<4; $i++) {
                    echo "<i class='fa-solid fa-star' style='color: #ffe63b; margin: 0; padding: 0'></i>";
                }
            ?> 
            </label>
            <input type="radio" id="star" name="rating" value="5">
            <label for="rating" title="5 stars">
            <?php
                for ($i=0; $i<5; $i++) {
                    echo "<i class='fa-solid fa-star' style='color: #ffe63b; margin: 0; padding: 0'></i>";
                }
            ?> 
            </label>
            </div>
        </div>
    </div>
    <div class="mb-3 row">       
        <div class="col-sm-9 offset-sm-3">
            <br /><button class="submitbutton" type="submit">Submit</button>
        </div>
    </div>
</form>

<?php
// Process after user click the submit button
$checkSubmission = true;
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["rating"])) {
    echo "<p><span style='color:red;'>
          Please rate the website of your liking!</span></p>;";
}

else if (isset($_SESSION["ShopperID"]) && isset($_POST["rating"])) {
    $subject = $_POST["subject"];
    $content = $_POST["content"];
    $rank = $_POST["rating"];
    $qry = "INSERT INTO feedback (ShopperID, Subject, Content, Rank, DateTimeCreated)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("issss", $_SESSION["ShopperID"], $subject, $content, $rank, $currentDate);

    if ($stmt->execute()) { // SQL statement executed successfully
        // Retrieve the Shooper ID assigned to the new shopper
        $qry = "SELECT LAST_INSERT_ID() AS FeedbackID";
        $result = $conn->query($qry); // Execute the SQL and get the returned result 
        $stmt->close();
        $conn->close();
        // Successful message 
        echo "<p ><span style='color:red;'>
        Successfully created feedback!</span></p>";
        echo "<script>
              setTimeout(function() {
                  window.location.href = 'feedback.php';
              }, 3000);
              </script>";
    }

    else { // Error message
         echo "<h3 style='text-align: center; color:red'>Error in inserting record</h3>";
         $stmt->close();
         $conn->close();
    }
}

?>
</div> <!-- Closing container -->
<?php 
include("footer.php"); // Include the Page Layout footer
?>