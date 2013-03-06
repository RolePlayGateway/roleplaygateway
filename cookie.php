<?php

    /**
    *
    * @package Cookie and script settings tool
    * @copyright (c) 2008 ktuk.net
    * @license GPL
    *
    */

    // Standard definitions/includes
    $page_title = 'phpBB3 Cookies';
    define('IN_PHPBB', true);
    $root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
    $phpEx = substr(strrchr(__FILE__, '.'), 1);
    include($root_path . 'common.' . $phpEx);

    $cookie_data = array();
    $server_data = array();
    $sent = request_var('sent', '');

    $sql = ' SELECT config_value FROM ' . CONFIG_TABLE . "
    WHERE config_name = 'cookie_domain'";
    $result = $db->sql_query($sql);
    $cookie_data[] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = ' SELECT config_value FROM ' . CONFIG_TABLE . "
    WHERE config_name = 'cookie_name'";
    $result = $db->sql_query($sql);
    $cookie_data[] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = ' SELECT config_value FROM ' . CONFIG_TABLE . "
    WHERE config_name = 'cookie_path'";
    $result = $db->sql_query($sql);
    $cookie_data[] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = ' SELECT config_value FROM ' . CONFIG_TABLE . "
    WHERE config_name = 'cookie_secure'";
    $result = $db->sql_query($sql);
    $cookie_data[] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = ' SELECT config_value FROM ' . CONFIG_TABLE . "
    WHERE config_name = 'force_server_vars'";
    $result = $db->sql_query($sql);
    $server_data[] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = ' SELECT config_value FROM ' . CONFIG_TABLE . "
    WHERE config_name = 'script_path'";
    $result = $db->sql_query($sql);
    $server_data[] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = ' SELECT config_value FROM ' . CONFIG_TABLE . "
    WHERE config_name = 'server_name'";
    $result = $db->sql_query($sql);
    $server_data[] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = ' SELECT config_value FROM ' . CONFIG_TABLE . "
    WHERE config_name = 'server_port'";
    $result = $db->sql_query($sql);
    $server_data[] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $sql = ' SELECT config_value FROM ' . CONFIG_TABLE . "
    WHERE config_name = 'server_protocol'";
    $result = $db->sql_query($sql);
    $server_data[] = $db->sql_fetchrow($result);
    $db->sql_freeresult($result);

    $cookie_checked = ($cookie_data[3]['config_value']) ? "checked = \"checked\"" : '';
    $force_checked = ($server_data[0]['config_value']) ? "checked = \"checked\"" : '';

    if (!$sent)
    {
    print "<html>
    <head><title>$page_title</title></head>
    <body>
    <form action=\"" . $_SERVER['SCRIPT_NAME'] . "\" method=\"post\"
    <fieldset>
    <table>
    <tr><h2>Cookie Settings</h2></tr>
    <tr><td>Cookie Domain:</td><td><input type=\"text\" name=\"cookie_domain\" value=\"" . $cookie_data[0]['config_value'] . "\"></td></tr>
    <tr><td>Cookie Name:</td><td><input type=\"text\" name=\"cookie_name\" value=\"" . $cookie_data[1]['config_value'] . "\"></td></tr>
    <tr><td>Cookie Path:</td><td><input type=\"text\" name=\"cookie_path\" value=\"" . $cookie_data[2]['config_value'] . "\"></td></tr>
    <tr><td>Cookie Secure:</td><td><input type=\"checkbox\" name=\"cookie_secure\" value=\"1\" $cookie_checked ></td></tr>
    </table>

    <table>
    <tr><h2>Server Settings</h2></tr>
    <tr><td>Force Server Vars:</td><td><input type=\"checkbox\" name=\"force_server_vars\" value=\"1\"  $force_checked ></td></tr>
    <tr><td>Script Path:</td><td><input type=\"text\" name=\"script_path\" value=\"" . $server_data[1]['config_value'] . "\"></td></tr>
    <tr><td>Server Name:</td><td><input type=\"text\" name=\"server_name\" value=\"" . $server_data[2]['config_value'] . "\"></td></tr>
    <tr><td>Server Port:</td><td><input type=\"text\" name=\"server_port\" value=\"" . $server_data[3]['config_value'] . "\"></td></tr>
    <tr><td>Server Protocol:</td><td><input type=\"text\" name=\"server_protocol\" value=\"" . $server_data[4]['config_value'] . "\"></td></tr>
    <tr><td>Submit:</td><td><input type=\"submit\" name=\"sent\" value=\"submit\"></td></tr>
    </table>
    </fieldset>
    </body>
    <html>
    ";
    }

    if ($sent)
    {
       $cookie_domain   =   request_var('cookie_domain', '');
       $cookie_name   =   request_var('cookie_name', '');
       $cookie_path   =   request_var('cookie_path', '');
       $cookie_secure   =   request_var('cookie_secure', 0);
       $force_server_vars   = request_var('force_server_vars', 0);
       $script_path   =   request_var('script_path', '');
       $server_name   =   request_var('server_name', '');
       $server_port   = request_var('server_port', 80);
       $server_protocol   = request_var('server_protocol', 'http://');
       
       $sql = ' UPDATE ' . CONFIG_TABLE . " SET `config_value` = '$cookie_domain' WHERE `config_name` = 'cookie_domain'";
       $db->sql_query($sql);
       $sql = ' UPDATE ' . CONFIG_TABLE . " SET `config_value` = '$cookie_name' WHERE `config_name` = 'cookie_name'";
       $db->sql_query($sql);
       $sql = ' UPDATE ' . CONFIG_TABLE . " SET `config_value` = '$cookie_path' WHERE `config_name` = 'cookie_path'";
       $db->sql_query($sql);
       $sql = ' UPDATE ' . CONFIG_TABLE . " SET `config_value` = '$cookie_secure' WHERE `config_name` = 'cookie_secure'";
       $db->sql_query($sql);
       $sql = ' UPDATE ' . CONFIG_TABLE . " SET `config_value` = '$force_server_vars' WHERE `config_name` = 'force_server_vars'";
       $db->sql_query($sql);
       $sql = ' UPDATE ' . CONFIG_TABLE . " SET `config_value` = '$script_path' WHERE `config_name` = 'script_path'";
       $db->sql_query($sql);
       $sql = ' UPDATE ' . CONFIG_TABLE . " SET `config_value` = '$server_name' WHERE `config_name` = 'server_name'";
       $db->sql_query($sql);
       $sql = ' UPDATE ' . CONFIG_TABLE . " SET `config_value` = '$server_port' WHERE `config_name` = 'server_port'";
       $db->sql_query($sql);
       $sql = ' UPDATE ' . CONFIG_TABLE . " SET `config_value` = '$server_protocol' WHERE `config_name` = 'server_protocol'";
       $db->sql_query($sql);
       
       print "Settings updated!";
       print "<meta http-equiv=\"refresh\" content=\"2;./cookie.php\">";
    }

    ?>
