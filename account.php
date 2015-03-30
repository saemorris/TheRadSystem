<?php require('session.php'); ?>

<html>
	<head>
		<style>
			table {
				border: 1px solid black;
			}
			tr {
				border: 1px solid black;
			}
			td.fieldname {
			}
			td.data {
				color: darkblue;
			}
		</style>
	</head>
	<body>
		<?php 
		require('_database.php');
		
		$query = "SELECT first_name, last_name, address, email, phone FROM persons WHERE person_id = '". getUserPersonID() ."'";
   
		$statement = oci_parse($connection, $query);
		oci_execute($statement);
		
		?>
		
		<?php if (oci_fetch($statement)) { ?>
			<table>
			<?php for ($i = 1; $i <= oci_num_fields($statement); $i++) { ?>
				<tr>
					<td class="fieldname"><?php echo oci_field_name($statement, $i)?></td>
					<td class="data"><?php echo oci_result($statement, oci_field_name($statement, $i)) ?></td>
				</tr>
				
			<?php } ?>
			</table>
			
		<?php } else {
			echo "<p>Database state error: Unable to find user info</p>";
		} ?>
		
		<?php
		oci_free_statement($statement);
		oci_close($connection);
		?>
		
		<p><a href="editinfo.php">Edit Account Info</a></p>
		<p><a href="changepassword.php">Change Password</a></p>
		<p><a href="logout.php">Log out</a></p>
	</body>
</html>