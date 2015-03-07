<?php
include("password.php");
function connect() {
	list($oid, $pass)  = getCredentials();
    $conn = oci_connect($oid, $pass);
    if (!$conn) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
    }       
            
    return $conn;
}

function odb_execute($conn, $sql) {
	// Prepare sql using conn and returns the statement identifier
	$statement = oci_parse($conn, $sql);
	echo "<p><b>$sql</b></p>";

	// Execute a statement returned from oci_parse()
	$res=oci_execute($statement);

	// Check result
	if (!$res) {
		$err = oci_error($statement); 
		return htmlentities($err['message']);
	} else {
		return null;
	}
	oci_free_statement($statement);
}

function odb_select($conn, $sql, $action, $max=10) {
	$statement = oci_parse($conn, $sql);
	echo "<p><b>$sql</b></p>";

	// Execute a statement returned from oci_parse()
	$res=oci_execute($statement);

	// Check result
	if (!$res) {
		$err = oci_error($statement); 
		return htmlentities($err['message']);
	}

	// Print result
	$count = 0;
	while ($count < $max && ($row=oci_fetch_array($statement, OCI_ASSOC))) {
		$action( $row );
	}
	return false;
}
?>

