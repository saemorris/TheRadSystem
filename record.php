<?php
$id = $_GET['id'];
// Ensure that an id was got
if (!isset($id)) {
	header("Location: search.php");
}

$default = "displayImage.php?id=$id&size=";
?>

<html>
	<head>
		<script>
			var imgsize;
			imgsize = 1;
			function zoompac() {
				imgsize = 1+imgsize%2;
				document.getElementById('pacimg').src="<?php echo $default; ?>"+imgsize;
			}
		</script>
	</head>
	<body>
		
		
		<img id="pacimg" src="<?php echo $default; ?>" onclick="zoompac()" />
	</body>
</html>
