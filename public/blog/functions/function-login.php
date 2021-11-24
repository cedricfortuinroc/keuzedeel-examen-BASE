<?php
session_start();

// Check if the user is already logged in, if yes then redirect him to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

// Include config file from the root directory
require_once "config/config.php";

// Set the variables to empty
$password = $username = "";
$password_err = $username_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username (firstname) is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "<div class='alert alert-warning text-center'><i class='fa fa-exclamation fa-fw'></i> Vul je gebruikersnaam in.</div>";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "<div class='alert alert-warning text-center'><i class='fa fa-exclamation fa-fw'></i> Vul je wachtwoord in.</div>";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        $sql = "SELECT USER_ID, USERNAME, USER_PASSWORD FROM users WHERE USERNAME = ?";

        if ($stmt = mysqli_prepare($ConnectionLink, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            // This creates cookies which make logging in and using the admin panel, working
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION['loggedin_time'] = time();

                            $addOnline = "UPDATE users SET USER_LAST_ONLINE = 'true' WHERE USERNAME = '" . $username . "'";
                            mysqli_query($ConnectionLink, $addOnline);

                            // Redirect user to welcome page
                            header("location: index.php");
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "<div class='alert alert-danger text-center'><i class='fa fa-exclamation-triangle fa-fw'></i> Gebruikersnaam of wachtwoord niet correct.</div>";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = "<div class='alert alert-danger text-center'><i class='fa fa-exclamation-triangle fa-fw'></i> Gebruikersnaam of wachtwoord niet correct.</div>";
                }
            } else {
                echo "<div class='alert alert-danger text-center'>Oops! Something went wrong. Please try again later. O_o</div>";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($ConnectionLink);
}
?>