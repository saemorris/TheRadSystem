<html>
	<head>
		<title>Generate OLAP Report</title>
	</head>
<body>
	<form action="http://consort.cs.ualberta.ca/~vanbelle/TheRadSystem/OLAP_report.php" method="post">
		<table>
		<select name="time_period">
			<option value="All">All</option>
			<option value="Year">Year</option>
			<option value="Month">Month</option>
			<option value="Week">Week</option>
		</select>
		</table>
	
<?php

if (isset($_POST['request'])) {
	//get a database connection 
	require_once('_database.php');
	
	$query = "CREATE TABLE FactTable AS (SELECT p.first_name AS fn, p.last_name AS ln, r.test_type AS tt, r.test_date AS td, COUNT(DISTINCT i.image_id) AS n 
	FROM persons p, radiology_record r, pacs_images i WHERE i.record_id  = r.record_id AND p.person_id = r.patient_id
	GROUP BY p.first_Name, p.last_name, r.test_type, r.test_date, i.image_id)";
	
	//get a database response
	$statement = oci_parse($connection, $query);
	$results = oci_execute($statement);
	
	//TODO

	$query = "DROP TABLE FactTable";
	
	//get a database response
	$statement = oci_parse($connection, $query);
	$results = oci_execute($statement);
	
	//close database connection?
	oci_free_statement($statement);
	oci_close($connection);
}
	
?>
</form>
</body>
</html>