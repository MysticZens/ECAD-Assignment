<?php
session_start();
// Destroy the session
session_destroy();
// Redirect page back to index.php
header("Location: index.php");
exit();
?>
