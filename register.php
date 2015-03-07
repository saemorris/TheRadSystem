<body>
	<h1>Executing query...</h1>
	<?php
	include('connectDB.php');
	include('printTable.php');
	$conn = connect();
	echo '<table border="1">';
	odb_select($conn, "SELECT * FROM USERS", show_table);
	echo "</table>";
	oci_close($conn);
	

	if (isset($_POST['register'])) {
		$user_name = $_POST['user_name'];
		$password = $_POST['password'];
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$address = $_POST['address'];
		$email = $_POST['email'];
		$phone = $_POST['phone'];

		// Change error display settings
		ini_set('display_errors', 1);
		error_reporting(E_ALL);
		
		// Establish connection
		$conn = connect();
		if (!$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES),
				E_USER_ERROR);
        }

		$sql = "SELECT MAX(person_id)+1 as nextid FROM persons";
		$statement = oci_parse($conn, $sql);
		oci_execute($statement);
		
		if (($row = oci_fetch_row($statement)) == false) {
			$pid = 1;
		} else {
			$pid = $row[0];
		}

		oci_free_statement($statement);

        $sql = "INSERT INTO persons VALUES ($pid, '$first_name', '$last_name', '$address', '$email', '$phone')";

		$err = odb_execute($conn, $sql);
		if ($err) {
			echo $err;
			odb_execute($conn, "ROLLBACK");
			oci_close($conn);
			return;
		}
		$sql = "INSERT INTO users VALUES ('$user_name', '$password', 'a', $pid, sysdate)";
		$err= odb_execute($conn, $sql);
		if ($err) {
			echo $err;
			odb_execute($conn, "ROLLBACK");
			oci_close($conn);
			return;
		}

		odb_execute($conn, "COMMIT");
		oci_close($conn);
		echo "<p>Insert data sucessfully.</p>";
	} else {
		echo "<p>Error: No known request</p>";
	}
	?>
</body>
