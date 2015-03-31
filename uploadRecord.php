<?php

require_once('session.php');

// Check to make sure the user is a radiologist
if (getUserClass() == 'r') { ?>


	<!DOCTYPE html>
	<html>
	    <head>
	        <title>Upload Radiology Record</title>
	        <link rel="stylesheet" href="UploadStyleSheet.css">
	    </head>
	    <body>
	        <div id="container">
	            <form id="uploadRecord" action="uploadRecord.php" method="post">
	                <div>   
	                    <p class="header">Upload Radiology Record:</p>
	                    <?php echo "<p id=\"message\" class=\"$msg_class\">$message</p>"?>
	                </div>
	                <table>
	                    <tr>
	                        <td class="prompt_field">Patient Id:</td>
	                        <td class="field"><input type="text" name="patient_id" /></td>
	                    </tr>
	                    <tr>
	                        <td class="prompt_field">Doctor Id:</td>
	                        <td class="field"><input type="text" name="doctor_id" /></td>
	                    </tr>
	                    <tr>
	                        <td class="prompt_field">Radiologist Id:</td>
	                        <td class="field"><input type="text" name="radiologist_id" /></td>
	                    </tr>
	                    <tr>
	                        <td class="prompt_field">Test Type:</td>
	                        <td class="field"><input type="text" name="test_type" /></td>
	                    </tr>
	                    <tr>
	                        <td class="prompt_field">Prescribing Date:</td>
	                        <td class="field"><input type="date" name="prescribing_date" /></td>
	                    </tr>
	                    <tr>
	                        <td class="prompt_field">Test Date:</td>
	                        <td class="field"><input type="date" name="test_date" /></td>
	                    </tr>
	                    <tr>
	                        <td class="prompt_field">Diagnosis:</td>
	                        <td class="field"><input type="text" name="diagnosis" /></td>
	                    </tr>
	                    <tr>
	                        <td class="prompt_field">Description:</td>
	                        <td class="field"><input type="text" name="description" /></td>
	                    </tr>
						<tr>
						<td/>
						<td>
							<input type="submit" name="upload" value="Upload"/>
						</td>
						</tr></div>
					</table>
	            </form>
	        </div>
	                	
			<?php
				require('_upload.php');
			?>
	    </body>
	</html>
<?php
} else { ?>
	<html>
		<h4>You do not have access to this page.</h4>
		<a href="search.php">Home</a>
	</html>
<?php } ?>
