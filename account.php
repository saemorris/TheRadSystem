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
		
		<?php if (oci_fetch($statement)) {
			// Fetch data
			$firstname = oci_result($statement, "FIRST_NAME");
			$lastname = oci_result($statement, "LAST_NAME");
			$address = oci_result($statement, "ADDRESS");
			$email = oci_result($statement, "EMAIL");
			$phone = oci_result($statement, "PHONE");
			
			?>
			<table>
				<tr>
					<td class="fieldname">First Name: </td>
					<td class="data"><p><?php echo $firstname ?></p></td>
				</tr>
				<tr>
					<td class="fieldname">Last Name: </td>
					<td class="data"><p><?php echo $lastname ?></p></td>
				</tr>
				<tr>
					<td class="fieldname">Address: </td>
					<td class="data"><p><?php echo $address ?></p></td>
				</tr>
				<tr>
					<td class="fieldname">Email: </td>
					<td class="data"><p><?php echo $email ?></p></td>
				</tr>
				<tr>
					<td class="fieldname">Phone: </td>
					<td class="data"><p><?php echo $phone ?></p></td>
				</tr>
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