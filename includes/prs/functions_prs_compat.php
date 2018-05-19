<?php

function is_votable_basis($post_id, $score, $trigger = FALSE) { return prs_is_votable_basis($post_id, $score, $trigger); }
function are_closed($post_list) { return prs_are_closed($post_list); }
function is_voteround_open_basis($post_id, $row = NULL, $score = 3, $trigger = FALSE) { return prs_is_voteround_open_basis($post_id, $row, $score = 3, $trigger); }
function &get_votes_dataset($post_list, $user_list = NULL, $sql_array = NULL, $related_tables = TRUE) { return prs_get_votes_dataset($post_list, $user_list, $sql_array, $related_tables); }
function &get_votes_dataset_simple($post_list, $user_list = NULL, $sql_array = NULL) { return prs_get_votes_dataset_simple($post_list, $user_list, $sql_array); }
function display_votes(&$dataset, $post_list = NULL, $user_list = NULL) { return prs_display_votes($dataset, $post_list, $user_list); }
function posts_ratings_close_posts($post_list = NULL, $force = FALSE) { return prs_close_posts($post_list, $force); }
function determine_score($votable, $user_id, &$data, $admin = FALSE) { return prs_determine_score($votable, $user_id, $data, $admin); }
function submit_vote($mode, &$data) { return prs_submit_vote($mode, $data); }
function &posts_ratings_fetch_votes_posts($post_list, &$rowset) { return prs_fetch_votes_posts($post_list, $rowset); }
function &posts_ratings_display_rating_posts(&$row, &$dataset, $admin = FALSE) { return prs_display_rating_posts($row, $dataset, $admin); }
function &posts_ratings_fetch_votes_topics($topic_list, &$rowset) { return prs_fetch_votes_topics($topic_list, $rowset); }
function &posts_ratings_display_rating_topics(&$row, &$dataset, $admin = FALSE) { return prs_display_rating_topics($row, $dataset, $admin); }
function &posts_ratings_fetch_votes($list, &$rowset, $mode = 'posts') { return prs_fetch_votes($list, $rowset, $mode); }
function &posts_ratings_display_rating($row, &$dataset, $forum_id, $mode = 'posts', $admin = FALSE) { return prs_display_rating($row, $dataset, $forum_id, $mode, $admin); }
function posts_ratings_delete_votes($post_id) { return prs_delete_votes($post_id); }
function posts_ratings_new_posts(&$post_data) { return prs_new_posts($post_data); }
function posts_ratings_delete_post($post_id, &$data) { return prs_delete_post($post_id, $data); }
function posts_ratings_change_owner($post_id, $new_owner, $post_time) { return prs_change_owner($post_id, $new_owner, $post_time); }
function &posts_ratings_profile($user_id) { return prs_profile($user_id); }
function posts_ratings_cron() { return prs_cron(); }
function get_post_from_topic($topic_list, $all = FALSE) { return prs_get_post_from_topic($topic_list, $all); }
function posts_ratings_set_score($score, $mode, $action, $data, $force = FALSE) { return prs_set_score($score, $mode, $action, $data, $force); }
function posts_ratings_vote_unlock($mode, $action, $data) { return prs_vote_unlock($mode, $action, $data); }
function posts_ratings_vote_lock($mode, $data) { return prs_vote_lock($mode, $data); }
function posts_ratings_mcp_main($mode, $action, $quickmod) { return prs_mcp_main($mode, $action, $quickmod); }
function karma($user_id) { return prs_karma($user_id); }
function modpoints($user_id) { return prs_modpoints($user_id); }
function is_votable_modpoints($post_id, $trigger = FALSE) { return prs_is_votable_modpoints($post_id, $trigger); }
function reduce_modpoints($user_id, $number = 1) { return prs_reduce_modpoints($user_id, $number = 1); }
function reduce_modpoints_deleted_post($post_id) { return prs_reduce_modpoints_deleted_post($post_id); }
function increase_modpoints($user_id, $post_id, $time = 0, $factor = 1) { return prs_increase_modpoints($user_id, $post_id, $time = 0, $factor = 1); }
function clean_modpoints() { return prs_clean_modpoints(); }
function is_votable_penalty($post_id, $trigger = FALSE) { return prs_is_votable_penalty($post_id, $trigger); }
function penalty($user_id, $poster_id = 0) { return prs_penalty($user_id, $poster_id = 0); }
function determine_penalties() { return prs_determine_penalties(); }
function update_votes_chi_table($users = NULL) { return prs_update_votes_chi_table($users); }
function declair_post_shadowed($post_id) { return prs_declair_post_shadowed($post_id); }
function create_shadow_votes() { return prs_create_shadow_votes(); }
function posts_ratings_stat_normal_pdf($x, $u, $o, $s) { return prs_stat_normal_pdf($x, $u, $o, $s); }
function posts_ratings_stat_variables_cmd($a, $b) { return prs_stat_variables_cmd($a, $b); }
function &posts_ratings_stat_standard_diviation($data, $u, $n) { return prs_stat_standard_diviation($data, $u, $n); }
function &posts_ratings_stat_variables($data) { return prs_stat_variables($data); }
function posts_ratings_stat_normal($z) { return prs_stat_normal($z); }
function posts_ratings_stat_normal_reverse($p) { return prs_stat_normal_reverse($p); }
function posts_ratings_stat_chi($v, $k) { return prs_stat_chi($v, $k); }
function &posts_ratings_switches($mode = '', $data = NULL) { return prs_switches($mode, $data); }
function posts_ratings_is_locked() { return prs_is_locked(); }
function posts_ratings_lock() { return prs_lock(); }
function posts_ratings_unlock() { return prs_unlock(); }
function &get_post_list_time($min = 0, $max = 0) { return prs_get_post_list_time($min = 0, $max = 0); }
function &get_first_post_in_post_list($post_list) { return prs_get_first_post_in_post_list($post_list); }
function &get_select_post($post_id) { return prs_get_select_post($post_id); }
function sql_multiselect($prefixes, $columns) { return prs_sql_multiselect($prefixes, $columns); }
function sql_update_post($post_id, $data) { return prs_sql_update_post($post_id, $data); }
function is_votable($post_id, $score = 3, $trigger = FALSE) { return prs_is_votable($post_id, $score = 3, $trigger); }
function is_voteround_open($post_id, $row = NULL, $score = 3, $trigger = FALSE) { return prs_is_voteround_open($post_id, $row, $score = 3, $trigger); }
function &posts_ratings_stars($data, $base = 'posts_ratings_star_s_', $n = 2) { return prs_stars($data, $base, $n = 2); }
function &users_who_voted_n_time($n) { return prs_users_who_voted_n_time($n); }

?>
