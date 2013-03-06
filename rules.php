<?php
$phpbb_root_path = './';

define('IN_PHPBB', true);
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/bbcode.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();


$posts_ary = array(
        'SELECT'    => 'p.*, t.*, u.username, u.user_colour',
    
        'FROM'      => array(
            POSTS_TABLE     => 'p',
        ),
    
        'LEFT_JOIN' => array(
            array(
                'FROM'  => array(USERS_TABLE => 'u'),
                'ON'    => 'u.user_id = p.poster_id'
            ),
            array(
                'FROM'  => array(TOPICS_TABLE => 't'),
                'ON'    => 'p.topic_id = t.topic_id'
            ),
        ),
    
        'WHERE'     => ' t.topic_id = 1369',
    
        'ORDER_BY'  => 'p.post_id ASC',
    );
    
    $posts = $db->sql_build_query('SELECT', $posts_ary);

   $posts_result = $db->sql_query_limit($posts, 1);

      while( $posts_row = $db->sql_fetchrow($posts_result) )
      {
         $topic_title       = $posts_row['topic_title'];
         $post_author       = get_username_string('full', $posts_row['poster_id'], $posts_row['username'], $posts_row['user_colour']);
         $post_date          = $user->format_date($posts_row['post_time']);
         $post_link       = append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $posts_row['forum_id'] . '&amp;t=' . $posts_row['topic_id'] . '&amp;p=' . $posts_row['post_id']) . '#p' . $posts_row['post_id'];

         $post_text = nl2br($posts_row['post_text']);

         $bbcode = new bbcode(base64_encode($bbcode_bitfield));         
         $bbcode->bbcode_second_pass($post_text, $posts_row['bbcode_uid'], $posts_row['bbcode_bitfield']);

         $post_text = smiley_text($post_text);

      }


         $template->assign_vars(array(
         'SITE_RULES' => $post_text
         ));


page_header('RolePlayGateway\'s 5 Rules');

$template->set_filenames(array(
	'body' => 'rules.html'
	)
);

page_footer();
