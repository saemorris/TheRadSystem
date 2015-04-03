<?php 

// Source: http://www.htmlgoodies.com/beyond/php/article.php/3877766/Web-Developer-How-To-Upload-Images-Using-PHP.htm

// make a note of the current working directory, relative to root. 
$directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']); 

// make a note of the directory that will recieve the uploaded file 
$uploadsDirectory = $_SERVER['DOCUMENT_ROOT'] . $directory_self . 'uploaded_files/'; 

// make a note of the location of the upload form in case we need it 
$uploadForm = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'uploadImage.php'; 


// make a note of the location of the success page 
$uploadSuccess = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'uploadSuccess.php'; 

// fieldname used within the file <input> of the HTML form 
$filename = 'image_file'; 

// possible PHP upload errors 
$errors = array(1 => 'php.ini max file size exceeded', 
                2 => 'html form max file size exceeded', 
                3 => 'file upload was only partial', 
                4 => 'no file was attached'); 

// check the upload form was actually submitted else print the form 
isset($_POST['submit']) 
    or error('the upload form is neaded', $uploadForm); 

// check for PHP's built-in uploading errors 
($_FILES[$filename]['error'] == 0) 
    or error($errors[$_FILES[$filename]['error']], $uploadForm); 
     
// check that the file we are working on really was the subject of an HTTP upload 
@is_uploaded_file($_FILES[$filename]['tmp_name']) 
    or error('not an HTTP upload', $uploadForm); 
     
// validation... since this is an image upload script we should run a check   
// to make sure the uploaded file is in fact an image. Here is a simple check: 
// getimagesize() returns false if the file tested is not an image. 
@getimagesize($_FILES[$filename]['tmp_name']) 
    or error('only image uploads are allowed', $uploadForm); 
     
// make a unique filename for the uploaded file and check it is not already 
// taken... if it is already taken keep trying until we find a vacant one 
$now = time(); 
while(file_exists($uploadFilename = $uploadsDirectory.$now.'-'.$_FILES[$filename]['tmp_name'])) 
{ 
    $now++; 
} 

echo "uploadFileName: " . $uploadFilename . "<p>";

//require a connection to the database
require ('_database.php');

// Creates an "empty" OCI-Lob object to bind to the locator
$image_lob = oci_new_descriptor($connection, OCI_D_LOB);
$regular_lob = oci_new_descriptor($connection, OCI_D_LOB);
$thumbnail_lob = oci_new_descriptor($connection, OCI_D_LOB);

$record_id = (int) $_POST['record_id'];
echo $record_id;


$query="INSERT INTO pacs_images (record_id, image_id, thumbnail, regular_size, full_size) VALUES 
($record_id, image_id_seq.nextval, EMPTY_BLOB(), EMPTY_BLOB(), EMPTY_BLOB()) RETURNING 
thumbnail, regular_size, full_size, image_id INTO :thumbnail, :regular, :image, :id";

$src = imagecreatefromjpeg($_FILES[$filename]['tmp_name']);

// Regular image size is proportional to original
$regular_width = 400;
$regular_height = round($regular_width*imagesy($src)/imagesx($src));

echo "<p>Regular size = $regular_width x $regular_height</p>";

$regular = imagecreatetruecolor($regular_width, $regular_height);
$thumbnail = imagecreatetruecolor(150, 150);

// resize image for different zoom strength
imagecopyresampled($regular, $src, 0, 0, 0, 0, $regular_width, $regular_height, imagesx($src), imagesy($src));
imagecopyresampled($thumbnail, $src, 0, 0, 0, 0, 150, 150, imagesx($src), imagesy($src));

$statement = oci_parse($connection, $query);

// Bind the returned Oracle LOB locator to the PHP LOB object
oci_bind_by_name($statement, ":image", $image_lob, -1, OCI_B_BLOB);
oci_bind_by_name($statement, ":regular", $regular_lob, -1, OCI_B_BLOB);
oci_bind_by_name($statement, ":thumbnail", $thumbnail_lob, -1, OCI_B_BLOB);

oci_bind_by_name($statement, ":id", $id, -1, OCI_B_INT);

// Execute the statement using , OCI_DEFAULT - as a transaction
$valid = TRUE;
oci_execute($statement, OCI_DEFAULT) or die ("Unable to execute query\n");

echo "<p>image id = $id</p>";

// Save full size image
if($image_lob->savefile($_FILES[$filename]['tmp_name'])) {
	echo "<p>image successfully uploaded</p>";
} else {
	oci_rollback($connection);
	$valid = FALSE;
	echo "<p>Couldn't upload image</p>";
}

// Upload thumbnail
if ($valid) {
	$tempfilename = tempnam(sys_get_temp_dir(), "upload_thumb");
	imagejpeg($thumbnail, $tempfilename, 90);
	echo "saving to '$tempfilename' ... ";
	
	if($thumbnail_lob->savefile($tempfilename)) {
		echo "<p>Thumbnail successfully uploaded</p>";
	} else {
		oci_rollback($connection);
		$valid = FALSE;
		echo "<p>Couldn't upload image</p>";
	}
}

// Upload regular
if ($valid) {
	$tempfilename = tempnam(sys_get_temp_dir(), "upload_thumb");
	imagejpeg($regular, $tempfilename, 90);
	echo "saving to '$tempfilename' ... ";
	
	if($regular_lob->savefile($tempfilename)) {
		echo "<p>Regular successfully uploaded</p>";
	} else {
		oci_rollback($connection);
		$valid = FALSE;
		echo "<p>Couldn't upload image</p>";
	}
}

if ($valid) {
	oci_commit($connection);
	echo "<img src='displayImage.php?id=$id&size=1' />";
}

echo "<a href='search.php'>Home</a>";

// Clean up database objects
oci_free_statement($statement);
oci_close($connection);

// The following function is an error handler which is used 
// to output an HTML error page if the file upload fails 
function error($error, $location, $seconds = 5) { 
    //header("Refresh: $seconds; URL="$location""); 
    echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 " +
		"Transitional//EN\">\n" +
		"<HTML>\n" +
		"<HEAD><TITLE>Upload Message</TITLE></HEAD>\n" +
		"<BODY>\n" +
		"<H1>" +
	        response_message +
		"</H1>\n" +
		"</BODY></HTML>");
}
?> 