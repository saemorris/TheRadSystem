<?php 

// Source: http://www.htmlgoodies.com/beyond/php/article.php/3877766/Web-Developer-How-To-Upload-Images-Using-PHP.htm

// make a note of the current working directory relative to root. 
$directory_self = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']); 

// make a note of the location of the upload handler script 
$uploadHandler = 'http://' . $_SERVER['HTTP_HOST'] . $directory_self . 'uploadProcessor.php'; 

?> 

<html > 
    <head> 
        <meta http-equiv="content-type" content="text/html; charset=iso-8859-1"> 
     
        <link rel="stylesheet" type="text/css" href="uploadstylesheet.css"> 
         
        <title>Upload DICOM Image</title> 
     
    </head> 
     
    <body> 
     
    <form id="Upload" action="uploadProcessor.php" enctype="multipart/form-data" method="post"> 
     
        <h1> 
            Upload a DICOM image 
        </h1> 
         
        <p> 
            <label for="file">File to upload:</label> 
            <input id="file" type="file" name="image_file"> 
        </p> 
                 
        <p> 
            <label for="submit">Press to...</label> 
            <input id="submit" type="submit" name="submit" value="Upload"> 
        </p> 
     
    </form> 
    
    </body> 

</html> 
