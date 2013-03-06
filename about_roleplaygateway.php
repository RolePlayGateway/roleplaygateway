<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_display.' . $phpEx);


// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('viewforum');


page_header('About Us: '.$config['sitename']);

// GET Owner (hard coded by ID at mo) 
	$sql="SELECT username,user_avatar,user_avatar_type,user_avatar_width,user_avatar_height FROM gateway_users WHERE user_id=4";
 	$result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result)) {
		if($row['user_avatar_type']==1)
		{
		$row['user_avatar']= "http://www.roleplaygateway.com/download/file.php?avatar=" .$row['user_avatar'];
		$row['user_avatar_type']=2;
		}
		$avatar = get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height'], 'USER_AVATAR', true);
		if ($avatar =="")
		{
			$avatar = "<img src='http://www.roleplaygateway.com/images/no.photo.gif' style='height: 80px;'>";
		}
                 $template->assign_block_vars('owners', array(
					'NAME'  =>$row['username'],
					'GROUP' =>'Owner',
					'URL'   =>$row['username'],
					'AVATAR'=>$avatar
			 ));
        }
        $db->sql_freeresult($result);
// GET Admins
	$sql="SELECT username,user_avatar,user_avatar_type,user_avatar_width,user_avatar_height FROM gateway_users WHERE user_id<>4 AND group_id=2626 AND user_id<>0";
 	$result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result)) {
		if($row['user_avatar_type']==1)
		{
		$row['user_avatar']= "http://www.roleplaygateway.com/download/file.php?avatar=" .$row['user_avatar'];
		$row['user_avatar_type']=2;
		}
		$avatar = get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height'], 'USER_AVATAR', true);
		if ($avatar =="")
		{
			$avatar = "<img src='http://www.roleplaygateway.com/images/no.photo.gif' style='height: 80px;'>";
		}	
                 $template->assign_block_vars('admins', array(
					'NAME'  =>$row['username'],
					'GROUP' =>'Administrator',
					'URL'   =>$row['username'],
					'AVATAR'=>$avatar
			 ));
        }
        $db->sql_freeresult($result);

// GET leaders.
	$sql="SELECT a.group_id,a.user_id,group_name,username,user_avatar,user_avatar_type,user_avatar_width,user_avatar_height
FROM gateway_user_group a 
INNER JOIN gateway_groups b ON a.group_id=b.group_id
INNER JOIN gateway_users c ON c.user_id=a.user_id
WHERE group_leader=1 AND a.group_id IN (2625,2629,2635) ORDER BY group_name";
 	$result = $db->sql_query($sql);
        while ($row = $db->sql_fetchrow($result)) {
		if($row['user_avatar_type']==1)
		{
		$row['user_avatar']= "http://www.roleplaygateway.com/download/file.php?avatar=" .$row['user_avatar'];
		$row['user_avatar_type']=2;
		}
		$avatar = get_user_avatar($row['user_avatar'], $row['user_avatar_type'], $row['user_avatar_width'], $row['user_avatar_height'], 'USER_AVATAR', true);
		if ($avatar =="")
		{
			$avatar = "<img src='http://www.roleplaygateway.com/images/no.photo.gif' style='height: 80px;'>";
		}
		 $template->assign_block_vars('leaders', array(
					'NAME'  =>$row['username'],
					'GROUP' =>ucwords(strtolower(str_replace("_"," ",$row['group_name']))),
					'URL'   =>$row['username'],
					'AVATAR'=>$avatar
			 ));
        }
        $db->sql_freeresult($result);



#$this->tpl_name = 'about_page';
#$this->page_title = 'About Us: '.$config['sitename'];

$template->set_filenames(array(
	'body' => 'about_page.html'
	)
);


page_footer();

?>
