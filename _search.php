<?php
// check for submission post
if (isset($_POST['search'])) {
	// missing fields
	if (empty($_POST['keyWordQuery']) && empty($_POST['dateQueryFrom'])) {
		$message = "Invalid search query";
		$msg_class = 'error';
	} else {
		//require a connection to the database
		require ('_database.php');

		//get the class of the user
		$class = getUserClass();
		$personId = getUserPersonID();

		//check if date query
		if (empty($_POST['keyWordQuery'])) {

			// query the database for records in the specified date range
			$startDate = $_POST['dateQueryFrom'];
			$endDate = $_POST['dateQueryTo'];
			$query = "SELECT i.image_id, r.record_id, r.patient_id, p.first_name, p.last_name, r.doctor_id, r.radiologist_id,
				r.test_type, r.prescribing_date, r.test_date, r.diagnosis, r.description	FROM family_doctor fd, radiology_record r, 
				persons p, pacs_images i WHERE r.patient_id = p.person_id AND r.record_id = i.record_id AND 
				((prescribing_date BETWEEN to_date('$startDate', 'YYYY-MM-DD') AND to_date('$endDate', 'YYYY-MM-DD')) 
				OR (test_date BETWEEN to_date('$startDate', 'YYYY-MM-DD') AND to_date('$endDate', 'YYYY-MM-DD'))) ";
				
			// patient can only view his/her records
			if ($class == "p") {
				$query .= "AND p.person_id = $personId ";
			} else if ($class == "d") {
				// doctor can only view records of their patients
				$query .= "AND fd.doctor_id = $personId AND fd.doctor_id = r.doctor_id ";
			} else if ($class == "r") {
				// radiologist can only view records of tests conducted by oneself
				$query .= "AND r.radiologist_id = $personId ";
			}	

			//display what was searched for
			echo "<b>Search results for: </b>";
			echo $startDate . " to " . $endDate;
			echo "<p>";

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

					echo "<th>$column_name</th>";
				}
				echo "</tr>";

				
				// display the rows found for the specified date range
				while ($row = oci_fetch_array($statement, OCI_ASSOC + OCI_RETURNS_NULLS)) {
					$isFirst = True;
					echo "<tr>";
					foreach ($row as $item) {
						if ($isFirst) {
							echo "<td> <img src='displayImage.php?size=0&id=$item' /> </td>";	
							$isFirst= False;	
						} else {
						
							echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>";
						}
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
			$wordArray = array();

			//display what was searched for
			echo "<b>Search results for: </b>";
			foreach ($words as $word) {
				echo " " . $word;
			}
			echo "<p>";

			// display the headers of the table
			echo "<table border='1' cellspacing=0>";

			// display column headers
			echo "<tr>";
			echo "<th>Rank</th>";
			echo "<th>Thumbnail</th>";
			echo "<th>Record Id</th>";
			echo "<th>Patient Id</th>";
			echo "<th>First Name</th>";
			echo "<th>Last Name</th>";
			echo "<th>Doctor Id</th>";
			echo "<th>Radiologist Id</th>";
			echo "<th>Test Type</th>";
			echo "<th>Prescribing Date</th>";
			echo "<th>Test Date</th>";
			echo "<th>Diagnosis</th>";
			echo "<th>Description</th>";
			echo "</tr>";

			$query = "i.image_id, r.record_id, r.patient_id, p.first_name, p.last_name, r.doctor_id, 
								r.radiologist_id, r.test_type, r.prescribing_date, r.test_date, r.diagnosis, r.description 
								FROM (radiology_record r inner join persons p on r.patient_id = p.person_id)
									 left join pacs_images i on r.record_id = i.record_id, 
									 family_doctor fd 
								WHERE ";
			// patient can only view his/her records
			if ($class == "p") {
				$query .= "p.person_id = $personId AND (";
			} else if ($class == "d") {
				// doctor can only view records of their patients
				$query .= "fd.doctor_id = $personId AND fd.doctor_id = r.doctor_id AND (";
			} else if ($class == "r") {
				// radiologist can only view records of tests conducted by oneself
				$query .= "r.radiologist_id = $personId AND (";
			} else {
				$query .= "(";
			}

			$i = 1;
			$descFreq = "";
			$diagnosisFreq = "";
			$nameFreq = "";
			// add the contains clauses to the query
			foreach ($words as $word) {
				$query .= "CONTAINS(r.description, '$word', $i) > 0 OR ";
				$descFreq .= "SCORE($i) + ";
				$i = $i + 1;
				$query .= "CONTAINS(r.diagnosis, '$word', $i) > 0 OR ";
				$diagnosisFreq .= "SCORE ($i) + ";
				$i = $i + 1;
				$query .= "CONTAINS(p.first_name, '$word', $i) > 0 OR ";
				$nameFreq .= "SCORE($i) + ";
				$i = $i + 1;
				$query .= "CONTAINS(p.last_name, '$word', $i) > 0 OR ";
				$nameFreq .= "Score($i) + ";
				$i = $i + 1;
				$query .= "CONTAINS(r.test_type, '$word', $i) > 0 OR ";
				$i = $i + 1; 
			}
			//strip extra OR's and +' from the strings
			$query = substr_replace($query, '', -3, 2);
			$descFreq  = substr_replace($descFreq, '', -2, 2);
			$diagnosisFreq  = substr_replace($diagnosisFreq, '', -2, 2);
			$nameFreq  = substr_replace($nameFreq, '', -2, 2);

			$query .=")";
			
			//Order the results by the rank
			$query = "SELECT * FROM (SELECT DISTINCT 6*($nameFreq) + 3*($diagnosisFreq) + 3*($descFreq) as rank, $query)
				 ORDER BY rank DESC"; 
			
			$statement = oci_parse($connection, $query);

			$results = oci_execute($statement);

			// display the rows found with matching keywords
			$num_fields = -1;
			while ($row = oci_fetch_array($statement, OCI_ASSOC + OCI_RETURN_NULLS)) {
				$field = 0;
				echo "<tr>";
				foreach ($row as $item) {
					if ($field==1) {
							if ($item === null) {
								echo '<td></td>';
							} else {
								echo "<td> <img src='displayImage.php?size=0&id=$item' /> </td>\n";	
							}	
					} else {
						echo "<td>" . ($item !== null ? htmlentities($item, ENT_QUOTES) : "&nbsp;") . "</td>\n";
					}
					$field++;
				}
				echo "</tr>";
			}
			echo "</table>";
		}
	}
}
?>
