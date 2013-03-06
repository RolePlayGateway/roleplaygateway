<?php 

require('includes/header.php');

require('config.php');

session_destroy();

echo 'Logged out.';

require('includes/footer.php');

?>