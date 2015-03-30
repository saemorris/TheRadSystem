<?php 	
require('session.php'); 
?>


<!DOCTYPE html>
<html>
	<head>
		<title>Request an OLAP Report</title>
	</head>
	<body>
			<form id="olapRequest" action="OLAP_report.php" method="post">
				<div>
					<p class="header"><b>Report Request</b></br></p>
					<?php echo "<p id=\"message\" class=\"$msg_class\">$message</p>"?>
				</div>
				<table>
					<tr>
						<td class="prompt_field">Patient:</td>
                        <td class="field"><input type="text" name="patient" value="All"/></td>
                    </tr>
                    <tr>
                        <td class="prompt_field">Test Type:</td>
                        <td class="field"><input type="text" name="test" value="All" /></td>
                    </tr>
                    <tr>
						<select name="time_period">
							<option value="All">All</option>
							<option value="Year">Year</option>
							<option value="Month">Month</option>
							<option value="Week">Week</option>
						</select>
					</tr>
                </table>
                <div id="submit"><input type="submit" name="request" value="Submit"/></div>
            </form>
		</div>
	</body>
</html>