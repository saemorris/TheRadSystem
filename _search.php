<?php
require ('session.php');

// check for submission post
if (isset($_POST['search'])) {
	// missing fields
	if (empty($_POST['keyWordQuery']) && empty($_POST['dateQueryFrom'])) {
		$message = "Invalid search query";
		$msg_class = 'error';
	} else {
		//require a connection to the database
		require ('_database.php');
		
		//check if date query
		if (empty($_POST['keyWordQuery'])) {
				
			$fromDate = $_POST['dateQueryFrom'];
			$toDate = $_POST['dateQueryTo'];
			
			$query="SELECT * FROM radiology_record";
			
			$statement = oci_parse($connection, $query);

			$results = oci_execute($statement);
	
			// Did we get a valid result?
			if ($results) {
				// display the results
				echo "<table border='1' cellspacing=0>";
				
				while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURNS_NULLS)) {
				
					echo "<tr>";
					
					foreach($row as $item) {
						//echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
						echo "<td>" . $item . "</td>";
					}

					echo "</tr>";
				}
				echo "</table>";
				

			} else {
				// No.
				// Inform the user
				$message = 'No records for date range';
				$msg_class = 'error';
			}

			// Clean up database objects
			oci_free_statement($statement);
			oci_close($connection);
		}
	}
}

?>
