<?php
require ('session.php');

// check for submission post
if (isset($_POST['upload'])) {
	// missing fields
	if (empty($_POST['record_id']) && empty($_POST['patient_id'])) {
		$message = "Missing fileds";
		$msg_class = 'error';
	} else {
		//require a connection to the database
		require ('_database.php');
		// Insert the new record into the database	
		$record_id = $_POST['record_id'];
		$patient_id = $_POST['patient_id'];
		$doctor_id = $_POST['doctor_id'];
		$radiologist_id = $_POST['radiologist_id'];
		$test_type = $_POST['test_type'];
		$prescribing_date = $_POST['prescribing_date'];
		$test_date = $_POST['test_date'];
		$diagnosis = $_POST['disagnosis'];
		$description = $_POST['description'];
		
		echo $record_id;
		echo $patient_id;
		echo $doctor_id;
		echo radiologist_id;
		echo $test_type;
		echo $diagnosis;
		echo $description;
		
		$query="INSERT INTO radiology_record VALUES('$record_id', '$patient_id', '$doctor_id', '$radiologist_id',
			'$test_type', to_date('$prescribing_date', 'YYYY-MM-DD'), to_date('$test_date', 'YYYY-MM-DD'), 
			'$diagnosis', '$description')";
		
		$statement = oci_parse($connection, $query);

		$results = oci_execute($statement);
			
	}
}

?>
