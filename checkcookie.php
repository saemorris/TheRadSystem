<?php
include('cookiedata.php');
if (!isset($_COOKIE[$cookie_name])) {
	echo "Cookie not found. Creating cookie...";
	include('makecookie.php');
} else {
	echo "<p>Cookie '$cookie_name' is set!<br/>";
	echo "Value is: '$cookie_value'.</p>";
	echo "<p>Deleting cookie...";
	unset($_COOKIE[$cookie_name]);
	setcookie($cookie_name, null, -1, "/");
}
?>
