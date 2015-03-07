<html>
	<body>
		<h1>Test: <?php echo date(r); ?></h1>
		<p>Your Browser Info:<br/>
			<?php
				echo $_SERVER['HTTP_USER_AGENT'] . "\n\n";

				$browser = get_browser(null, true);
				print_r($browser); ?>
		</p>
		<?php include('checkcookie.php'); ?>
	</body>
</html>
