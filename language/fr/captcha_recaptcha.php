<?php
/**
*
* recaptcha [Standard french]
* translated by PhpBB-fr.com <http://www.phpbb-fr.com/>
*
* @package language
* @version $Id: captcha_recaptcha.php v1.25 2009-10-16 16:01:00 Elglobo $
* @copyright (c) 2009 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'RECAPTCHA_LANG'				=> 'fr',
	'RECAPTCHA_NOT_AVAILABLE'		=> 'Afin d’utiliser reCaptcha, vous devez créer un compte sur <a href="http://recaptcha.net">reCaptcha.net</a>.',
	'CAPTCHA_RECAPTCHA'				=> 'reCaptcha',
	'RECAPTCHA_INCORRECT'			=> 'Le code de confirmation visuelle soumis était incorrect',

	'RECAPTCHA_PUBLIC'				=> 'Clé publique reCaptcha',
	'RECAPTCHA_PUBLIC_EXPLAIN'		=> 'Votre clé publique reCaptcha. Des clés peuvent être obtenus sur <a href="http://recaptcha.net">reCaptcha.net</a>.',
	'RECAPTCHA_PRIVATE'				=> 'Clé privée reCaptcha',
	'RECAPTCHA_PRIVATE_EXPLAIN'		=> 'Votre clé privée reCaptcha. Des clés peuvent être obtenus sur <a href="http://recaptcha.net">reCaptcha.net</a>.',

	'RECAPTCHA_EXPLAIN'				=> 'Afin d’empêcher les inscriptions automatisées, nous vous demandons de taper les deux mots affichés ci-dessous dans le champ texte ci-contre.',
));

?>