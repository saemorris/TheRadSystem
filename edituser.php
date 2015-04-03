<?php
// This page is used by the admin
require ('session.php');

requireUserClass('a');
$username = $_GET['user'];

if (!isset($username) || $username == '') {
	header('Location: users.php');
	exit ;
}
?>

<?php
if (isset($_POST['changeinfo'])) {
	// XXX
	// Ensure that the post data exists
	if (!(isset($_POST['First_Name']) && isset($_POST['Last_Name']) && isset($_POST['Address']) && isset($_POST['Email']) && isset($_POST['Phone']) && isset($_POST['id']))) {

		$error_msg = "Not enough data supplied";

	} else {
		require ('_database.php');

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
		oci_bind_by_name($statement, ":person", $_POST['id']);

		oci_execute($statement);

		if (oci_num_rows($statement) > 0) {
			header("Location: account.php");

			// sync the diagnosis index with the new data just uploaded
			$query = "begin ctx_ddl.sync_index('diagnosisIndex', '2M'); end;";

			$statement = oci_parse($connection, $query);
			$result = oci_execute($statement);

			// sync the description index with the new data just uploaded
			$query = "begin ctx_ddl.sync_index('descriptionIndex', '2M'); end;";
			$statement = oci_parse($connection, $query);
			$result = oci_execute($statement);

			oci_commit($connection);

			oci_free_statement($statement);
			oci_close($connection);
			exit ;
		} else {
			$error_msg = "Database Error";
			oci_free_statement($statement);
			oci_close($connection);
		}
	}
}
?>
<html>
	<head>
		<script src="validate_form.js"></script>
		<script>
			function deleteListItem(elm) {
				var list = document.getElementById("doclist"),
					li = document.getElementById(elm);
				list.removeChild(li);
			}
			function addListItem() {
				var list = document.getElementById("doclist"),
					item = document.createElement("li"),
					newdoc = document.getElementById("newdoc");
				var s;

				item.id = "doc_" + newdoc.value;
				// do nothing if no value entered
				if (newdoc.value == "" || newdoc.value <= 0) {
					return;
				}
				s = '<label>{}</label>';
				s += '<input type="hidden" name="doc_{}" value="{}" />';
				s += '<input type="button" value="Delete"'; 
				s += 'onclick="deleteListItem(\'doc_{}\')" />'; 
				
				item.innerHTML = s.replace(/{}/g, newdoc.value);
				
				newdoc.value = "";

				list.appendChild(item);
			}
		</script>
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
		
		$query = "SELECT class, user_name, password, persons.person_id, first_name, last_name, address, email, phone " .
				"FROM users, persons " . "WHERE user_name = :username AND users.person_id = persons.person_id";
		
		$statement = oci_parse($connection, $query);
		oci_bind_by_name($statement, ":username", $username);
		oci_execute($statement);
		
		if (!oci_fetch($statement)) {
			echo "<p>Database state error: Unable to find user info</p>";
		} else { ?>
			
			<p class="error" id="error_msg"><?php echo $error_msg; ?></p>
			<form id="edituser" method="post" action="edituser.php"
				onsubmit="return validateInput('editinfo')" >
				
				<table border="1">
					<tr>
						<?php
						$fname = oci_field_name($statement, 1);
						$fsize = oci_field_size($statement, 1);
						$fvalue = oci_result($statement, 1); ?>
						<td>CLASS</td>
						<td>
							<select name="<?php echo $fname; ?>">
								<option value="a"<?php echo $fvalue == 'a' ? " selected" : ""; ?>>Admin</option>
								<option value="d"<?php echo $fvalue == 'd' ? " selected" : ""; ?>>Doctor</option>
								<option value="r"<?php echo $fvalue == 'r' ? " selected" : ""; ?>>Radiologist</option>
								<option value="p"<?php echo $fvalue == 'p' ? " selected" : ""; ?>>Patient</option>
							</select>
						</td>
					</tr>
					<tr>
						<?php
						$fname = oci_field_name($statement, 2);
						$fsize = oci_field_size($statement, 2);
						$fvalue = oci_result($statement, 2); ?>
						<td>USER_NAME</td>
						<td><input maxlength="<?php echo $fsize; ?>" assertion="not_blank" type="text" 
							name="<?php echo $fname; ?>" value="<?php echo $fvalue; ?>"/></td>
					</tr>
					<tr>
						<?php
						$fname = oci_field_name($statement, 3);
						$fsize = oci_field_size($statement, 3);
						$fvalue = oci_result($statement, 3); ?>
						<td>CHANGE PASSWORD</td>
						<td><input maxlength="<?php echo $fsize; ?>" type="text" 
							name="<?php echo $fname; ?>" value=""/></td>
					</tr>
					
					<?php
					for ($i = 5; $i <= oci_num_fields($statement); $i++) {
						$fname = oci_field_name($statement, $i);
						$fsize = oci_field_size($statement, $i);
						$fvalue = oci_result($statement, $i); ?>
					
					<tr>
						<td><?php echo $fname; ?></td>
						<td><input maxlength="<?php echo $fsize; ?>" assertion="not_blank" type="text" 
							name="<?php echo $fname; ?>" value="<?php echo $fvalue; ?>"/></td>
					</tr>
					
					<?php
					}
					
					$patient = oci_result($statement, "PERSON_ID");
					oci_free_statement($statement);
					
					$query = "SELECT doctor_id, first_name, last_name FROM family_doctor d, persons p ".
					"WHERE d.doctor_id = p.person_id AND patient_id = :patient";
					
					$statement = oci_parse($connection, $query);
					oci_bind_by_name($statement, ':patient', $patient);
					
					oci_execute($statement);
					
					// Fetch a list of all associated doctors
					echo "<tr><td>DOCTORS</td><td><ul id='doclist'>";
					while (oci_fetch($statement)) {
							$doc_first_name = oci_result($statement, "FIRST_NAME");
							$doc_last_name = oci_result($statement, "LAST_NAME");
							$doc_full_name = $doc_first_name . ' ' . $doc_last_name;
							$doc_id = oci_result($statement, "DOCTOR_ID") ?>
							
							<li id="doc_<?php echo $doc_id; ?>" >
								<label><?php echo $doc_id; ?></label> 
								<input type="hidden" name="doc_<?php echo $doc_id; ?>" value=<?php echo $doc_id; ?> />
								<input type="button" value="Delete" 
									onclick="deleteListItem('doc_<?php echo $doc_id; ?>')" />
							</li>
							
					<?php } ?>
					</ul>
					<input type="number" value="" id="newdoc" min="1" />
					<input type="button" value="Add" id="add_doc" onclick="addListItem()" />
					</td></tr>
					
					<?php
					
					?>
					
					<tr>
						<td />
						<td><input type="submit" name="changeinfo" value="Save" /></td>
					</tr>
				</table>
			<input name="id" type="hidden" value="<?php echo $username; ?>"/>
		</form>
		
		<?php
		}
		oci_close($connection);
		?>
	</body>
</html>
