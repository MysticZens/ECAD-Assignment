<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
if (!isset($_SESSION["TempEmail"])) { // Check if user logged in 
	// redirect to login page if the session variable shopperid is not set
	header ("Location: forgetPassword.php");
	exit;
}
include_once("mysql_conn.php");
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

<!-- Create a cenrally located container -->
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
            <p><?php echo $pwdQuestion ?><p>     
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="passwordAnswer">
            Password Answer:</label>
        <div class="col-sm-9">
            <input class="form-control" name="passwordAnswer" id="passwordAnswer" 
                   type="text" maxlength="50" required />        
        </div>
    </div>
    <div class="mb-3 row">       
        <div class="col-sm-9 offset-sm-3">
            </br><button class="submitbutton" type="submit">Get Password</button>
        </div>
    </div>
</form>

<?php
if (isset($_POST["passwordAnswer"])) {
    $pwdAnswer = $_POST["passwordAnswer"];
    $qry = "SELECT * FROM Shopper WHERE Email=? AND PwdQuestion=? AND PwdAnswer=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("sss", $_SESSION["TempEmail"], $pwdQuestion, $pwdAnswer);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_array();
        $shopperId = $row["ShopperID"];
        $email = $row["Email"];
        $new_pwd = generateRandomPassword();
        $qry = "UPDATE Shopper SET Password=? WHERE ShopperID=?"; 
		$stmt = $conn->prepare($qry);
		// "s" - string, "i" - integer
		$stmt->bind_param("si", $new_pwd, $shopperId);
		$stmt->execute();
		$stmt->close();
        // To Do 2: e-Mail the new password to user
		include("myMail.php");
		// The "Send To" should be the e-mail address indicated
		// by shopper, i.e $eMail. In this case, use a testing e-mail 
		// address as the shopper's e-mail address in our database 
		// may not be a valid account.
		$to=$email; // use the gmail account that the user has logged in with
		$from="giftownsingapore@gmail.com"; // use the gmail account created
		$from_name="Giftown Singapore Online Gift Store";
		$subject="Giftown Singapore Login Password"; // e-mail title 
		// HTML body message
		$body="<span style='color:black; font-size:15px'>
				Your new password is <span style='font-weight:bold'> 
				$new_pwd</span>.<br />
				Do change this default password in the ecommerce website. </span>";
		// Initiate the e-mailing sending process
		if(smtpmailer($to, $from, $from_name, $subject, $body)) { 
			echo "<p>Your new password is sent to:
				  <span style='font-weight:bold'>$to</span>.</p>";
            unset($_SESSION["TempEmail"]);
		}
		
		else {
			echo "<p><span style='color:red;'>
				  Mailer Error: Cannot send E-mail Address!</span></p>";
		}
		// End of To Do 2
    }
    
    else {
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