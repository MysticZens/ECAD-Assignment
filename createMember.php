<?php
session_start(); // Detect the current session
// Read the data input from previous page
$name = $_POST["name"];
$dob = $_POST["dob"];
$address = $_POST["address"];
$country = $_POST["country"];
$phone = "(65) " . $_POST["phone"];
$email = $_POST["email"];
$password = $_POST["password"];
$pwdQuestion = $_POST["pwdQuestion"];
$pwdAnswer = $_POST["pwdAnswer"];
$dateEntered = new DateTime('now');
$formattedDate = $dateEntered->format('Y-m-d H:i:s');

if (empty($_POST["dob"])) {
    $dob = null;
}

// Include the PHP file that establishes database connection handle: $conn 
include_once("mysql_conn.php");

$checkEmail = true;
$qry1 = "SELECT Email FROM shopper";
$result = $conn->query($qry1);
while ($row = $result->fetch_array()) {
    if ($row["Email"] == $email) {
        $checkEmail = false;
    }
}
if ($checkEmail) {
    // Define the INSERT SQL statement
    $qry2 = "INSERT INTO Shopper (Name, BirthDate, Address, Country, Phone, Email, Password, PwdQuestion, PwdAnswer, ActiveStatus, DateEntered) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)";
    $stmt = $conn->prepare($qry2);
    // "ssssssssss" 10 string parameters
    $stmt->bind_param("ssssssssss", $name, $dob, $address, $country, $phone, $email, $password, $pwdQuestion, $pwdAnswer, $formattedDate);

    if ($stmt->execute()) { // SQL statement executed successfully
        // Retrieve the Shooper ID assigned to the new shopper
        $qry = "SELECT LAST_INSERT_ID() AS ShopperID";
        $result = $conn->query($qry); // Execute the SQL and get the returned result 
        while ($row = $result->fetch_array()) {
            $_SESSION["ShopperID"] = $row["ShopperID"];
        }

        // Successful message and Shopper ID
        $Message = "<p style='text-align: center'>Registration successful!<br />
                    Your Shopper ID is $_SESSION[ShopperID]<p>";
        // Save the Shopper Name in a session variable
        $_SESSION["ShopperName"] = $name;
    }

    else { // Error message
        $Message ="<h3 style='text-align: center; color:red'>Error in inserting record</h3>";
    }

    // Release the resource allocated for prepared statement
    $stmt->close();
}

else {
    $Message ="<h3 style='color: red; text-align: center'><b>Email Address already exists in the system!</b></h3>";
}

// Close database connection
$conn->close();

// Display Page Layout header with updated session state and links
include("header.php");
// Display message
echo $Message;
// Display Page Layout footer
include("footer.php");
?>