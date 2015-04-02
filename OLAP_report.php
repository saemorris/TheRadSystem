<!DOCTYPE html>
<html>
	<head>
		<title>Request an OLAP Report</title>
	</head>
	<body>
		<form id="olapRequest" action="OLAP_report.php" method="post">
			<div>
				<p class="header">
					<b>Data Analysis Request</b>
					<br/>
				</p>
			</div>
			<table>
				<tr>
					<td class="prompt_field">Patient:</td>
					<td class="field">
					<input type="text" name="patient" value="All"/>
					</td>
				</tr>
				<tr>
					<td class="prompt_field">Test Type:</td>
					<td class="field">
					<input type="text" name="test" value="All" />
					</td>
				</tr>
			</table>
			<select name="time_period">
				<option value="All">All</option>
				<option value="Year">Year</option>
				<option value="Month">Month</option>
				<option value="Week">Week</option>
			</select>
			<table>
				<tr>
					<td class="prompt_field">Year:</td>
					<td class="field">
					<input type="text" name="year/>
					</td>
				</tr>
				<tr>
					<td class="prompt_field">Month:</td>
					<td class="field">
					<input type="text" name="month" />
					</td>
				</tr>
			</table>

			<div id="submit">
				<input type="submit" name="orequest" value="Submit"/>
			</div>
		</form>

		<?php
			require ('session.php');
			if (getUserClass() != "a") {
				echo 'No Access To This Page';?>
				<p>
					<a href="search.php">Home</a>
				</p>
		<?php
			} else {
				if (isset($_POST['orequest'])) {
					$patient = $_POST['patient'];
					$test = $_POST['test'];
					$time = $_POST['time_period'];
					$AllTests = array();
					$AllPatients = array();
					$PatientIds = array();
					
					//get a database connection
					require_once ('_database.php');
					
					// //get all possible tests
					// $query = "DROP TABLE joined";					
					// //get a database response
					// $statement = oci_parse($connection, $query);
					// $results = oci_execute($statement);
					// if (!$results) {
						// echo "Could not get database query 0<br />";
						// }
										
					//get all possible tests
					$query = "SELECT distinct test_type FROM radiology_record";
					
					//get a database response
					$statement = oci_parse($connection, $query);
					$results = oci_execute($statement);
					
					//put the tests in an array
					if ($results) {
						while (($row = oci_fetch_array($statement, OCI_BOTH)) != false) {
							$AllTests[] = $row[0];
						}
					} else {
						echo "Could not get database query 1<br />";
					}
					
					//get all possible patients
					$query = "SELECT distinct first_name, last_name, person_id FROM persons";
					
					//get a database response
					$statement = oci_parse($connection, $query);
					$results = oci_execute($statement);
					
					//put the tests in an array
					if ($results) {
						while (($row = oci_fetch_array($statement, OCI_BOTH)) != false) {
							$AllPatients[] = $row[0].' '.$row[1];
							$PatientIds[] = $row[2];
						}
					} else {
						echo "Could not get database query 2<br />";
					}
					
					//get all possible tests
					$query = "CREATE TABLE joined AS (SELECT p.person_id AS pid, r.test_type AS test, COUNT(DISTINCT i.image_id) AS images FROM persons p, radiology_record r, pacs_images i 
					WHERE p.person_id = r.patient_id AND r.record_id = i.record_id GROUP BY p.person_id, r.test_type)";					
					//get a database response
					$statement = oci_parse($connection, $query);
					$results = oci_execute($statement);
					
					if(!$results) {
						echo "Could not get database query 3<br />";
					}
					
					if($time == 'All'&& $patient == 'All' && $test == 'All'){
						echo '<table align="left"
							border = "1"
							cellspace="5" cellpadding="8">
							<tr><td align="left"><b>Patient       </b></td>';
						foreach($AllTests as $test_type){
							echo '<td align="left"><b>' . $test_type . '</b></td>';
						}
						echo '</tr>';
						
						for ($int = 0; $int < count($AllPatients); $int++){
							echo '<td align="left">'.$AllPatients[$int].'</td>';
							 foreach($AllTests as $test_type){
								//the corresponding counts
								$query = "SELECT images FROM joined WHERE pid = '$PatientIds[$int]' AND test = '$test_type'";
								
								//get a database response
								$statement = oci_parse($connection, $query);
								$results = oci_execute($statement);
								
								//put the count in the table
								$row = oci_fetch_array($statement, OCI_BOTH);
								if ($results) {
									if($row == false){
										echo '<td align="left"> 0 </td>';
									} else {
										echo '<td align="left">' . $row[0] . '</td>';
									}
									
								} else {
									echo "Could not get database query 2<br />";
								}
							}	
							echo '</tr>';
						}
						
					

					} else if($time != 'All'&& $patient  == 'All' && $test == 'All'){
						//TODO
					} else if($time == 'All'&& $patient  != 'All' && $test == 'All'){
						echo '<b>For Patient'.$patient.'</b><br/>';
						//TODO
					} else if($time == 'All'&& $patient == 'All' && $test != 'All'){
						//TODO
					} else if($time != 'All'&& $patient  != 'All' && $test == 'All'){
						//TODO
					} else if($time != 'All'&& $patient  == 'All' && $test != 'All'){
						//TODO
					} else if($time == 'All'&& $patient  != 'All' && $test != 'All'){
						//TODO
					} else if($time != 'All'&& $patient != 'All' && $test != 'All'){
						//TODO
					}
					
					//get all possible tests
					$query = "DROP TABLE joined";					
					//get a database response
					$statement = oci_parse($connection, $query);
					$results = oci_execute($statement);
				}
				
			}
	?>
	</body>
	</html>