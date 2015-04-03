<?php
require('session.php');
requireUserClass('a');

$error_msg = '';

if (isset($_POST['register'])) {
	require('_database.php');
	
	// Check username not in user
	$query = 'SELECT count(*) AS in_use FROM users WHERE user_name = :user_name';
	$statement = oci_parse($connection, $query);
	oci_bind_by_name($statement, ':user_name', $_POST['user_name']);
	
	oci_execute($statement);
	oci_fetch($statement);
	
	if (oci_result($statement, 'IN_USE') > 0) {
		$error_msg = 'Username already in use';
		oci_free_statement($statement);
		oci_close($connection);
	} else {
		$query = 'INSERT INTO persons(person_id, first_name, last_name, address, email, phone)
					VALUES(person_id_seq.nextval, :first_name, :last_name, :address, :email, :phone)
					RETURNING person_id INTO :person_id';

		$statement = oci_parse($connection, $query);
		
		oci_bind_by_name($statement, ':first_name', $_POST['first_name']);
		oci_bind_by_name($statement, ':last_name', $_POST['last_name']);
		oci_bind_by_name($statement, ':address', $_POST['address']);
		oci_bind_by_name($statement, ':email', $_POST['email']);
		oci_bind_by_name($statement, ':phone', $_POST['phone']);
		oci_bind_by_name($statement, ':person_id', $person_id, -1, OCI_B_INT);
		
		if (!oci_execute($statement)) {
			echo '<p>Database error</p>';
			oci_rollback($connection);
			exit;
		}
		
		oci_free_statement($statement);
		
		$query = 'INSERT INTO users(user_name, password, person_id, class, date_registered) 
					VALUES (:user_name, :password, :person_id, :class, sysdate)';
		
		$statement = oci_parse($connection, $query);
		
		oci_bind_by_name($statement, ':user_name', $_POST['user_name']);
		oci_bind_by_name($statement, ':password', $_POST['password']);
		oci_bind_by_name($statement, ':person_id', $person_id);
		oci_bind_by_name($statement, ':class', $_POST['class']);
		
		if (!oci_execute($statement)) {
			echo '<p>Database error</p>';
			oci_rollback($connection);
			exit;
		}
		
		oci_free_statement($statement);
		oci_close($connection);

		header("Location: users.php?q=".$_POST['user_name']);
		exit();
	}
}
?>
<html>
	<body>
		<style>
			p.error {
				color: red;
			}
		</style>
		<script src="validate_form.js"> </script>
		<script>
		function checkPasswords() {
			var pwd1, pwd2;
			pwd = document.forms['registration']['pwd'].value;
			pwd2 = document.forms['registration']['pwd2'].value;
			
			if (pwd == pwd2) {
				return true;
			} else {
				show_message("Password fields don't match!");
				return false;
			}
		}
		</script>
	</head>
	<body>
		<form id="registration" method="post" action="register.php" 
				onsubmit="return checkPasswords() && validateInput('registration')">
			<p>
				<p class="error" id="error_msg"><?php echo $error_msg; ?></p>
				<h2>Account Info</h2>
				<table>
				<tr>
					<td align="right">Username:</td><td>
					<input assertion="not_blank" type="text" name="user_name" accept-charset="UTF-8" maxlength="24" />
					</td>
				</tr>
				<tr>
					<td align="right">Password:</td><td><input assertion="not_blank" id="pwd" type="password" name="password" accept-charset="UTF-8" maxlength="24" /><td>
				</tr>
				<tr>
					<td align="right">Confirm Password:</td><td><input id="pwd2" type="password" name="confirm_password" accept-charset="UTF-8" maxlength="24" /><td>
				</tr>
				<tr>
					<td align="right">Class:</td><td>
					<select name="class">
						<option value="p">Patient</option>
						<option value="d">Doctor</option>
						<option value="r">Radiologist</option>
						<option value="a">Admin</option>
					</select>
					</td>
				</tr>
			</table>
			</p>
			<p>
				<h2>Personal Details</h2>
				<table>
				<tr>
					<td align="right">First Name:</td><td>
					<input assertion="not_blank" type="text" name="first_name" accept-charset="UTF-8" maxlength="24" />
					</td>
				</tr>
				<tr>
					<td align="right">Last Name:</td><td>
					<input assertion="not_blank" type="text" name="last_name" accept-charset="UTF-8" maxlength="24"/>
					</td>
				</tr>
				<tr>
					<td align="right">Address:</td><td>
					<input assertion="not_blank" type="text" name="address" accept-charset="UTF-8" maxlength="128" />
					</td>
				</tr>
				<tr>
					<td align="right">Email:</td><td>
					<input assertion="not_blank" type="text" name="email" accept-charset="UTF-8" maxlength="128" />
					</td>
				</tr>
				<tr>
					<td align="right">Phone:</td><td>
					<input assertion="not_blank" type="text" name="phone" accept-charset="UTF-8" maxlength="10" />
					</td>
				</tr>
			</table>
			</p>
			<p>
				<input type="submit" name="register" value="Create" />
			</p>
		</form>
	</body>
</html>
