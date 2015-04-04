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
		<p><a href='search.php'>Home</a></p>
		<?php
		require("_database.php");
		
		$query = "SELECT record_id, ". 
			"p.first_name p_first_name, p.last_name p_last_name, p.address p_address, p.email p_email, p.phone p_phone, ".
			"d.first_name d_first_name, d.last_name d_last_name, d.address d_address, d.email d_email, d.phone d_phone, ".
			"r.first_name r_first_name, r.last_name r_last_name, r.address r_address, r.email r_email, r.phone r_phone, ". 
			"test_type, prescribing_date, test_date, diagnosis, description ".
			"FROM radiology_record rec, persons p, persons d, persons r ".
			"WHERE	".
			"		record_id = :id AND ".
			"		rec.patient_id = p.person_id AND ".
			"		rec.doctor_id = d.person_id AND ".
			"		rec.radiologist_id = r.person_id";
		
		$statement = oci_parse($connection, $query);
		
		oci_bind_by_name($statement, ":id", $id);
		
		if (oci_execute($statement)) {
			if (oci_fetch($statement)) { ?>
				<h3>Record <?php echo oci_result($statement, 'RECORD_ID'); ?></h3>
				<table border="2">
					<tr>
						<td>Patient</td>
						<td><table border="1">
							<tr><td>First Name</td>	<td><?php echo oci_result($statement, 'P_FIRST_NAME'); ?></td></tr>
							<tr><td>Last Name</td>	<td><?php echo oci_result($statement, 'P_LAST_NAME'); ?></td></tr>
							<tr><td>Address</td>	<td><?php echo oci_result($statement, 'P_ADDRESS'); ?></td></tr>
							<tr><td>Email</td> 		<td><?php echo oci_result($statement, 'P_EMAIL'); ?></td></tr>
							<tr><td>Phone</td>  	<td><?php echo oci_result($statement, 'P_PHONE'); ?></td></tr>
						</table></td>
					</tr>
					<tr>
						<td>Doctor</td>
						<td><table border="1">
							<tr><td>First Name</td>	<td><?php echo oci_result($statement, 'D_FIRST_NAME'); ?></td></tr>
							<tr><td>Last Name</td>	<td><?php echo oci_result($statement, 'D_LAST_NAME'); ?></td></tr>
							<tr><td>Address</td>	<td><?php echo oci_result($statement, 'D_ADDRESS'); ?></td></tr>
							<tr><td>Email</td> 		<td><?php echo oci_result($statement, 'D_EMAIL'); ?></td></tr>
							<tr><td>Phone</td>  	<td><?php echo oci_result($statement, 'D_PHONE'); ?></td></tr>
						</table></td>
					</tr>
					<tr>
						<td>Radiologist</td>
						<td><table border="1">
							<tr><td>First Name</td>	<td><?php echo oci_result($statement, 'R_FIRST_NAME'); ?></td></tr>
							<tr><td>Last Name</td>	<td><?php echo oci_result($statement, 'R_LAST_NAME'); ?></td></tr>
							<tr><td>Address</td>	<td><?php echo oci_result($statement, 'R_ADDRESS'); ?></td></tr>
							<tr><td>Email</td> 		<td><?php echo oci_result($statement, 'R_EMAIL'); ?></td></tr>
							<tr><td>Phone</td>  	<td><?php echo oci_result($statement, 'R_PHONE'); ?></td></tr>
						</table></td>
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
					"src='displayImage.php?id=$index' onclick=\"zoompac('pacimg$index')\" /></p>\n";
		}
		
		oci_free_statement($statement); 
		oci_close($connection);
		
		?>
		<a href="uploadImage.php?record_id=<?php echo $id?>">Upload New Image</a>
	</body>
</html>
