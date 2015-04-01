<?php
$id = $_GET['id'];
// Ensure that an id was got
if (!isset($id)) {
	header("Location: search.php");
}

$default = "displayImage.php?id=$id&size=";
?>

<html>
	<head>
		<script>
			function zoompac(elmid) {
				var pic = document.getElementById(elmid);
				var imgsize = parseInt(pic.getAttribute('imgsize'));
				imgsize = 1 + imgsize%2
				pic.setAttribute('imgsize', imgsize);
				pic.src = pic.getAttribute('base') + "\&size=" + imgsize;
			}
		</script>
	</head>
	<body>
		<?php
		require("_database.php");
		
		$query = "SELECT record_id, patient_id, doctor_id, radiologist_id, ". 
			"test_type, prescribing_date, test_date, diagnosis, description ".
			"FROM radiology_record WHERE record_id = :id";
		
		$statement = oci_parse($connection, $query);
		
		oci_bind_by_name($statement, ":id", $id);
		
		echo $query;
		
		if (oci_execute($statement)) {
			if (oci_fetch($statement)) { ?>
				<h3><?php echo oci_result($statement, 'RECORD_ID'); ?></h3>
				<table>
					<tr>
						<td>Patient ID</td>
						<td><?php echo oci_result($statement, 'PATIENT_ID'); ?></td>
					</tr>
					<tr>
						<td>Doctor ID</td>
						<td><?php echo oci_result($statement, 'DOCTOR_ID'); ?></td>
					</tr>
					<tr>
						<td>Radiologist ID</td>
						<td><?php echo oci_result($statement, 'RADIOLOGIST_ID'); ?></td>
					</tr>
					<tr>
						<td>Test Type</td>
						<td><?php echo oci_result($statement, 'TEST_TYPE'); ?></td>
					</tr>
					<tr>
						<td>Prescribing Date</td>
						<td><?php echo oci_result($statement, 'PRESCRIBING_DATE'); ?></td>
					</tr>
					<tr>
						<td>Test Date</td>
						<td><?php echo oci_result($statement, 'TEST_DATE'); ?></td>
					</tr>
					<tr>
						<td>Diagnosis</td>
						<td><?php echo oci_result($statement, 'DIAGNOSIS'); ?></td>
					</tr>
					<tr>
						<td>Description</td>
						<td><?php echo oci_result($statement, 'DESCRIPTION'); ?></td>
					</tr>
				</table>
			<?php } else {
				echo "<p>No data to display</p>";
			} ?>
			
		<?php } else {
			echo "<p>  Could not execute query </p>";
		}
		oci_free_statement($statement);
		
		// Display all images associated with this record
		$query = "SELECT image_id FROM pacs_images WHERE record_id = :id";
		$statement = oci_parse($connection, $query);
		
		oci_bind_by_name($statement, ":id", $id);
		
		oci_execute($statement);
		
		while (oci_fetch($statement)) { 
			$index = oci_result($statement, 'IMAGE_ID');
			echo "<p><img id='pacimg$index' imgsize='1' base='displayImage.php?id=$index' ".
					"src='displayImage.php?id=$index' onclick=\"zoompac('pacimg$index')\" /></p>";
		}
		
		oci_free_statement($statement); 
		oci_close($connection);
		
		?>
	</body>
</html>
