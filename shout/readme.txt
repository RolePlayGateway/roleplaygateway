/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */


AJAX Chat
=========

This is the phpBB3 integration version:
http://www.phpbb.com/


AJAX stands for "Asynchronous JavaScript and XML".
The AJAX Chat clients (the user browsers) use JavaScript to query the web server for updates.
Instead of delivering a complete HTML page only updated data is send in XML format.
By using JavaScript the chat page can be updated without having to reload the whole page.




Requirements
============

Server-Side:

- PHP >= 4
- MySQL >= 4
- Ruby >= 1.8 (optional)


Client-Side:

- Enabled JavaScript
- Enabled Cookies
- Flash Plugin >= 9 (optional)




Installation
============

1.	Creation of database tables
	---------------------------

	Execute the provided SQL script chat.sql by using phpMyAdmin:
	http://www.phpmyadmin.net/
	
	On phpMyAdmin, select your phpBB database and click on "Import".
	Select the file chat.sql from your local harddisk and click on "OK".
	This should execute the included commands to create the tables needed by this chat.


2.	Upload to the server
	--------------------

	Upload the chat folder to your server into your phpBB forum directory:
	e.g. http://example.org/phpBB3/chat/


Ready! Just place a link to the chat directory on your forum. :)




Configuration files:
====================

AJAX Chat is fully customizable and contains two configuration files:

1.	lib/config.php
	--------------
	This file contains the server side (PHP) settings.

2.	js/config.js
	------------
	This file contains the client side (JavaScript) settings.

Each configuration option is explained with a comment prior to the setting assignment.




Customizing the layout:
=======================

The layout of AJAX Chat is fully customizable by using CSS (Cascaded Style Sheets).
AJAX Chat comes with a predefined set of styles. To add your own style, do the following:

1.	Add a new CSS file (e.g. mystyle.css) by copying one of the existing styles from the CSS directory.

2.	Edit your file (css/mystyle.css) and adjust the CSS settings to your liking.

3.	Add the name of your style without file extension to the available styles in lib/config.php:

	// Available styles:
	$config['styleAvailable'] = array('mystyle','beige','black','grey');
	// Default style:
	$config['styleDefault'] = 'mystyle';

To further customize the layout you can adjust the template files in lib/template/.

Make sure you are creating valid XHTML, else you will produce errors in modern browsers.
This is due to the page content-type served as "application/xhtml+xml".
Using this content-type improves performance when manipulating the Document Object Model (DOM).

If for some reason you cannot create valid XHTML you can force a HTML content-type.
Just edit lib/config.php and set the following option:

	$config['contentType'] = 'text/html';




Adjusting the language settings:
================================

AJAX Chat comes with two language file directories:

1.	js/lang/
	------------
	This directory contains the language files used for the chat messages localization.
	These are JavaScript files with the extension ".js".

2.	lib/lang/
	--------------
	This directory contains the language files used for the template output.
	These are PHP files with the extension ".php".


For each language, you need a file in each of these directories, with the language code as file name.
The language code is used following the ISO 639 standards.

The files for the english (language code "en") localization are the following:

	js/lang/en.js
	lib/lang/en.php

To enable a language, you need to add the language code in lib/config.php:

	$config['langAvailable'] = array('en');

For the language selection you also need to add the language name:

	$config['langNames'] = array('en'=>'English');

To avoid errors, you should follow these rules:

	1. Make sure you encode your localization files in UTF-8 (without Byte-order mark).
	2. Don't use HTML entities in your localization files.
	3. Don't remove any "%s" inside the JavaScript language files - these are filled with dynamic data.




Logs:
=====
	
By default, AJAX Chat stores all chat messages in the database.
To access the logs you have to add the GET parameter view=logs to your chat url:

	e.g. http://example.org/path/to/chat/?view=logs

If you are not already logged in, you have to login as administrator to access the logs.

The log view enables you to monitor the latest chat messages on all channels.
It is also possible to view the logs of private rooms and private messages.
You have the option to filter the logs by date, time and search strings.

The search filter accepts MySQL style regular expressions:

	http://dev.mysql.com/doc/refman/5.1/en/regexp.html

To search for IPs, use the following syntax:

	ip=127.0.0.1




Shoutbox:
=========
	
AJAX Chat is also usable as shoutbox - this is a short guide on how to set it up:


1. Shoutbox stylesheet
----------------------

Add the following line to the stylesheet (CSS) of all pages displaying the shoutbox:

@import url("http://example.org/path/to/chat/css/shoutbox.css");

Replace http://example.org/path/to/chat/ with the URL to the chat.
Modify css/shoutbox.css to your liking.


2. Shoutbox function
--------------------

Add the following function to your PHP code:

<?php
function getShoutBoxContent() {
	// URL to the chat directory:
	if(!defined('AJAX_CHAT_URL')) {
		define('AJAX_CHAT_URL', './chat/');
	}
	
	// Path to the chat directory:
	if(!defined('AJAX_CHAT_PATH')) {
		define('AJAX_CHAT_PATH', realpath(dirname($_SERVER['SCRIPT_FILENAME']).'/chat').'/');
	}
	
	// Validate the path to the chat:
	if(@is_file(AJAX_CHAT_PATH.'lib/classes.php')) {
		
		// Include Class libraries:
		require_once(AJAX_CHAT_PATH.'lib/classes.php');
		
		// Initialize the shoutbox:
		$ajaxChat = new CustomAJAXChatShoutBox();
		
		// Parse and return the shoutbox template content:
		return $ajaxChat->getShoutBoxContent();
	}
	
	return null;
}
?>

Make sure AJAX_CHAT_URL and AJAX_CHAT_PATH point to the chat directory.


2. Shoutbox output
------------------

Display the shoutbox content using the shoutbox function:

<div style="width:200px;"><?php echo getShoutBoxContent(); ?></div>




Socket Server:
==============

Using the AJAX technology the chat clients have to permanently pull updates from the server.
This is due to AJAX being a web technology and HTTP being a stateless protocol.
Using a permanent socket connection between clients and server it is possible to push updates from the server.
With JavaScript alone it is not possible to establish permanent socket connections.

AJAX Chat uses a JavaScript-to-Flash brigde to establish a permanent socket connection from client side.
The JavaScript-to-Flash brigde requires a Flash plugin >= 9 installed on the user browser.
Clients without this requirement will fall back to pull the server for updates.

On server side you need to be able to start a custom Flash XML Socket Server.
The socket server coming with AJAX Chat is implemented in Ruby - you will find it in the socket/ directory.
To be able to start the service, the script files have to be executable:

	$ chmod +x server
	$ chmod +x server.rb

"server" is a simple bash script to start and stop a service.
"server.rb" is the ruby socket server script.
"server.conf" is a configuration file - each setting is explained with a comment.

To start the service, execute the "server" script with the parameter "start":

	$ ./server start

This will create two additional files:

"server.pid" contains the process id of the service.
"server.log" is filled with the socket server log.

To monitor the socket server logs, you can use the following command:

	$ tail -f server.log

To stop the service, execute the "server" script with the parameter "stop":

	$ ./server stop

If the socket server is running, you have to enable the following option in lib/config.php:

	$config['socketServerEnabled'] = true;
	
This tells the server-side chat script to push updates to the socket server.
It tells clients to establish a permanent connection to the socket server to listen for updates.

Clients connected to the socket server will only send update requests to the web server if

	1. The socket server update is not caused by their own messages sent.
	2. The socket server update applies to their current channel (or private message box).
	3. The socket server update is relevant to their view (e.g. the shoutbox does not display channel messages).

Using the socket server increases response time while improving server performance at the same time.



