<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header

// Include the PHP file that establishes database connection handle: $conn 
include_once("mysql_conn.php");

// To Do 1: Check if user logged in 
if (!isset($_SESSION["ShopperID"])) {
    header("Location: index.php");
    exit();
}
// End of To Do 1
?>

<script type="text/javascript">
function validateForm()
{
    // Check if password matched
	if (document.changePwd.pwd1.value != document.changePwd.pwd2.value) {
 	    alert("Passwords not matched!");
        return false;   // cancel submission
    }
    return true;  // No error found
}
</script>
<!-- Create a cenrally located container -->
<div style="width:80%; margin:auto;">
<form name="changePwd" method="post" onsubmit="return validateForm()">
    <div class="form-group row">
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Change Password</span>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="pwd1">
         New Password:</label>
        <div class="col-sm-9">
            <input class="form-control" name="pwd1" id="pwd1" 
                   type="password" required />
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="pwd2">
         Retype Password:</label>
        <div class="col-sm-9">
            <input class="form-control" name="pwd2" id="pwd2"
                   type="password" required />
        </div>
    </div>
    <div class="form-group row">       
        <div class="col-sm-9 offset-sm-3">
            <button type="submit">Update</button>
        </div>
    </div>
</form>

<?php
// Process after user click the submit button
if (isset($_POST["pwd1"])) {
	// To Do 2: Read new password entered by user
	$password = $_POST["pwd1"];
	
	// To Do 3: Hash the default password
	$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
	
	// To Do 4: Update the new password hash
    $shopperId = $_SESSION["ShopperID"];
	$qry = "UPDATE shopper SET Password=? WHERE ShopperID=?";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("si", $hashedPassword, $shopperId);
    $stmt->execute();
    $stmt->close();
    echo "<br />";
    echo "<p>Your new password is changed successfully.</p>";
}
?>

</div> <!-- Closing container -->
<?php 
include("footer.php"); // Include the Page Layout footer
?>