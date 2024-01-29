<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>

<!-- Create a cenrally located container -->
<div style="width:50%; margin:auto;">
<form method="post">
	<div class="row mb-3">
		<div class="col-sm-9 offset-sm-3">
			<span class="page-title">Forget Password</span>
		</div>
	</div>
	<div class="row mb-3">
		<label class="col-sm-3 col-form-label" for="eMail">
         Email Address:</label>
		<div class="col-sm-9">
			<input class="form-control" name="eMail" id="eMail"
                   type="email" required />
		</div>
	</div>
	<div class="row mb-3">      
		<div class="col-sm-9 offset-sm-3">
            <br />
			<button class="submitbutton" type="submit">Submit</button>
		</div>
	</div>
	
</form>

<?php 
// Process after user click the submit button
if (isset($_POST["eMail"])) {
	// Read email address entered by user
	$eMail = $_POST["eMail"];
	// Retrieve shopper record based on e-mail address
	include_once("mysql_conn.php");
	$qry = "SELECT Email, PwdQuestion FROM shopper";
    $result = $conn->query($qry);
	$checkEmail = false;
	$checkPassword = false;
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

    if ($checkEmail && $checkPassword) {
		header("Location: getPassword.php");
		exit();
    }

	else if (!$checkPassword && $checkEmail) {
		echo "<p><span>
			  Please contact: <b><a href='mailto:giftownSingapore@np.edu.sg' style='text-decoration: none; color: red'>
			  gifttownsingapore@gmail.com</a></b> for external help to reset your password.</span></p>";
	}

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