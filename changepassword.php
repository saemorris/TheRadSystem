<?php
require ('session.php');
?>

<?php
if (isset($_POST['changepass'])) {
	$oldpass = $_POST['oldpass'];
	$newpass = $_POST['newpass'];
	$confirmpass = $_POST['confirmpass'];

	if ($newpass == $confirmpass) {
		require ('_database.php');
		// Try to change the password
		$query = "UPDATE users SET password = :newpass WHERE user_name = :username AND password = :oldpass";
		
		// Parse the statement...
		$statement = oci_parse($connection, $query);
		// ...then bind names dynamically so that users can't inject data
		oci_bind_by_name($statement, ":newpass", $newpass);
		oci_bind_by_name($statement, ":oldpass", $oldpass);
		oci_bind_by_name($statement, ":username", getUserName());
		
		if (oci_execute($statement)) {
			// If a row was edited (should only be 1 since user_name is PK)
			if (oci_num_rows($statement) > 0) {
				$error_msg = "Password changed";				
			} else {
				$error_msg = "Invalid password";
			}
		} else {
			$error_msg = "Database Error: Could not update password";
			echo oci_error();
		}
		oci_free_statement($statement);
		oci_close($connection);
	} else {
		$error_msg = "Password fields don't match!";
	}
} else {
	$error_msg = "";
}
?>
<html>
	<head>
		<style>
			p.error {
				color: red;
			}
		</style>
		<script src="validate_form.js"></script>
		<script>
			function checkPasswords() {
				var pwd1,
				    pwd2;
				pwd = document.forms['changepassword']['newpass'].value;
				pwd2 = document.forms['changepassword']['confirmpass'].value;

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
		<form id="changepassword" method="post" action="changepassword.php"
		onsubmit="return checkPasswords()">

			<p class="error" id="error_msg"><?php echo $error_msg ?></p>
			<table>
				<tr>
					<td>Old Password</td>
					<td>
					<input type="password" name="oldpass" />
					</td>
				</tr>
				<tr>
					<td>New Password:</td>
					<td>
					<input type="password" name="newpass" />
					</td>
				</tr>
				<tr>
					<td>Confirm Password:</td>
					<td>
					<input type="password" name="confirmpass" />
					</td>
				</tr>
				<tr>
					<td />
					<td>
					<input type="submit" name="changepass" value="Change Password" />
					</td>
				</tr>
			</table>
		</form>
		<p><a href="account.php">&lt; Back</a></p>
	</body>
</html>