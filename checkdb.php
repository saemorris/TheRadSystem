<?php

$username = "tmeleshk";
$password = "alpha1441BETA";
$connection_string = '//localhost:1525/crs';

$connection = oci_connect($username, $password, $connection_string);
if (!$connection) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	print 'Failed to connect';
}

$query = "SELECT * FROM users";
$statement = oci_parse($connection, $query);
if (!$statement) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	print 'failed to parse';
}

$results = oci_execute($statement);
if (!$results) {
    $e = oci_error();
    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	print 'failed to execute';
}

print '<h1>Checking db...</h1>\n';
print '<table border="1">\n';
while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURN_NULLS)) {
	print '<tr>\n';
	foreach ($row as $item) {
		print '<td>'. ($item == null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . '</td>\n';
	}
}
print '</table>\n';

oci_free_statement($statement);
oci_close($connection);



?>