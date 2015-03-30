<?php
require_once('session.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Search </title>
        <link rel="stylesheet" href="searchpage.css">
        
        <!-- if the user is an admin, give them the optin to upload a record -->
        <?php if (getUserClass() == "r") { ?>
        		<a href="uploadRecord.php">Upload Record</a>
        <?php } ?>
        
        <a href="logout.php">Logout</a>
        <form id="search" action="search.php" method="post">
	        	
	        	<div id="dateRangeSearch">
	        		<p class="header">Search by Date Range</p>	
	        		<table>
	                    <tr>
	                        <td class="prompt_field">From:</td>
	                        <td class="field"><input type="date" name="dateQueryFrom" /></td>
	                        
	                        <td class="prompt_field">To:</td>
	                        <td class="field"><input type="date" name="dateQueryTo" /></td>
	                      	<?php echo "<p id=\"message\" class=\"$msg_class\">$message</p>"?>
	                    </tr>
	                    <tr>
	                    </tr>
	                    <div id="submit">
						<tr>
							<td/>
							<td>
								<input type="submit" name="search" value="Search"/>
							</td>
						</tr></div>
					</table>	
	        	</div>
	        	
	        	<div id="keyWordSearch">
	        		<p class="header">Search by Key Words</p>
	        		<table>
	                    <tr>
	                        <td class="prompt_field">Search:</td>
	                        <td class="field"><input type="text" name="keyWordQuery" /></td>
	                    </tr>
	                    <div id="submit">
						<tr>
							<td/>
							<td>
								<input type="submit" name="search" value="Search"/>
							</td>
						</tr></div>
					</table>	
	        	</div>
	        	
				<div style="clear:both"</div>
	        	  	
			<hr>
        </form>
    </head>
    <body>

		<?php
		require('_search.php');
		?>
    </body>
</html>
