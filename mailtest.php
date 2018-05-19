<?php
// The message
$message = "A simple mail test...";

// In case any of our lines are larger than 70 characters, we should use wordwrap()
$message = wordwrap($message, 70);

// Send
if (mail('admin@roleplaygateway.com', 'Mail test!', $message)) {
	echo 'mailed';
} else {
	echo 'failed';
}

?>