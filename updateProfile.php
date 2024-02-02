<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header

// Include the PHP file that establishes database connection handle: $conn 
include_once("mysql_conn.php");

if (!isset($_SESSION["ShopperID"])) {
    header("Location: login.php");
    exit();
}

$shopperId = $_SESSION["ShopperID"];
$qry = "SELECT * FROM Shopper WHERE ShopperID=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $shopperId);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_array();
$name = $row["Name"];
$dob = $row["BirthDate"];
$address = $row["Address"];
$country = $row["Country"];
$phone = substr($row["Phone"], 5);
$email = $row["Email"];
$pwd = $row["Password"];
$currentDate = date('Y-m-d');
$stmt->close();
?>

<script type="text/javascript">
function authenticateForm()
{
    // Check if password matched
	if (document.updateProfile.pwd1.value != document.updateProfile.pwd2.value) {
 	    alert("Passwords not matched!");
        return false;   // cancel submission
    }

    if (document.updateProfile.telephone.value != "") { 
        var str = document.updateProfile.telephone.value; 
        if (str.length != 8) {
            alert("Please enter a 8-digit phone number."); 
            return false; // cancel submission
        }
        else if (str.substr(0,1) != "6" &&
                 str.substr(0,1) != "8" &&
                 str.substr(0,1) != "9" ) {
            alert("Phone number in Singapore should start with 6, 8 or 9.");
            return false; // cancel submission
        }
    }

    if (document.updateProfile.passwordQuestion.value != "") { 
        var str = document.updateProfile.passwordQuestion.value; 
        if (!str.endsWith("?") && !str.includes("?")) {
            alert("A password question must include a '?' at the end of the question.");
            return false;
        }
    }
    return true;  // No error found
}
</script>
<!-- Create a cenrally located container -->
<div style="width:50%; margin:auto;">
<form name="updateProfile" method="post" onsubmit="return authenticateForm()">
    <div class="mb-3 row">
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Update Profile</span>
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="username">Name:</label>
        <div class="col-sm-9">
            <input class="form-control" name="username" id="username" 
                   type="text" maxlength="50" value="<?php echo $name; ?>" required />
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="birthdate">Date of Birth:</label>
        <div class="col-sm-9">
            <input class="form-control" name="birthdate" id="birthdate" type="date" value="<?php echo $dob; ?>" max="<?php echo $currentDate; ?>" />
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="address">Address:</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="address" id="address"
                      cols="25" rows="4" maxlength="150"><?php echo $address; ?></textarea>
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="country">Country:</label>
        <div class="col-sm-9">
            <input class="form-control" name="country" id="country" type="text" maxlength="50" value="<?php echo $country; ?>" />
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="telephone">Phone:</label>
        <div class="col-sm-9">
            <input class="form-control" name="telephone" id="telephone" type="text" maxlength="20" value="<?php echo $phone; ?>" />
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="userEmail">
         Email Address:</label>
        <div class="col-sm-9">
            <input class="form-control" name="userEmail" id="userEmail" 
                   type="email" maxlength="50" value="<?php echo $email; ?>" required /> 
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="pwd1">
         New Password:</label>
        <div class="col-sm-9">
            <input class="form-control" name="pwd1" id="pwd1" 
                   type="password" maxlength="50" required />
        </div>
    </div>
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="pwd2">
         Retype Password:</label>
        <div class="col-sm-9">
            <input class="form-control" name="pwd2" id="pwd2"
                   type="password" maxlength="50" required />
        </div>
    </div>
    <div class="mb-3 row">       
        <div class="col-sm-9 offset-sm-3">
            <br /><button class="submitbutton" type="submit">Update</button>
        </div>
    </div>
</form>

<?php
$checkEmail = true;
// Process after user click the submit button
if (isset($_POST["pwd1"]) && isset($_POST["pwd2"]) && isset($_POST["username"]) && isset($_POST["userEmail"])) {
    $username = $_POST["username"];
    $userdob = $_POST["birthdate"];
    $useraddress = $_POST["address"];
    $usercountry = $_POST["country"];
    $telephone = "(65) ".$_POST["telephone"];
    $useremail = $_POST["userEmail"];
    $password = $_POST["pwd1"];
    $qry1 = "SELECT Email FROM shopper WHERE Email!=?";
    $statement = $conn->prepare($qry1);
    $statement->bind_param("s", $email);
    $statement->execute();
    $result = $statement->get_result();
    while ($row = $result->fetch_array()) {
        if ($row["Email"] == $useremail) {
            $checkEmail = false;
        }
    }
    $statement->close();

    if ($checkEmail) {
        $qry2 = "UPDATE shopper SET Name=?, BirthDate=?, Address=?, Country=?, Phone=?, Email=?, 
                 Password=? 
                 WHERE ShopperID=?";
        $stmt = $conn->prepare($qry2);
        $stmt->bind_param("sssssssi", $username, $userdob, $useraddress, $usercountry, $telephone, $useremail, $password, $shopperId);
        $stmt->execute(); 
        $stmt->close();   
        echo "<br />";
        echo "<p><span style='color:red'>Your profile has been updated successfully.</span></p>";
        $_SESSION["ShopperName"] = $username;
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 3000);
                </script>";
    }

    else {
        echo "<br />";
        echo "<p><span style='color:red'>Email Address already exists in the system!</span></p>";
        $_SESSION["ShopperName"] = $name;
    }
}
?>

</div> <!-- Closing container -->
<?php 
$conn->close();
include("footer.php"); // Include the Page Layout footer
?>