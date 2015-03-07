<?php
function show_table($row) {
	echo 'received data';
	echo '<tr>';
	foreach ($row as $key=>$value) {
		echo "<td>$value</td>";
	}
	echo '</tr>';
}
?>
