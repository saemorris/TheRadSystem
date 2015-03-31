<?php
$id = $_GET['id'];

require ('_database.php');
$query = "Select full_size from pacs_images where image_id = :id";

$statement = oci_parse($connection, $query);
oci_bind_by_name($statement, ':id', $id);
$result = oci_execute($statement);

$row_data = oci_fetch_row($statement);

$image = $row_data[0] -> load();
header("Content-type: image/jpeg");
echo $image;
?>
