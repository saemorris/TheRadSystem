<?php require('session.php'); ?>

<html>
    <body>
        <h1>
            Success!
        </h1>
        <p>
            If you can read this after signing in, logging in works.
        </p>
        <p>Your username is "<?php echo getUserName() ?>"</p>
        <p>Your account type is "<?php echo getUserClass() ?>"</p>
        <p>Your id is "<?php echo getUserPersonID() ?>"</p>
        <p>Your session was created at <?php echo date("M d, Y g:i:s a", $_SESSION['us_created_time']) ?></p>
        <p>Your session was last active at <?php echo date("M d, Y g:i:s a", $_SESSION['us_last_activity']) ?></p>
        <p><a href="account.php">Account</a></p>
        <p><a href="logout.php">Log out</a></p>
    </body>
</html>
