<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if ($user->data['user_id'] < 4) {
  header('Location: http://www.roleplaygateway.com/ucp.php?mode=login&redirect=http://www.roleplaygateway.com/feedback');
  exit();
}

# Configuration
$uservoice_subdomain  = "rpg";
$sso_key              = "38d4251d6abe69287b93e1ae5304e5bb";

$salted               = $sso_key . $uservoice_subdomain;
$hash                 = hash('sha1', $salted, true);
$saltedHash           = substr($hash, 0, 16);
$iv                   = "blatheringidiots";

$data = json_encode(array(
  'guid'            => $user->data['user_id'],
  'email'           => $user->data['user_email'],
  'display_name'    => $user->data['username'],
  'url'             => 'http://www.roleplaygateway.com/member/'.$user->data['username'].'/',
  'avatar_url'      => getAvatarURL($user->data['user_avatar_type'], $user->data['user_avatar']),
  'updates'         => True,
  'comment_updates' => True
  
), JSON_FORCE_OBJECT);

// double XOR first block
for ($i = 0; $i < 16; $i++)
{
  $data[$i] = $data[$i] ^ $iv[$i];
}

$pad    = 16 - (strlen($data) % 16);
$data   = $data . str_repeat(chr($pad), $pad);

$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'','cbc','');

mcrypt_generic_init($cipher, $saltedHash, $iv);
$encryptedData = mcrypt_generic($cipher,$data);
mcrypt_generic_deinit($cipher);

$encryptedData = urlencode(base64_encode($encryptedData));

header('Location: http://feedback.roleplaygateway.com/?sso='.$encryptedData);
exit();

function getAvatarURL($type, $url) {
  switch ($type) {
    default:
      return null;
    break;
    case 1:
      return 'http://www.roleplaygateway.com/download/file.php?avatar='.$url;
    break;
    case 2:
      return $url;
    break;
    case 3:
      return 'http://www.roleplaygateway.com/images/avatars/gallery/'.$url;
    break;
  }
}

?>
