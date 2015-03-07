<?php
include('cookiedata.php');
// 86400 = 1 day
setcookie($cookie_name, $cookie_value, time() + (30), "/");
echo "Made cookie.";
?>
