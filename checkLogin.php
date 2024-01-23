<?php
// Detect the current session 
session_start();

// Reading inputs entered in previous page 
$email = $_POST["email"];
$pwd = $_POST["password"];

// To Do 1 (Practical 2): Validate login credentials with database
// Include the PHP file that establishes database connection handle: $conn 
include_once("mysql_conn.php");
$qry = "SELECT * FROM Shopper WHERE Email=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("s", $email);
$stmt->execute();
$result1 = $stmt->get_result();
$stmt->close();
if ($result1->num_rows > 0) {
    $row1 = $result1->fetch_array();
    // Get the hashed password from database
    // Save user's info in session variables
    $_SESSION["ShopperName"] = $row1["Name"];
    $_SESSION["ShopperID"] = $row1["ShopperID"];
    $shopperId = $_SESSION["ShopperID"];
    $qry = "SELECT sc.ShopCartID, COUNT(sci.ProductID) AS NumItems FROM ShopCart sc LEFT JOIN ShopCartItem sci 
    ON sc.ShopCartID=sci.ShopCartID WHERE sc.ShopperID=$shopperId AND sc.OrderPlaced=0";
    $stmt = $conn->prepare($qry);
    $stmt->execute();
    $result1 = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row1 = $result1->fetch_assoc();
        $_SESSION["Cart"] = $row["ShopCartID"];
        $_SESSION["NumCartItem"] = $row["NumItems"];
    } else {
        // Handle the case where no records are found
        $_SESSION["Cart"] = null;
        $_SESSION["NumCartItem"] = 0;
    }
    header("Location: index.php");
    exit();
}

else {
    include_once("header.php");
    echo "<h3 style='color: red; text-align: center'><b>Authentication failed! You are an invalid user!</b></h3>";
    include_once("footer.php");
}
?>
