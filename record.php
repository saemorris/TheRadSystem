<?php
$id = $_GET['id'];
// Ensure that an id was got
if (!isset($id)) {
	header("Location: search.php");
}



?>