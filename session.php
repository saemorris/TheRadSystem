<?php
session_start();
require('_sessioninfo.php');

/* 
* Use require('session.php')) if a page requires authentication.
*/

// If session is expired, go to login page
if (!isset($_SESSION['_user_session'])) {
    unset($_SESSION['err_message']);
    header("Location: login.php");
    exit();
    
} else if (isExpired()) {
    $_SESSION['err_message'] = "Session expired.";
    header("Location: login.php");
    unset($user_session);
    exit();
}

// Session is definitely valid if we're here. Refresh its expiry time.
updateActiveTime();

// Expose some useful functions for fellow programmers


?>
