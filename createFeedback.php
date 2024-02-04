<!-- Done by Qian Zhe -->
<!-- Student ID: S10243009K -->
<?php
// Start the session
session_start();
// Include the Page Layout header
include("header.php");

// Redirect to login page if shopper is not logged in
if (!isset($_SESSION["ShopperID"])) {
    header("Location: login.php");
    exit();
}

// Include the file that establishes database connection handle: $conn 
include_once("mysql_conn.php");

// Get the current date and time
$currentDateTime = date('Y-m-d'); 
?>
<!-- JavaScript function to validate the feedback form -->
<script>
function validateForm() {
    // Check if a star rating is selected
    if (document.feedback.rating.value == "" || document.feedback.rating.value == null) {
        alert("Please click at least a star rated from 1 star to 5 stars!");
        return false;
    }
    return true;
}
</script>

<!-- Create a centrally located container -->
<div style="width:50%; margin:auto;">
    <!-- Feedback Form -->
    <form method="post" name="feedback" onsubmit="return validateForm()">
        <!-- Form header -->
        <div class="mb-3 row">
            <div class="col-sm-9 offset-sm-3">
                <span class="page-title">Add Feedback</span>
            </div>
        </div>
        <!-- Form fields for feedback -->
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="subject">Subject:</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="subject" id="subject" type="text" cols="25" rows="4" maxlength="255"></textarea>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="content">Content:</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="content" id="content" cols="25" rows="4"></textarea>
            </div>
        </div>
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="rating">Rating:</label>
            <div class="col-sm-9">
                <!-- Star rating input and stars display -->
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
        <!-- Submit button -->
        <div class="mb-3 row">       
            <div class="col-sm-9 offset-sm-3">
                <br /><button class="submitbutton" type="submit">Submit</button>
            </div>
        </div>
    </form>

    <!-- JavaScript to handle star rating interaction -->
    <script>
        const allStar = document.querySelectorAll('.rating .fa-star');
        const ratingValue = document.querySelector('.rating input');

        allStar.forEach((item, idx) => {
            item.addEventListener('click', function () {
                let click = 0;
                ratingValue.value = idx + 1;

                allStar.forEach(i => {
                    i.classList.replace("fa-solid", "fa-regular");
                    i.classList.remove('active');
                });

                for (let i = 0; i < allStar.length; i++) {
                    if (i <= idx) {
                        allStar[i].classList.replace("fa-regular", "fa-solid");
                        allStar[i].classList.add('active');
                    } else {
                        allStar[i].style.setProperty('--i', click);
                        click++;
                    }
                }
            })
        });
    </script>

    <?php
    // Process after user clicks the submit button
    $checkSubmission = true;
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST["rating"])) {
        echo "<p><span style='color:red;'>Please rate the website of your liking!</span></p>;";
    } else if (isset($_SESSION["ShopperID"]) && isset($_POST["rating"])) {
        $subject = $_POST["subject"];
        $content = $_POST["content"];
        $rank = $_POST["rating"];

        // Prepare and execute SQL statement to insert feedback
        $qry = "INSERT INTO feedback (ShopperID, Subject, Content, Rank, DateTimeCreated)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($qry);
        $stmt->bind_param("issss", $_SESSION["ShopperID"], $subject, $content, $rank, $currentDateTime);

        if ($stmt->execute()) {
            // Retrieve the FeedbackID of the newly created feedback
            $qry = "SELECT LAST_INSERT_ID() AS FeedbackID";
            $result = $conn->query($qry);
            $stmt->close();
            $conn->close();
            // Display success message
            echo "<p ><span style='color:red;'>Successfully created feedback!</span></p>";
            echo "<script>
                  setTimeout(function() {
                      window.location.href = 'feedback.php';
                  }, 3000);
                  </script>";
        } else {
            // Display error message if insertion fails
            echo "<h3 style='text-align: center; color:red;'>Error in inserting record</h3>";
            $stmt->close();
            $conn->close();
        }
    }
    ?>
</div> <!-- Closing container -->
<?php 
// Include the Page Layout footer
include("footer.php");
?>
