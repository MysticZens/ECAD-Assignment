<!-- Done by Qian Zhe -->
<!-- Student ID: S10243009K -->
<?php 
session_start(); // Start or resume the session

include("header.php"); // Include the Page Layout header

// Check if the user is logged in
if (!isset($_SESSION["TempEmail"])) {
	// Redirect to login page if the session variable "TempEmail" is not set
	header ("Location: forgetPassword.php");
	exit();
}

include_once("mysql_conn.php"); // Include the MySQL connection file

// Retrieve user information based on the session email
$qry = "SELECT * FROM Shopper WHERE Email=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("s", $_SESSION["TempEmail"]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array();
$pwdQuestion = $row["PwdQuestion"];
$stmt->close();
?>

<?php
// Function to generate a random password between 8 to 20 characters
function generateRandomPassword() {
    $length = rand(8, 20);
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_-=+;:,<.>';
    $charactersLength = strlen($characters);
    $randomPassword = '';

    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomPassword;
}
?>

<!-- Create a centrally located container -->
<div style="width:50%; margin:auto;">
<form method="post">
    <div class="row mb-3">
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Retrieve Password</span>
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="passwordQuestion">
            Password Question:</label>
        <div class="col-sm-9">
            <p class="col-form-label"><?php echo $pwdQuestion ?></p>  
        </div>
    </div>
    <!-- Form for entering the password answer -->
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="passwordAnswer">
            Password Answer:</label>
        <div class="col-sm-9">
            <input class="form-control" name="passwordAnswer" id="passwordAnswer" 
                    type="text" maxlength="50" required />        
        </div>
    </div>
    <!-- Button to submit the form and retrieve the password -->
    <div class="mb-3 row">       
        <div class="col-sm-9 offset-sm-3">
            <br /><button class="submitbutton" type="submit">Get Password</button>
        </div>
    </div>
</form>

<?php
// Process the form submission
if (isset($_POST["passwordAnswer"])) {
    $pwdAnswer = $_POST["passwordAnswer"];
    $qry = "SELECT * FROM Shopper WHERE Email=? AND PwdQuestion=? AND PwdAnswer=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("sss", $_SESSION["TempEmail"], $pwdQuestion, $pwdAnswer);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if the answer is valid
    if ($result->num_rows > 0) {
        $row = $result->fetch_array();
        $shopperId = $row["ShopperID"];
        $email = $row["Email"];
        
        // Generate a new random password
        $new_pwd = generateRandomPassword();
        
        // Update the user's password in the database
        $qry = "UPDATE Shopper SET Password=? WHERE ShopperID=?"; 
        $stmt = $conn->prepare($qry);
        $stmt->bind_param("si", $new_pwd, $shopperId);
        $stmt->execute();
        $stmt->close();

        // Include mail sending functionality
        include("myMail.php");
        
        // Email the new password to the user
        $to = $email;
        $from = "giftownsingapore@gmail.com";
        $from_name = "Giftown Singapore Online Gift Store";
        $subject = "Giftown Singapore Login Password";
        $body = "<span style='color:black; font-size:15px'>
                    Your new password is <span style='font-weight:bold'> 
                    $new_pwd</span>.<br />
                    Do change this default password in the ecommerce website. </span>";
        
        // Send the email
        if(smtpmailer($to, $from, $from_name, $subject, $body)) {
            echo $body; 
            echo "<p>Your new password is also sent to:
                    <span style='font-weight:bold'>$to</span>. Please check your email account.</p>";
            unset($_SESSION["TempEmail"]);
            
            // Redirect the user after a delay of 15 seconds
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'forgetPassword.php';
                    }, 15000);
                    </script>";
        } else {
            echo "<p><span style='color:red;'>
                    Mailer Error: Cannot send E-mail Address!</span></p>";
        }
    } else {
        echo "<p><span style='color:red;'>
                Invalid answer to the Question!</span></p>";
    }
}
?>
</div> <!-- Closing container -->
<br />

<?php 
$conn->close();
include("footer.php"); // Include the Page Layout footer
?>
