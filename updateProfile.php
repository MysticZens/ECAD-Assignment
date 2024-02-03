<?php 
// Detect the current session
session_start(); 
// Include the Page Layout header
include("header.php"); 

// Include the PHP file that establishes database connection handle: $conn 
include_once("mysql_conn.php");

// Check if ShopperID session variable is not set
if (!isset($_SESSION["ShopperID"])) {
    // Redirect user to login.php page
    header("Location: login.php");
    exit();
}

// Retrieve ShopperID from the session
$shopperId = $_SESSION["ShopperID"];

// SQL query to select shopper details based on ShopperID
$qry = "SELECT * FROM Shopper WHERE ShopperID=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $shopperId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array();

// Extract shopper details from the fetched row
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
// Function to validate the registration form
function authenticateForm()
{
    // Check if password matched
	if (document.updateProfile.pwd1.value != document.updateProfile.pwd2.value) { 
        alert("Passwords not matched!");
        return false; // cancel submission
    }
	// Check if telephone number entered correctly
	// Singapore telephone number consists of 8 digits,
	// start with 6, 8, or 9
    if (document.updateProfile.telephone.value != "") { 
        var str = document.updateProfile.telephone.value; 
        if (str.length != 8) {
            alert("Please enter an 8-digit phone number."); 
            return false; // cancel submission
        }
        else if (str.substr(0,1) != "6" &&
                 str.substr(0,1) != "8" &&
                 str.substr(0,1) != "9" ) {
            alert("Phone number in Singapore should start with 6, 8, or 9.");
            return false; // cancel submission
        }
        str = "(65)" + str;
    }
    return true;
}

// Check if the eye icon has been clicked, then show the password
function togglePasswordVisibility() {
    var passwordInput = document.getElementById("pwd1");
    var eyeIcon = document.querySelector(".fa-regular .fa-eye");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    
    } else {
        passwordInput.type = "password";
    }
}

function toggle2PasswordVisibility() {
    var password2Input = document.getElementById("pwd2");
    var eyeIcon = document.querySelector(".fa-regular .fa-eye");

    if (password2Input.type === "password") {
        password2Input.type = "text";
    
    } else {
        password2Input.type = "password";
    }
}
</script>
<!-- Create a centrally located container -->
<div style="width:50%; margin:auto;">
<form name="updateProfile" method="post" onsubmit="return authenticateForm()">
    <!-- Title for the update profile section -->
    <div class="mb-3 row">
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Update Profile</span>
        </div>
    </div>
    <!-- Form field for updating name -->
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="username">Name:</label>
        <div class="col-sm-9">
            <input class="form-control" name="username" id="username" 
                   type="text" maxlength="50" value="<?php echo $name; ?>" required />(required)
        </div>
    </div>
    <!-- Form field for updating date of birth -->
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="birthdate">Date of Birth:</label>
        <div class="col-sm-9">
            <input class="form-control" name="birthdate" id="birthdate" type="date" value="<?php echo $dob; ?>" max="<?php echo $currentDate; ?>" />
        </div>
    </div>
    <!-- Form field for updating address -->
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="address">Address:</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="address" id="address"
                      cols="25" rows="4" maxlength="150"><?php echo $address; ?></textarea>
        </div>
    </div>
    <!-- Form field for updating country -->
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="country">Country:</label>
        <div class="col-sm-9">
            <input class="form-control" name="country" id="country" type="text" maxlength="50" value="<?php echo $country; ?>" />
        </div>
    </div>
    <!-- Form field for updating phone number -->
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="telephone">Phone:</label>
        <div class="col-sm-9">
            <input class="form-control" name="telephone" id="telephone" type="text" maxlength="20" value="<?php echo $phone; ?>" />
        </div>
    </div>
    <!-- Form field for updating email -->
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="userEmail">Email Address:</label>
        <div class="col-sm-9">
            <input class="form-control" name="userEmail" id="userEmail" 
                   type="email" maxlength="50" value="<?php echo $email; ?>" required />(required)
        </div>
    </div>
    <!-- Form field for updating new password -->
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="pwd1">New Password:</label>
        <div class="col-sm-9 password-container">
            <input class="form-control" name="pwd1" id="pwd1" 
                   type="password" maxlength="50" required />(required)   
            <i class="fa-regular fa-eye required" style="font-size: 18px" onclick="togglePasswordVisibility()"></i>
        </div>
    </div>
    <!-- Form field for retyping new password -->
    <div class="mb-3 row">
        <label class="col-sm-3 col-form-label" for="pwd2">Retype Password:</label>
        <div class="col-sm-9 password-container">
            <input class="form-control" name="pwd2" id="pwd2"
                   type="password" maxlength="50" required />(required)
            <i class="fa-regular fa-eye required" style="font-size: 18px" onclick="toggle2PasswordVisibility()"></i>
        </div>
    </div>
    <!-- Submission button for updating profile -->
    <div class="mb-3 row">       
        <div class="col-sm-9 offset-sm-3">
            <br /><button class="submitbutton" type="submit">Update</button>
        </div>
    </div>
</form>

<?php
$checkEmail = true; // Flag to check if the entered email is unique

// Process after the user clicks the submit button
if (isset($_POST["pwd1"]) && isset($_POST["pwd2"]) && isset($_POST["username"]) && isset($_POST["userEmail"])) {
    
    // Retrieve user input from the form
    $username = $_POST["username"];
    $userdob = $_POST["birthdate"];
    $useraddress = $_POST["address"];
    $usercountry = $_POST["country"];
    $telephone = "(65) ".$_POST["telephone"];
    $useremail = $_POST["userEmail"];
    $password = $_POST["pwd1"];

    // SQL query to check if the email already exists in the database
    $qry1 = "SELECT Email FROM shopper WHERE Email!=?";
    $statement = $conn->prepare($qry1);
    $statement->bind_param("s", $email);
    $statement->execute();
    $result = $statement->get_result();

    // Check if the entered email already exists
    while ($row = $result->fetch_array()) {
        if ($row["Email"] == $useremail) {
            $checkEmail = false;
        }
    }
    $statement->close();

    // If email is unique, update the user's profile
    if ($checkEmail) {
        // SQL query to update the shopper's profile
        $qry2 = "UPDATE shopper SET Name=?, BirthDate=?, Address=?, Country=?, Phone=?, Email=?, 
                 Password=? 
                 WHERE ShopperID=?";
        
        // Prepare and execute the update statement
        $stmt = $conn->prepare($qry2);
        $stmt->bind_param("sssssssi", $username, $userdob, $useraddress, $usercountry, $telephone, $useremail, $password, $shopperId);
        $stmt->execute(); 
        $stmt->close();   

        // Display success message
        echo "<br />";
        echo "<p><span style='color:red'>Your profile has been updated successfully.</span></p>";
        $_SESSION["ShopperName"] = $username;

        // Redirect to the index page after a delay
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 3000);
                </script>";
    }
    // If email already exists, display an error message
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
