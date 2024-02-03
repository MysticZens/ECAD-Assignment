<?php 
// Detect the current session
session_start(); 
// Include the Page Layout header
include("header.php"); 
$currentDate = date('Y-m-d');
?>
<script type="text/javascript">
// Function to validate the registration form
function validateForm()
{
    // Check if password matched
	if (document.register.password.value != document.register.password2.value) { 
        alert("Passwords not matched!");
        return false; // cancel submission
    }
	// Check if telephone number entered correctly
	// Singapore telephone number consists of 8 digits,
	// start with 6, 8, or 9
    if (document.register.phone.value != "") { 
        var str = document.register.phone.value; 
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
    }

    // Check if a password question is entered correctly
    if (document.register.pwdQuestion.value != "") { 
        var str = document.register.pwdQuestion.value; 
        if (!str.endsWith("?") && !str.includes("?")) {
            alert("A password question must include a '?' at the end of the question.");
            return false;
        }
    }
    return true;  // No error found
}

function togglePasswordVisibility() {
    var passwordInput = document.getElementById("password");
    var eyeIcon = document.querySelector(".fa-regular .fa-eye");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
    
    } else {
        passwordInput.type = "password";
    }
}

function toggle2PasswordVisibility() {
    var password2Input = document.getElementById("password2");
    var eyeIcon = document.querySelector(".fa-regular .fa-eye");

    if (password2Input.type === "password") {
        password2Input.type = "text";
    
    } else {
        password2Input.type = "password";
    }
}
</script>

<!-- This section contains the form for user registration -->
<div style="width:50%; margin:auto;">
    <form name="register" action="createMember.php" method="post" onsubmit="return validateForm()">
        <!-- Section for page title -->
        <div class="mb-3 row">
            <div class="col-sm-9 offset-sm-3">
                <span class="page-title">Register as a Member</span>
            </div>
        </div>

        <!-- Input fields for user details -->
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="name">Name:</label>
            <div class="col-sm-9">
                <input class="form-control" name="name" id="name" type="text" maxlength="50" required /> (required)
            </div>
        </div>
        <!-- Date of Birth -->
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="dob">Date of Birth:</label>
            <div class="col-sm-9">
                <input class="form-control" name="dob" id="dob" type="date" max="<?php echo $currentDate; ?>" />
            </div>
        </div>
        <!-- Address -->
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="address">Address:</label>
            <div class="col-sm-9">
                <textarea class="form-control" name="address" id="address" cols="25" rows="4" maxlength="150"></textarea>
            </div>
        </div>
        <!-- Country -->
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="country">Country:</label>
            <div class="col-sm-9">
                <input class="form-control" name="country" id="country" type="text" maxlength="50" />
            </div>
        </div>
        <!-- Phone -->
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="phone">Phone:</label>
            <div class="col-sm-9">
                <input class="form-control" name="phone" id="phone" type="text" maxlength="20" 
                       placeholder="Allow only Singapore registered phone numbers." />
            </div>
        </div>
        <!-- Email Address -->
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="email">Email Address:</label>
            <div class="col-sm-9">
                <input class="form-control" name="email" id="email" type="email" maxlength="50" required /> (required)
            </div>
        </div>
        <!-- Password -->
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="password">Password:</label>
            <div class="col-sm-9 password-container">
                <input class="form-control" name="password" id="password" type="password" maxlength="50" required /> (required)
                <i class="fa-regular fa-eye required" style="font-size: 18px" onclick="togglePasswordVisibility()"></i>
            </div>
        </div>
        <!-- Retype Password -->
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="password2">Retype Password:</label>
            <div class="col-sm-9 password-container">
                <input class="form-control" name="password2" id="password2" type="password" maxlength="50" required /> (required)
                <i class="fa-regular fa-eye required" style="font-size: 18px" onclick="togglePassword2Visibility()"></i>
            </div>
        </div>
        <!-- Password Question -->
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="pwdQuestion">Password Question:</label>
            <div class="col-sm-9">
                <input class="form-control" name="pwdQuestion" id="pwdQuestion" type="text" maxlength="100" 
                       placeholder="Please enter the security password question!" /> 
                       (Enter this security password question only once)
            </div>
        </div>
        <!-- Password Answer -->
        <div class="mb-3 row">
            <label class="col-sm-3 col-form-label" for="pwdAnswer">Password Answer:</label>
            <div class="col-sm-9">
                <input class="form-control" name="pwdAnswer" id="pwdAnswer" type="text" maxlength="50" 
                       placeholder="Please enter the security password answer!" /> 
                       (Enter this security password answer only once)
            </div>
        </div>
        <!-- Submit button -->
        <div class="mb-3 row">       
            <div class="col-sm-9 offset-sm-3">
                <button class="submitbutton" type="submit">Sign Up</button>
            </div>
        </div>
    </form>
</div>

<?php 
// Include the Page Layout footer
include("footer.php"); 
?>
