<?php
session_start();
include("header.php");
if (!isset($_SESSION["ShopperID"])) {
    header("Location: login.php");
    exit();
}
include_once("mysql_conn.php");
$currentDate = date('Y-m-d'); 
?>
<script type="text/javascript">
    const allStar = document.querySelectorAll('.rating .fa-star');
    const ratingValue = document.querySelector('.rating input');    
    // Gets all the star items
    allStar.forEach((item, idx)=> {
        item.addEventListener('click', function () {
            let click = 0;
            ratingValue.value = idx + 1;

            allStar.forEach(i=> {
                // replaces all the solid stars with empty one when click
                i.classList.replace('fa-solid', 'fa-regular');
                i.classList.remove('active');
            });
            for(let i=0; i<allStar.length; i++) {
                if(i <= idx) {
                    // Replaces all the empty stars with solid ones when clicked
                    allStar[i].classList.replace('fa-regular', 'fa-solid');
                    allStar[i].classList.add('active');
                } else {
                    allStar[i].style.setProperty('--i', click);
                    click++;
                }
            }
        });
    });
</script>
<!-- Create a cenrally located container -->
<div style="width:50%; margin:auto;">
<form name="feedback" method="post">
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
                <input type="number" name="rating" id="rating" hidden required>
                <i class="fa-regular fa-star" style="--i: 0; font-size: 20px;"></i>
                <i class="fa-regular fa-star" style="--i: 1; font-size: 20px;"></i>
                <i class="fa-regular fa-star" style="--i: 2; font-size: 20px;"></i>
                <i class="fa-regular fa-star" style="--i: 3; font-size: 20px;"></i>
                <i class="fa-regular fa-star" style="--i: 4; font-size: 20px;"></i>
            </div>
        </div>
    </div>
    <div class="mb-3 row">       
        <div class="col-sm-9 offset-sm-3">
            <br /><button class="submitbutton" type="submit">Submit</button>
        </div>
    </div>
</form>
</div> <!-- Closing container -->
<?php
// Process after user click the submit button
if (isset($_SESSION["ShopperID"]) && isset($_POST["rating"])) {
    $subject = $_POST["subject"];
    $content = $_POST["content"];
    $rank = $_POST["rating"];
    $qry = "INSERT INTO feedback (ShopperID, Subject, Content, Rank, DateCreated)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("issss", $_SESSION["ShopperID"], $subject, $content, $rank, $currentDate);

    if ($stmt->execute()) { // SQL statement executed successfully
        // Retrieve the Shooper ID assigned to the new shopper
        $qry = "SELECT LAST_INSERT_ID() AS FeedbackID";
        $result = $conn->query($qry); // Execute the SQL and get the returned result 
        // Successful message 
        echo "<meta httpequiv='refresh' content='5; url=feedback.php'><p><span style='color:red;'>
        Successfully created feedback!</span></p>";
    }

    else { // Error message
         echo "<h3 style='text-align: center; color:red'>Error in inserting record</h3>";
    }
    $stmt->close();
    $conn->close();
}

else {
    echo "<h3 style='color: red; text-align: center'><b>You haven't ranked the rating yet!</b></h3>";
}

?>
<?php 
include("footer.php"); // Include the Page Layout footer
?>