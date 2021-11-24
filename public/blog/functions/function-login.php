<?php
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: ../index.php");
    exit;
}


// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
}
?>