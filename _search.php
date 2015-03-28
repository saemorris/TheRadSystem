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
			
			// query the database for records in the specified date range	
			$startDate = $_POST['dateQueryFrom'];
			$endDate = $_POST['dateQueryTo'];
			$query="SELECT * FROM radiology_record WHERE prescribing_date BETWEEN to_date('$startDate', 'YYYY-MM-DD') and to_date('$endDate', 'YYYY-MM-DD')
				OR test_date BETWEEN to_date('$startDate', 'YYYY-MM-DD') and to_date('$endDate', 'YYYY-MM-DD')";
			
			$statement = oci_parse($connection, $query);

			$results = oci_execute($statement);
			
			// Did we get a valid result?
			if ($results) {
				// display the results
				echo "<table border='1' cellspacing=0>";
				
				// get the number of fields in the table
				$numCols = oci_num_fields($statement);
				
				// display column headers
				echo "<tr>";
				for ($i = 1; $i <= $numCols; $i++) {
					$column_name = oci_field_name($statement, $i);
					
					echo"<th>$column_name</th>";
				}
				echo "</tr>";
				
				// display the rows found for the specified date range
				while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURNS_NULLS)) {
				
					echo "<tr>";
					foreach($row as $item) {
						echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
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
			
		} else {
			// key word search
			$string = $_POST['keyWordQuery'];
			$words = explode(" ", $string);
			$wordArray=array();

			//display what was searched for
			echo "<b>Search results for: </b>"; 
			foreach($words as $word) {
				echo " " . $word;
			}
			echo "<p>";
			
			$query="SELECT * FROM radiology_record";
			
			$statement = oci_parse($connection, $query);
			
			$results = oci_execute($statement);
	
			// get the number of fields in the table
			$numCols = oci_num_fields($statement);
			
			// display the results
			echo "<table border='1' cellspacing=0>";
				
			// display column headers
			echo "<tr>";
			for ($i = 1; $i <= $numCols; $i++) {
				$column_name = oci_field_name($statement, $i);
					
				echo"<th>$column_name</th>";
			}
			echo "</tr>";
			
			foreach ($words as $word) {
				$query="SELECT * FROM radiology_record WHERE CONTAINS(description, '$word', 1) > 0 OR CONTAINS(diagnosis, '$word', 2) > 0";
				
				$statement = oci_parse($connection, $query);
	
				$results = oci_execute($statement);
				
				// display the rows found with matching keywords
				while ($row = oci_fetch_array($statement, OCI_ASSOC+OCI_RETURNS_NULLS)) {
					echo "<tr>";
					foreach($row as $item) {
						echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
					}
					echo "</tr>";
				}
			}
			echo "</table>";
			
		}
	}
}

?>
