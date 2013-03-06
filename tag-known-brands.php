<?php
$phpbb_root_path = '/var/www/html/';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_prs.' . $phpEx);

$brands = array(
	'naruto',
	'twilight',
	'inuyasha',
	'full metal alchemist',
	'harry potter',
	'x-men',
	'x men',
	'gundam',
	'dragonball',
	'dbz',
	'ouran',
	'one piece',
	'nbc',
	'heroes',
	'akatsuki',
	'sailor moon',
	'pokemon',
	'disney',
	'final fantasy',
	'star wars',
	'batman',
	
);

foreach ($brands as $brand) {
	$sql = 'SELECT roleplay_id FROM gateway_tags WHERE tag = "'.$brand.'" AND roleplay_id > 1';

	$roleplays_result = $db->sql_query($sql);
	
	while ($roleplay = $db->sql_fetchrow($roleplays_result)) {
		echo "\n".'Tagging '. $roleplay['roleplay_id'] .' with #fanfic...';

		$sql = 'INSERT IGNORE into gateway_tags (roleplay_id, tag) VALUES ('.$roleplay["roleplay_id"].', "fanfic" )';
		if ($db->sql_query($sql)) echo '...done';		
	}

	$db->sql_freeresult($roleplays_result);

}

?>
