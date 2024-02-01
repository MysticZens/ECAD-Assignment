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
<script>
function validateForm()
{
    if (document.feedback.rating.value == "" || document.feedback.rating.value == null) 
    {
        alert("Please click at least a star rated from 1 star to 5 stars!");
        return false;
    }
    return true;
}
</script>
<!-- Create a cenrally located container -->
<div style="width:50%; margin:auto;">
<form method="post" name="feedback" onsubmit="return validateForm()">
    <div class="mb-3 row">
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Add Feedback</span>
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="subject">Subject:</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="subject" id="subject" 
                   type="text" cols="25" rows="4" maxlength="255"></textarea>
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
                <input type="number" name="rating" id="rating" min='1' max='5' style="display: none;" />
                <i class="fa-regular fa-star" style="--i: 0; font-size: 30px;"></i>
                <i class="fa-regular fa-star" style="--i: 1; font-size: 30px;"></i>
                <i class="fa-regular fa-star" style="--i: 2; font-size: 30px;"></i>
                <i class="fa-regular fa-star" style="--i: 3; font-size: 30px;"></i>
                <i class="fa-regular fa-star" style="--i: 4; font-size: 30px;"></i>
            </div>
        </div>
    </div>
    <div class="mb-3 row">       
        <div class="col-sm-9 offset-sm-3">
            <br /><button class="submitbutton" type="submit">Submit</button>
        </div>
    </div>
</form>
<script>
// Selecting the stars
const allStar = document.querySelectorAll('.rating .fa-star');

// Selecting the input
const ratingValue = document.querySelector('.rating input');

allStar.forEach((item, idx)=> {
    item.addEventListener('click', function () {
        let click = 0;
        ratingValue.value = idx + 1;

        allStar.forEach(i=> {
            i.classList.replace("fa-solid", "fa-regular");
            i.classList.remove('active');
        });
        for(let i=0; i<allStar.length; i++) {
            if(i <= idx) {
                allStar[i].classList.replace("fa-regular", "fa-solid");
                allStar[i].classList.add('active');
            } else {
                allStar[i].style.setProperty('--i', click);
                click++;
            }
        }
    })
})
</script>
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