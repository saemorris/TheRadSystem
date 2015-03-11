<?php
session_start();
require('_sessioninfo.php');

function redirect() {
    header("Location: index.php");
    exit();
}

if (!isExpired()) {
    // Session is valid
    redirect();
}

$user = 'admin';
$pass = 'pass';
// Check for submission post
// http://www.formget.com/login-form-in-php/
if (isset($_POST['login'])) {
    // Missing fields
    if (empty($_POST['user']) || empty($_POST['pass'])) {
        $message="Invalid username/password";
        $msg_class='error';
    } else {
        // FIXME Valid credentials
        // database.select(user_name where user_name = $user and password = $pass)
        if ($_POST['user'] == $user && $_POST['pass'] == $pass) {
            // Credentials valid. Create info
            $_SESSION['_user_session'] = true;
            $_SESSION['us_created_time'] = time();
            $_SESSION['us_last_activity'] = time();
            
            // Redirect to home page
            redirect();
        } else {
            $message='Incorrect username/password';
            $msg_class='error';
        }
    }
} else {
    // Check if we received info about the error
    if (isset($_SESSION['err_message'])) {
        $message = $_SESSION['err_message'];
        $msg_class='error';
        unset($_SESSION['err_message']);
    } else {
        $message='Login is required.';
        $msg_class='normal';
    }
}
?>