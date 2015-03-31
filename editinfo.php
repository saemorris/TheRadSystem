<?php
require ('session.php');
 ?>

<?php
if (isset($_POST['changeinfo'])) {
	// Ensure that the post data exists
	if (!(isset($_POST['First_Name']) && isset($_POST['Last_Name']) && isset($_POST['Address']) && isset($_POST['Email']) && isset($_POST['Phone']) && isset($_POST['Password']))) {

		$error_msg = "Not enough data supplied";

	} else {
		require ("_database.php");

		$query = "SELECT person_id FROM users WHERE user_name = :username AND password = :password";

		$statement = oci_parse($connection, $query);

		oci_bind_by_name($statement, ":password", $_POST['Password']);
		oci_bind_by_name($statement, ":username", getUserName());

		oci_execute($statement);

		// Check that their credentials match up
		if (oci_fetch($statement) and oci_result($statement, "PERSON_ID") == getUserPersonID()) {
			oci_free_statement($statement);

			// Update their info
			$query = "UPDATE persons SET first_name = :firstname, last_name = :lastname, address = :address, email = :email, phone = :phone WHERE person_id = :person";

			// Parse the statement
			$statement = oci_parse($connection, $query);
			// Dynamically bind the values
			oci_bind_by_name($statement, ":firstname", $_POST['First_Name']);
			oci_bind_by_name($statement, ":lastname", $_POST['Last_Name']);
			oci_bind_by_name($statement, ":address", $_POST['Address']);
			oci_bind_by_name($statement, ":email", $_POST['Email']);
			oci_bind_by_name($statement, ":phone", $_POST['Phone']);
			oci_bind_by_name($statement, ":person", getUserPersonID());

			oci_execute($statement);

			if (oci_num_rows($statement) > 0) {
				header("Location: account.php");
			} else {
				$error_msg = "Database Error";
			}

		} else {
			$error_msg = "Invalid credentials";
		}

		oci_free_statement($statement);
		oci_close($connection);
	}
}
?>
<html>
	<head>
		<script src="validate_form.js"></script>
		<style>
			table {
				border: 1px solid black;
			}
			tr {
				border: 1px solid black;
			}
			p.error {
				color: red;
			}
		</style>
	</head>
	<body>
		<?php
		require ('_database.php');

		$query = "SELECT first_name, last_name, address, email, phone FROM persons WHERE person_id = '" . getUserPersonID() . "'";

		$statement = oci_parse($connection, $query);
		oci_execute($statement);
		?>
		
		<?php if (oci_fetch($statement)) {
			// Fetch data
 			$firstname = trim(oci_result($statement, "FIRST_NAME"));
			$lastname = trim(oci_result($statement, "LAST_NAME"));
			$address = trim(oci_result($statement, "ADDRESS"));
			$email = trim(oci_result($statement, "EMAIL"));
			$phone = trim(oci_result($statement, "PHONE"));
			?>
			<p class="error" id="error_msg"><?php echo $error_msg ?></p>
			<form id="editinfo" method="post" action="editinfo.php"
				onsubmit="return validateInput('editinfo')" >
				
				<table>
					<tr>
						<td>First Name: </td>
						<td><input maxlength="24" assertion="not_blank" type="text" name="First_Name" value="<?php echo $firstname ?>"/></td>
					</tr>
					<tr>
						<td>Last Name: </td>
						<td><input maxlength="24" assertion="not_blank" type="text" name="Last_Name" value="<?php echo $lastname ?>"/></td>
					</tr>
					<tr>
						<td>Address: </td>
						<td><input maxlength="128" assertion="not_blank" type="text" name="Address" value="<?php echo $address ?>"/></td>
					</tr>
					<tr>
						<td>Email: </td>
						<td><input maxlength="128" assertion="not_blank" type="text" name="Email" value="<?php echo $email ?>"/></td>
					</tr>
					<tr>
						<td>Phone: </td>
						<td><input onchange="autofilter('editinfo')" filter="\D" maxlength="10"
							assertion="not_blank" type="text" name="Phone" value="<?php echo $phone ?>"/></td>
					</tr>
					
					<tr>
						<td>Confirm Password</td>
						<td><input assertion="not_blank" type="password" name="Password" /></td>
					</tr>
					
					<tr>
						<td />
						<td><input type="submit" name="changeinfo" value="Save" /></td>
					</tr>
				</table>
			</form>
			
		<?php } else {
				echo "<p>Database state error: Unable to find user info</p>";
				}
				oci_free_statement($statement);
				oci_close($connection);
		?>
		