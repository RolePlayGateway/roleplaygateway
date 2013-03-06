<?php
// Advanced Textual Confirmation settings
// (c) 2007 bbAntiSpam, info@ http://bbantispam.com/atc/
global $questions, $lang, $license_key, $confirmation_page;

//
// The text before "=>" is a question. The array after "=>" is
// the valid answers. You should not use the special symbols ("<",
// ">", "&", "\", "$", '"' and "'") unless you know HTML and PHP.
//
$questions = array(
  'Are you human?' => array ('yes', 'ja', 'oui'),
  'Say "Hello"'    => array ('hello', 'hi', 'hallo')
);

//
// The text messages. Again, don't use the special symbols.
//
$lang = array (
  'CHARSET'  => 'iso-8859-1',
  'TITLE'    => 'Advanced Textual Confirmation',
  'EXPLAIN'  => 'Answer the question to prove you are not a spam bot, but a human.',
  'SUBMIT'   => 'Submit Answer',
  'FOOTNOTE' => 'This site is protected with <a href="http://bbantispam.com/atc/" target="_blank">Advanced Textual Confirmation</a> from <a href="http://bbantispam.com/" target="_blank">bbAntiSpam</a>.'
);

//
// The layout of the confirmation page can be changed only
// if you have a license key. Otherwise Advanced Textual
// Confirmation stops working.
//
$license_key = '00000000';
$confirmation_page = <<<EOT
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset={CHARSET}">
<title>{TITLE}</title>
<style type="text/css">
body { font-family:Verdana, Arial, Helvetica, sans-serif; font-size:100%; background-color:#cde4f1; color:#000000; }
a:link, a:visited  { color:#6699FF; }
a:hover, a:active  { color:#FFCC66; text-decoration:underline; }
table { border:0px; border-collapse:collapse; padding:0px; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:100%; width:100%; height:100%; background-color:#FFFFFF; vertical-align:middle; }
td { text-align:center; vertical-align:middle; }
div { border:none; margin:0px; padding:0px; }
.footnote { font-size: 66%; background-color:#ffffcc; text-align:center; margin:0px; padding:15px; }
.yell { background: #ffffff; height:100%; }
.question { background: #FCD141; }
</style>
</head>
<body>
<table>
<tr><td class="footnote">&nbsp;</td></tr>
<tr><td class="yell">
<p>{EXPLAIN}</p>
<span class="question">{QUESTION}</span><br><br>
<form action="{ACTION}" method="post">
<input name="{FIELD_ANSWER}" size="30" value="" type="text"><br><br>
<input value="{SUBMIT}" type="submit">
{HIDDEN_FIELDS}
</form>
</td></tr>
<tr height="40" valign="bottom"><td>
<div class="footnote">{FOOTNOTE}
<img src="/{RANDOM}_Advanced_Textual_Confirmation_Is_Shareware_Please_Buy_At_bbAntiSpam_dot_com.png" alt="" title="" border="0" height="1" width="1"></div>
</td>
</tr></table>
</body></html>
EOT;

?>
