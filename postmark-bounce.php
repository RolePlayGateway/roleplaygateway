<?php

if (!isset($_SERVER['PHP_AUTH_USER'])) {
  header('WWW-Authenticate: Basic realm="RPG"');
  header('HTTP/1.0 401 Unauthorized');
  echo 'You must be authorized to view this resource.';
  exit;
} else {

  if ($_SERVER['PHP_AUTH_USER'] == 'postmark' && $_SERVER['PHP_AUTH_PW'] == 'brup9EzuQaqus98d' ) {

    define('IN_PHPBB', true);
    $phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
    $phpEx = substr(strrchr(__FILE__, '.'), 1);
    include($phpbb_root_path . 'common.' . $phpEx);
    include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

    // Start session management
    $user->session_begin();
    $auth->acl($user->data);
    $user->setup();

    $myFile = "postmark-bounces.txt";
    $fh = fopen($myFile, 'a');

    fwrite($fh, "\nREQUEST: " + file_get_contents('php://input'));
    fclose($fh);

    header('Content-Type: application/json');
    echo json_encode(array(
      'status' => 'success'
    ));

  } else {
    header('WWW-Authenticate: Basic realm="RPG"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'You must be authorized to view this resource.';
    exit;
  }
}


?>
