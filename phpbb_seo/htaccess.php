<?php
/** 
*
* @package Advanced phpBB SEO mod Rewrite
* @version $Id: htaccess.php 2007/05/26 13:48:48 dcz Exp $
* @copyright (c) 2006, 2007 dcz - www.phpbb-seo.com
* @license http://www.opensource.org/licenses/rpl.php RPL Public License 
*
*/
/**
* phpBB_SEO Class
* www.phpBB-SEO.com
* @package Advanced phpBB3 SEO mod Rewrite
*/
if (!defined('IN_PHPBB')) {
	exit;
}
$modrtype = array('3' => 'ADVANCED', '2' => 'MIXED', '1' => 'SIMPLE', 'type' => intval($phpbb_seo->modrtype));
$htaccess_tpl = '<b style="color:blue"># Lines That should already be in your .htacess</b> <br/>
<b style="color:brown">&lt;Files</b> <b style="color:#FF00FF">"config.php"</b><b style="color:brown">&gt;</b> <br/>
Order Allow,Deny <br/>
Deny from All <br/>
<b style="color:brown">&lt;/Files&gt;</b> <br/>
<b style="color:brown">&lt;Files</b> <b style="color:#FF00FF">"common.php"</b><b style="color:brown">&gt;</b> <br/>
Order Allow,Deny <br/>
Deny from All <br/>
<b style="color:brown">&lt;/Files&gt;</b> <br/>
<br/>
<b style="color:blue"># You may need to un-comment the following line <br/>
# Options +FollowSymlinks <br/>
# REMEBER YOU ONLY NEED TO STARD MOD REWRITE ONCE</b> </b> <br/>
<b style="color:green">RewriteEngine</b> <b style="color:#FF00FF">On</b> <br/>
<b style="color:blue"># REWRITE BASE</b> <br/>
<b style="color:green">RewriteBase</b> <b>/{REWRITEBASE}</b> <br/>
<b style="color:blue"># HERE IS A GOOD PLACE TO ADD THE WWW PREFIXE REDIRECTION</b> <br/>
<br/>
<b style="color:blue">##################################################### <br/>
# PHPBB SEO REWRITE RULES - {MOD_RTYPE} <br/>
##################################################### <br/>
# AUTHOR : dcz www.phpbb-seo.com <br/>
# STARTED : 01/2006 <br/>
#################################  <br/>
# FORUMS PAGES  <br/>
###############</b> <br/>' . "\n";
$htaccess_common_tpl= '<b style="color:blue"># POST</b>  <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_POST}([0-9]+){EXT_POST}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.php?p=$1 [QSA,L,NC] <br/>
<b style="color:blue">#PROFILES</b>  <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_MEMBERS}([0-9]+){EXT_MEMBERS}$ {DEFAULT_SLASH}{PHPBB_RPATH}memberlist.php?mode=viewprofile&u=$1 [QSA,L,NC] <br/>
<b style="color:blue"># THE TEAM</b> <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_TEAM}{EXT_TEAM}$ {DEFAULT_SLASH}{PHPBB_RPATH}memberlist.php?mode=leaders [QSA,L,NC] <br/>' . "\n";
if (!empty($phpbb_seo->seo_static['index'])){
	$htaccess_tpl .= '<b style="color:blue"># FORUM INDEX</b> <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_INDEX}{EXT_INDEX}$ {DEFAULT_SLASH}{PHPBB_RPATH}index.php [QSA,L,NC] <br/>' . "\n";
} else {
	$htaccess_tpl .= '<b style="color:blue"># FORUM INDEX REWRITERULE WOULD STAND HERE IF USED. \'forum\' REQUIRES TO BE SET As FORUM INDEX <br/>
# RewriteRule ^{WIERD_SLASH}{PHPBB_LPATH}forum\.html$ {DEFAULT_SLASH}{PHPBB_RPATH}index.php [QSA,L,NC]</b> <br/>' . "\n";
}
if ($modrtype['type'] == 3) {
	$htaccess_tpl .= '<b style="color:blue"># FORUM</b>  <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}[a-z0-9_-]*{DELIM_FORUM}([0-9]+){FORUM_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewforum.php?f=$1&start=$3 [QSA,L,NC] <br/>
<b style="color:blue"># TOPIC WITH VIRTUAL FOLDER</b>  <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}[a-z0-9_-]*{DELIM_FORUM}([0-9]+)/[a-z0-9_-]*{DELIM_TOPIC}([0-9]+)({DELIM_START}([0-9]+))?{EXT_TOPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.php?f=$1&t=$2&start=$4 [QSA,L,NC] <br/>
<b style="color:blue"># GLOBAL ANNOUNCES WITH VIRTUAL FOLDER</b> <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_ANNOUNCES}{EXT_ANNOUNCES}[a-z0-9_-]*{DELIM_TOPIC}([0-9]+)({DELIM_START}([0-9]+))?{EXT_TOPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.php?t=$1&start=$3 [QSA,L,NC] <br/>
<b style="color:blue"># TOPIC WITHOUT FORUM ID & DELIM</b> <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}[a-z0-9_-]*/?[a-z0-9_-]*{DELIM_TOPIC}([0-9]+)({DELIM_START}([0-9]+))?{EXT_TOPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.php?t=$1&start=$3 [QSA,L,NC] <br/>' . "\n";
$htaccess_tpl .= $htaccess_common_tpl . '<b style="color:blue"># HERE IS A GOOD PLACE TO ADD OTHER PHPBB RELATED REWRITERULES</b><br/>
<br/>
<b style="color:blue"># FORUM WITHOUT ID & DELIM</b><br/>
<b style="color:blue"># THESE FOUR LINES MUST BE LOCATED AT THE END OF YOUR HTACCESS TO WORK PROPERLY</b><br/>
<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-f<br/>
<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-d<br/>
<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-l<br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}[a-z0-9_-]+{FORUM_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewforum.php?start=$2 [QSA,L,NC]<br/>' . "\n";
} elseif ($modrtype['type'] == 2) {
	$htaccess_tpl .= '<b style="color:blue"># FORUM</b>  <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}[a-z0-9_-]*{DELIM_FORUM}([0-9]+){FORUM_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewforum.php?f=$1&start=$3 [QSA,L,NC] <br/>
<b style="color:blue"># TOPIC WITH VIRTUAL FOLDER</b>  <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}[a-z0-9_-]*{DELIM_FORUM}([0-9]+)/{STATIC_TOPIC}([0-9]+)(-([0-9]+))?{EXT_TOPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.php?f=$1&t=$2&start=$4 [QSA,L,NC] <br/>
<b style="color:blue"># GLOBAL ANNOUNCES WITH VIRTUAL FOLDER</b> <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_ANNOUNCES}{EXT_ANNOUNCES}{STATIC_TOPIC}([0-9]+)(-([0-9]+))?{EXT_TOPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.php?t=$1&start=$3 [QSA,L,NC] <br/>
<b style="color:blue"># TOPIC WITHOUT FORUM ID & DELIM</b> <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}[a-z0-9_-]*/?{STATIC_TOPIC}([0-9]+)({DELIM_START}([0-9]+))?{EXT_TOPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.php?t=$1&start=$3 [QSA,L,NC] <br/>' . "\n";
$htaccess_tpl .= $htaccess_common_tpl . '<b style="color:blue"># HERE IS A GOOD PLACE TO ADD OTHER PHPBB RELATED REWRITERULES</b><br/>
<br/>
<b style="color:blue"># FORUM WITHOUT ID & DELIM</b><br/>
<b style="color:blue"># THESE FOUR LINES MUST BE LOCATED AT THE END OF YOUR HTACCESS TO WORK PROPERLY</b><br/>
<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-f<br/>
<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-d<br/>
<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-l<br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}[a-z0-9_-]+{FORUM_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewforum.php?start=$2 [QSA,L,NC]<br/>' . "\n";
} elseif ($modrtype['type'] == 1) {
	$htaccess_tpl .= '<b style="color:blue"># FORUM</b>  <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_FORUM}([0-9]+){FORUM_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewforum.php?f=$1&start=$3 [QSA,L,NC] <br/>
<b style="color:blue"># TOPIC WITH VIRTUAL FOLDER</b>  <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_FORUM}([0-9]+)/{STATIC_TOPIC}({DELIM_START}([0-9]+))?{EXT_TOPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.php?f=$1&t=$2&start=$4 [QSA,L,NC] <br/>
<b style="color:blue"># GLOBAL ANNOUNCES WITH VIRTUAL FOLDER</b> <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}{STATIC_ANNOUNCES}{EXT_ANNOUNCES}{STATIC_TOPIC}([0-9]+)(-([0-9]+))?{EXT_TOPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.php?t=$1&start=$3 [QSA,L,NC] <br/>
<b style="color:blue"># TOPIC WITHOUT FORUM ID & DELIM</b> <br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}[a-z0-9_-]*/?{STATIC_TOPIC}([0-9]+)({DELIM_START}([0-9]+))?{EXT_TOPIC}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewtopic.php?t=$1&start=$3 [QSA,L,NC] <br/>' . "\n";
$htaccess_tpl .= $htaccess_common_tpl . '<b style="color:blue"># HERE IS A GOOD PLACE TO ADD OTHER PHPBB RELATED REWRITERULES</b><br/>
<br/>
<b style="color:blue"># FORUM WITHOUT ID & DELIM</b><br/>
<b style="color:blue"># THESE FOUR LINES MUST BE LOCATED AT THE END OF YOUR HTACCESS TO WORK PROPERLY</b><br/>
<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-f<br/>
<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-d<br/>
<b style="color:green">RewriteCond</b> %{REQUEST_FILENAME} !-l<br/>
<b style="color:green">RewriteRule</b> ^{WIERD_SLASH}{PHPBB_LPATH}[a-z0-9_-]+{FORUM_PAGINATION}$ {DEFAULT_SLASH}{PHPBB_RPATH}viewforum.php?start=$2 [QSA,L,NC]<br/>' . "\n";
} else {
	$redirect_url = append_sid("{$phpbb_root_path}phpbb_seo.$phpEx?htaccess") . '#seo_top';
	meta_refresh(3, $redirect_url);
	trigger_error($user->lang['SEO_MOD_TYPE_ER'] . '<br /><br />' . sprintf($user->lang['RETURN_PAGE'], '<a href="' . $redirect_url . '">', '</a>'));
}
$htaccess_tpl .= '<b style="color:blue"># END PHPBB PAGES  <br/>
#####################################################</b> <br/>';
$default_slash = '/';
$wierd_slash = '';
$phpbb_path = trim($phpbb_seo->seo_path['phpbb_script'], '/');
$show_rewritebase_opt = false;
$rewritebase = '';
$slash = false;
if ( isset($_POST['slash']) ) {
	$slash = ( $_POST['slash'] == 1 ) ? false : true;
}
$wslash = false;
if ( isset($_POST['wslash']) ) {
	$wslash = ( $_POST['wslash'] == 1 ) ? false : true;
}
$rbase = false;
if ( isset($_POST['rbase']) ) {
	$rbase = ( $_POST['rbase'] == 1 ) ? false : true;
}
$wierd_slash = $wslash ? '<b style="color:red">/</b>' : '';
$default_slash = $slash ? '' : '/';
if (!empty($phpbb_path )) {
	$phpbb_path = $phpbb_path . '/';
	if ($rbase) {
		$rewritebase = $phpbb_path;
		$default_slash = $slash ? '/' : '';
	}
	$rewritebase = $rbase ? $phpbb_path : '';
	$show_rewritebase_opt = $phpbb_seo->seo_opt['virtual_root'] ? false : true;
}
$more_options = false;
if (isset($_POST['more_options']) ) {
	$more_options = true;
	$default_slash = !empty($default_slash) ? '<b style="color:red">' . $default_slash . '</b>' : '';
}
// handle the suffixes proper in the RegEx
$seo_ext = array();
foreach ( $phpbb_seo->seo_ext as $type => $value) {
	$seo_ext[$type] = str_replace('.', '\.', $value);
}
if ($phpbb_seo->seo_opt['virtual_folder']) {
	$reg_ex_fpage = $seo_ext['forum'] . '?(<b style="color:#A020F0">' . $phpbb_seo->seo_static['pagination'] . '</b>([0-9]+)<b style="color:#6A5ACD">' . $seo_ext['pagination'] . '</b>)?';
} else {
	$reg_ex_fpage = '(<b style="color:#FF00FF">' . $phpbb_seo->seo_delim['start'] . '</b>([0-9]+))?<b style="color:#6A5ACD">' . $seo_ext['forum'] . '</b>';
}
// Load the .htaccess vars
$htaccess_tpl_vars = array(
	'{REWRITEBASE}' => $rewritebase,
	'{PHPBB_LPATH}' => ($rbase || $phpbb_seo->seo_opt['virtual_root']) ? '' : $phpbb_path, 
	'{PHPBB_RPATH}' => $rbase ? '' : $phpbb_path, 
	'{STATIC_INDEX}' => '<b style="color:#A020F0">' . $phpbb_seo->seo_static['index'] . '</b>', 
	'{STATIC_FORUM}' => '<b style="color:#A020F0">' . $phpbb_seo->seo_static['forum'] . '</b>',  
	'{STATIC_TOPIC}' => '<b style="color:#A020F0">' . $phpbb_seo->seo_static['topic'] . '</b>',  
	'{STATIC_POST}' => '<b style="color:#A020F0">' . $phpbb_seo->seo_static['post'] . '</b>',  
	'{STATIC_MEMBERS}' => '<b style="color:#A020F0">' . $phpbb_seo->seo_static['user'] . '</b>',  
	'{STATIC_ANNOUNCES}' => '<b style="color:#A020F0">' . $phpbb_seo->seo_static['global_announce'] . '</b>',  
	'{STATIC_TEAM}' => '<b style="color:#A020F0">' . $phpbb_seo->seo_static['leaders'] . '</b>', 
       	'{EXT_INDEX}' =>'<b style="color:#6A5ACD">' . $seo_ext['index'] . '</b>',  
	'{EXT_FORUM}' =>'<b style="color:#6A5ACD">' . $seo_ext['forum'] . '</b>',  
	'{EXT_TOPIC}' =>'<b style="color:#6A5ACD">' . $seo_ext['topic'] . '</b>', 
	'{EXT_POST}' =>'<b style="color:#6A5ACD">' . $seo_ext['post'] . '</b>', 
	'{EXT_MEMBERS}' =>'<b style="color:#6A5ACD">' . $seo_ext['user'] . '</b>',  
	'{EXT_ANNOUNCES}' =>'<b style="color:#6A5ACD">' . $seo_ext['global_announce'] . '</b>', 
	'{EXT_TEAM}' =>'<b style="color:#6A5ACD">' . $seo_ext['leaders'] . '</b>',
	'{DELIM_FORUM}' =>'<b style="color:#FF00FF">' . $phpbb_seo->seo_delim['forum'] . '</b>',  
	'{DELIM_TOPIC}' =>'<b style="color:#FF00FF">' . $phpbb_seo->seo_delim['topic'] . '</b>',
	'{DELIM_START}' =>'<b style="color:#FF00FF">' . $phpbb_seo->seo_delim['start'] . '</b>', 
	'{FORUM_PAGINATION}' => $reg_ex_fpage,
	'{DEFAULT_SLASH}' => $default_slash, 
	'{WIERD_SLASH}' => $wierd_slash,
	'{MOD_RTYPE}' => $modrtype[$modrtype['type']],
);
// Parse .htaccess
$htaccess_output = str_replace(array_keys($htaccess_tpl_vars), array_values($htaccess_tpl_vars), $htaccess_tpl);
// Dummy nav links
$template->assign_block_vars('navlinks', array(
	'S_IS_CAT'	=>  true,
	'S_IS_LINK'	=> false,
	'S_IS_POST'	=> false,
	'FORUM_NAME'	=> $user->lang['SEO_HTACCESS_TITLE'],
	'FORUM_ID'	=> 0,
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_seo->seo_path['phpbb_url']}phpbb_seo.$phpEx?htaccess"))
);
// let's go
$template->assign_vars(array(
	'MESSAGE_TITLE' => $user->lang['SEO_HTACCESS_TITLE'],
	'MESSAGE_TEXT' => $user->lang['SEO_HTACCESS_MSG'],	
	'S_CACHE_HTACCESS_ACTION'	=> true,
	'TITLE' => $user->lang['SEO_HTACCESS_TITLE'],
	'TITLE_EXPLAIN' => $user->lang['SEO_HTACCESS_EXPLAIN'],
	'U_CACHE_ACTION'=> $phpbb_seo->seo_path['phpbb_url'] . 'phpbb_seo.php?htaccess',
	'STATUS_IMG'	=> $user->img('announce_read', '', false, '', 'src'),
	'HTACCESS_CODE'	=> $htaccess_output,
	'SEO_SLASH_DEFAULT'	=> $user->lang['SEO_SLASH_DEFAULT'],
	'SEO_SLASH_ALT'	=> $user->lang['SEO_SLASH_ALT'],
	'SEO_HTACCESS_GENED'	=> ($show_rewritebase_opt && $rbase) ? $user->lang['SEO_HTACCESS_FOLDER_MSG'] : $user->lang['SEO_HTACCESS_ROOT_MSG'],
	'SEO_HTACCESS_CAPTION'	=> $user->lang['SEO_HTACCESS_CAPTION'],
	'SEO_HTACCESS_CAPTION_COMMENT'	=> $user->lang['SEO_HTACCESS_CAPTION_COMMENT'],
	'SEO_HTACCESS_CAPTION_STATIC'	=> $user->lang['SEO_HTACCESS_CAPTION_STATIC'],
	'SEO_HTACCESS_CAPTION_SUFFIX'	=> $user->lang['SEO_HTACCESS_CAPTION_SUFFIX'],
	'SEO_HTACCESS_CAPTION_DELIM'	=> $user->lang['SEO_HTACCESS_CAPTION_DELIM'],
	'SEO_HTACCESS_CAPTION_SLASH'	=> $more_options ? $user->lang['SEO_HTACCESS_CAPTION_SLASH'] : false,
	)
);
$template->assign_block_vars('optionrow', array(
	'TITLE' => $user->lang['SEO_MORE_OPTION'],
	'TITLE_EXPLAIN' => $user->lang['SEO_MORE_OPTION_EXPLAIN'],
	'STATUS_IMG'	=> $user->img('announce_unread', '', false, '', 'src'),
	'SEO_UPDATE' => $user->lang['SEO_MORE_OPTION'],
	'OPTION'	=> 'more_options',
	'S_MORE_OPTION'	=> true,
	)
);
if ($submit) {
	if ($more_options) {
		$checked = true;
		if ( isset($_POST['rbase']) ) {
			$checked = ( $_POST['rbase'] == 1 ) ? true : false;
		}
		if ($show_rewritebase_opt) {
			$template->assign_block_vars('optionrow', array(
				'TITLE' => $user->lang['SEO_HTACCESS_RBASE'],
				'TITLE_EXPLAIN' => $user->lang['SEO_HTACCESS_RBASE_EXPLAIN'],
				'STATUS_IMG'	=> $user->img('announce_read', '', false, '', 'src'),
				'SEO_UPDATE' => $user->lang['SEO_UPDATE'],
				'S_CHECKED' => $checked,
				'OPTION'	=> 'rbase',
				)
			);
		}
		$checked = true;
		if ( isset($_POST['slash']) ) {
			$checked = ( $_POST['slash'] == 1 ) ? true : false;
		}
		$template->assign_block_vars('optionrow', array(
			'TITLE' => $user->lang['SEO_HTACCESS_SLASH'],
			'TITLE_EXPLAIN' => $user->lang['SEO_HTACCESS_SLASH_EXPLAIN'],
			'STATUS_IMG'	=> $user->img('announce_read', '', false, '', 'src'),
			'SEO_UPDATE' => $user->lang['SEO_UPDATE'],
			'S_CHECKED' => $checked,
			'OPTION'	=> 'slash',
			)
		);
		if (isset($_POST['slash']) ) {
			$checked = true;
			if ( isset($_POST['wslash']) ) {
				$checked = ( $_POST['wslash'] == 1 ) ? true : false;
			}
			$template->assign_block_vars('optionrow', array(
				'TITLE' => $user->lang['SEO_HTACCESS_WSLASH'],
				'TITLE_EXPLAIN' => $user->lang['SEO_HTACCESS_WSLASH_EXPLAIN'],
				'STATUS_IMG'	=> $user->img('announce_unread', '', false, '', 'src'),
				'SEO_UPDATE' => $user->lang['SEO_UPDATE'],
				'S_CHECKED' => $checked,
				'OPTION'	=> 'wslash',
				)
			);
		}

	}
}
?>
