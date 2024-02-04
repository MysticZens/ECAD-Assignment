<?php 
session_start(); // Start or resume the session
include("header.php"); // Include the Page Layout header
// Check if member has logged in already
if (isset($_SESSION["ShopperID"])) {
    header("Location: index.php");
    exit();
}
?>

<!-- Create a centrally located container -->
<div style="width:50%; margin:auto;">
<form method="post">
	<div class="row mb-3">
		<div class="col-sm-9 offset-sm-3">
			<span class="page-title">Forget Password</span>
		</div>
	</div>
	<!-- Form for entering the email address -->
	<div class="row mb-3">
		<label class="col-sm-3 col-form-label" for="eMail">
			Email Address:</label>
		<div class="col-sm-9">
			<input class="form-control" name="eMail" id="eMail"
					type="email" required />
		</div>
	</div>
	<!-- Button to submit the form and initiate the password retrieval process -->
	<div class="row mb-3">      
		<div class="col-sm-9 offset-sm-3">
			<br />
			<button class="submitbutton" type="submit">Submit</button>
		</div>
	</div>
</form>

<?php 
// Process after the user clicks the submit button
if (isset($_POST["eMail"])) {
	// Read the email address entered by the user
	$eMail = $_POST["eMail"];
	// Retrieve shopper record based on email address
	include_once("mysql_conn.php");
	$qry = "SELECT Email, PwdQuestion FROM shopper";
	$result = $conn->query($qry);
	$checkEmail = false;
	$checkPassword = false;

	// Loop through the results to check if the email exists and has a password question
	while ($row = $result->fetch_array()) {
		if ($row["Email"] == $eMail) {
			$_SESSION["TempEmail"] = $eMail;
			$checkEmail = true;
			if ($row["PwdQuestion"] != "" || $row["PwdQuestion"] != null) {
				$checkPassword = true;
			}
		}
	}
	$conn->close();

	// Redirect to getPassword.php if the email and password question are valid
	if ($checkEmail && $checkPassword) {
		header("Location: getPassword.php");
		exit();
	}
	// Display a message to contact support if there is no password question
	else if (!$checkPassword && $checkEmail) {
		echo "<p><span>
				Please contact: <b><a href='mailto:giftownSingapore@np.edu.sg' style='text-decoration: none; color: red'>
				gifttownsingapore@gmail.com</a></b> for external help to reset your password.</span></p>";
	}
	// Display an error message if the email address is invalid
	else {
		echo "<p><span style='color:red;'>
				Invalid E-mail address!</span></p>";
	}
}
?>
</div> <!-- Closing container -->
<br />
<?php 
include("footer.php"); // Include the Page Layout footer
?>
