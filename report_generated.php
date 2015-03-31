<html>
	<head>
		<title>Generate Report</title>
	</head>
	<body>
		<form action="http://consort.cs.ualberta.ca/~vanbelle/TheRadSystem/report_generated.php" method="post">

			<?php
			//http://www.newthinktank.com/2014/09/php-mysql-tutorial/ 2015/03/25
			require ('session.php');
			if (getUserClass() != "a") {
				echo 'No Access To This Page';
				?> <p><a href="search.php">Home</a></p>
				<?php
			} else {

				if (isset($_POST['request'])) {
					$data_missing = array();
					echo '<b>Diagnosis Report</b> <br />';
					if (empty($_POST['diagnosis'])) {
						$data_missing[] = 'diagnosis';
					} else {
						$diagnosis = trim($_POST['diagnosis']);
					}

					if (empty($_POST['year'])) {
						$data_missing[] = 'Year';
					} else {
						$year = trim($_POST['year']);
					}

					if (empty($data_missing)) {
						//get a database connection
						require_once ('_database.php');

						//creaate the query to get back the report results
						$query = "SELECT p.first_name, p.last_name, p.address, p.phone, r.test_date FROM persons p, radiology_record r WHERE p.person_id = r.patient_id AND r.diagnosis = '$diagnosis' AND '$year' = (EXTRACT (YEAR FROM r.test_date))";

						//get a database response
						$statement = oci_parse($connection, $query);
						$results = oci_execute($statement);

						//validate results then print table headers
						if ($results) {
							echo '<table align="left"
							cellspace="5" cellpadding="8">

							<tr><td align="left"><b>First Name</b></td>
							<td align="left"><b>Last Name</b></td>
							<td align="left"><b>Address</b></td>
							<td align="left"><b>Phone Number</b></td>
							<td align="left"><b>Testing Date</b></td></tr>';

							//fetch rows and print them one at a time
							while (($row = oci_fetch_array($statement, OCI_BOTH)) != false) {

								echo '<tr><td align="left">' . $row[0] . '</td><td align="left">' . $row[1] . '</td><td align="left">' . $row[2] . '</td><td align="left">' . $row[3] . '</td><td align="left">' . $row[4] . '</td><td align="left">';

								echo '</tr>';
							}
							echo '</table>';

							//if results returned was 0 print error message
						} else {
							echo "Could not get database query<br />";
						}

						//close database connection?
						oci_free_statement($statement);
						oci_close($connection);

					} else {
						echo 'Missing Data: ';
						foreach ($data_missing as $missing) {
							echo '$missing<br />';
						}
					}
				}
			}
			?>
		</form>
	</body>
</html>
