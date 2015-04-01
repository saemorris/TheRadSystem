<?php
$id = $_GET['id'];
$size = $_GET['size'];

// Size parameter must be 0 (thumbnail), 1 (regular), 2 (full size)
if (!isset($size) || !(0 <= $size && $size <= 2)) {
	$size = 1;
}

require ('_database.php');
// Fetch the requested size
if ($size == 0) {
	$query = "Select thumbnail from pacs_images where image_id = :id";
} else if ($size == 1) {
	$query = "Select regular_size from pacs_images where image_id = :id";
} else if ($size == 2) {
	$query = "Select full_size from pacs_images where image_id = :id";
}

$statement = oci_parse($connection, $query);
oci_bind_by_name($statement, ':id', $id);
$result = oci_execute($statement);

// Check if there was a result
if ($result) {
	$row_data = oci_fetch_row($statement);
	
	$image = $row_data[0] -> load();
	header("Content-type: image/jpeg");
	echo $image;
}
 else {
	header("Content-type: image/png");
	include('noimage.png');
}

oci_free_statement($statement);
oci_close($connection);

?>
