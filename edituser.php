<?php
// This page is used by the admin
require ('session.php');
requireUserClass('a');

// Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error_msg = '';
?>

<?php
function startsWith($string, $prefix) {
	$pos = strpos($string, $prefix);
	return !($pos === FALSE || $pos > 0);
}

if (isset($_POST['changeinfo'])) {
	$username = $_POST['username'];
	require ('_database.php');

	// Fetch the person id
	$statement = oci_parse($connection, "SELECT person_id FROM users WHERE user_name = :username");
	oci_bind_by_name($statement, ':username', $username);

	oci_execute($statement);

	oci_fetch($statement);
	$person_id = (int) oci_result($statement, 'PERSON_ID');

	oci_free_statement($statement);

	// Update user info
	$updatePersons = "UPDATE persons SET ";
	$updateUsers = "UPDATE users SET ";

	$personsBindings = array();
	$doctorsBindings = array();
	$usersBindings = array();

	$p_op = "";
	$d_op = "";
	$u_op = "";

	// Build up queries
	foreach ($_POST as $key => $value) {
		if (!isset($value) || empty($value) || $value == '') {
			continue;
		}
		$offset = strpos($key, "__") + 2;
		$target = substr($key, $offset);
		$tag = ':' . strtolower($target);
		if (startsWith($key, "persons__")) {

			$personsBindings[$tag] = $value;

			$updatePersons .= "$p_op $target = $tag";
			$p_op = ', ';

		} else if (startsWith($key, "users__")) {

			$usersBindings[$tag] = $value;

			$updateUsers .= "$u_op $target = $tag";
			$u_op = ',';

		} else if (startsWith($key, "doctors__")) {

			$doctorsBindings[$tag] = $value;

			$d_op = ',';
		} else {
			// silently drop
		}
	}
	// Drop doctors
	$query = "DELETE FROM family_doctor WHERE patient_id = :patient";
	$statement = oci_parse($connection, $query);

	oci_bind_by_name($statement, ":patient", $person_id);

	if (!oci_execute($statement)) {
		oci_rollback($connection);
		oci_free_statement($statement);
		oci_close($connection);
		exit ;
	}
	oci_free_statement($statement);

	// Add doctors individually because oracle is a pain in the butt
	if ($d_op !== "") {
		foreach ($doctorsBindings as $key => $value) {
			$query = "INSERT INTO family_doctor(patient_id, doctor_id) VALUES (:patient, $key)";
			$statement = oci_parse($connection, $query);

			oci_bind_by_name($statement, ":patient", $person_id);
			oci_bind_by_name($statement, $key, $doctorsBindings[$key]);

			if (!oci_execute($statement)) {
				oci_rollback($connection);
				oci_free_statement($statement);
				oci_close($connection);
				exit ;
			}
			oci_free_statement($statement);
		}

	}

	// Update person
	if ($p_op !== "") {
		$query = $updatePersons . ' WHERE person_id = :patient';
		$statement = oci_parse($connection, $query);

		oci_bind_by_name($statement, ":patient", $person_id);

		foreach ($personsBindings as $key => $value) {
			oci_bind_by_name($statement, $key, $personsBindings[$key]);
		}

		if (!oci_execute($statement)) {
			oci_rollback($connection);
			oci_free_statement($statement);
			oci_close($connection);
			exit ;
		}
		oci_free_statement($statement);
	}

	// Update user
	if ($u_op !== "") {
		$query = $updateUsers . " WHERE user_name = :username";
		$statement = oci_parse($connection, $query);

		oci_bind_by_name($statement, ":username", $username);

		foreach ($usersBindings as $key => $value) {
			oci_bind_by_name($statement, $key, $usersBindings[$key]);
		}

		if (!oci_execute($statement)) {
			oci_rollback($connection);
			oci_free_statement($statement);
			oci_close($connection);
			exit ;
		}
		oci_free_statement($statement);
		oci_commit($connection);
	}

	// Commit and exit
	oci_commit($connection);
	oci_close($connection);
	
	header('Location: users.php');
	exit ;
}

$username = $_GET['user'];

if (!isset($username) || $username == '') {
	header('Location: users.php');
	exit ;
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
				s += '<input type="hidden" name="doctors__doc_{}" value="{}" />';
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
						$fvalue = oci_result($statement, 1);
 ?>
						<td>CLASS</td>
						<td>
							<select name="users__<?php echo $fname; ?>">
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
						$fvalue = oci_result($statement, 2);
 ?>
						<td>USER_NAME</td>
						<td><input maxlength="<?php echo $fsize; ?>" type="text"  
							name="users__<?php echo $fname; ?>" value="<?php echo $fvalue; ?>"/></td>
					</tr>
					<tr>
						<?php
						$fname = oci_field_name($statement, 3);
						$fsize = oci_field_size($statement, 3);
						$fvalue = oci_result($statement, 3);
 ?>
						<td>CHANGE PASSWORD</td>
						<td><input maxlength="<?php echo $fsize; ?>" type="password"  
							name="users__<?php echo $fname; ?>" value=""/></td>
					</tr>
					
					<?php
					for ($i = 5; $i <= oci_num_fields($statement); $i++) {
						$fname = oci_field_name($statement, $i);
						$fsize = oci_field_size($statement, $i);
						$fvalue = oci_result($statement, $i); ?>
					
					<tr>
						<td><?php echo $fname; ?></td>
						<td><input maxlength="<?php echo $fsize; ?>" type="text"  
							name="persons__<?php echo $fname; ?>" value="<?php echo $fvalue; ?>"/></td>
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
					$doc_id = oci_result($statement, "DOCTOR_ID")
 ?>
							
							<li id="doc_<?php echo $doc_id; ?>" >
								<label><?php echo $doc_id; ?></label> 
								<input type="hidden" name="doctors__doc_<?php echo $doc_id; ?>" value=<?php echo $doc_id; ?> />
								<input type="button" value="Delete" 
									onclick="deleteListItem('doc_<?php echo $doc_id; ?>')" />
							</li>
							
					<?php } ?>
					</ul>
					<input type="number" value="" id="newdoc" min="1" />
					<input type="button" value="Add" id="add_doc" onclick="addListItem()" />
					</td></tr>
					
					<tr>
						<td />
						<td><input type="submit" name="changeinfo" value="Save" /></td>
					</tr>
				</table>
			<input type="hidden" name="username" value="<?php echo $username; ?>"/>
		</form>
		
		<?php
		}
		oci_close($connection);
		?>
	</body>
</html>
