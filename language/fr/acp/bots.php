<?php
/**
*
* acp_bots [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: bots.php, v1.24 2007/11/22 11:07:01 Elglobo Exp $
* @copyright (c) 2005 phpBB Group
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
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

// Bot settings
$lang = array_merge($lang, array(
	'BOTS'				=> 'Gestion des robots',
	'BOTS_EXPLAIN'		=> 'Les “robots” ou “aspirateurs” sont des agents automatisés le plus souvent utilisés par les moteurs de recherches pour mettre à jour leurs bases de données. Etant donné que ceux-ci font rarement une utilisation appropriée des sessions, ils peuvent fausser le compteur de visiteurs, augmenter la charge du serveur et parfois ne pas indexer correctement les sites. Vous pouvez définir un type spécial d’utilisateurs afin de résoudre ces problèmes.',
	'BOT_ACTIVATE'		=> 'Activer',
	'BOT_ACTIVE'		=> 'Robot actif',
	'BOT_ADD'			=> 'Ajouter un robot',
	'BOT_ADDED'			=> 'Nouveau robot ajouté.',
	'BOT_AGENT'			=> 'Agent correspondant',
	'BOT_AGENT_EXPLAIN'	=> 'Une chaîne de caractères correspondante à l’agent du robot, les correspondances partielles sont autorisées.',
	'BOT_DEACTIVATE'	=> 'Désactiver',
	'BOT_DELETED'		=> 'Robot supprimé.',
	'BOT_EDIT'			=> 'Editer les robots',
	'BOT_EDIT_EXPLAIN'	=>  'Cette page vous permet d’éditer un robot existant ou d’en ajouter un nouveau. Vous pouvez définir une chaîne de caractères pour l’agent et/ou une ou plusieurs adresses IPs (ou une série d’adresses) correspondantes. Faites attention en définissant la chaîne de caractères correspondants à l’agent ou aux adresses. Vous pouvez également indiquer un style et une langue que le robot visualisera lorsqu’il sera sur le forum. Cela peut vous permettre de réduire la bande passante utilisée en configurant un style simple pour les robots. N’oubliez pas de mettre les permissions appropriées au groupe d’utilisateurs spécial robot.',
	'BOT_LANG'			=> 'Langue du robot',
	'BOT_LANG_EXPLAIN'	=> 'Langue présentée au robot lors de son passage.',
	'BOT_LAST_VISIT'	=> 'Dernière visite',
	'BOT_IP'			=> 'Adresse IP du robot',
	'BOT_IP_EXPLAIN'	=> 'Les correspondances partielles sont autorisées, séparez les adresses par une virgule.',
	'BOT_NAME'			=> 'Nom du robot',
	'BOT_NAME_EXPLAIN'	=> 'Utilisé uniquement pour information.',
	'BOT_NAME_TAKEN'	=> 'Ce nom est déjà utilisé sur votre forum et ne peut être utilisé pour le robot.',
	'BOT_NEVER'			=> 'Jamais',
	'BOT_STYLE'			=> 'Style du robot',
	'BOT_STYLE_EXPLAIN'	=> 'Le style utilisé par le robot sur le forum.',
	'BOT_UPDATED'		=> 'Robot mis à jour.',

	'ERR_BOT_AGENT_MATCHES_UA'	=> 'L’agent du robot indiqué est identique à celui que vous utilisez actuellement. Fournissez un autre agent pour ce robot.',
	'ERR_BOT_NO_IP'				=> 'Les adresses IPs que vous avez fournies sont invalides ou le nom de domaine ne peut pas être résolu.',
	'ERR_BOT_NO_MATCHES'		=> 'Vous devez fournir au moins un agent ou une IP pour correspondre à ce robot.',

	'NO_BOT'		=> 'Il n’y a pas de robot avec cette ID.',
	'NO_BOT_GROUP'	=> 'Impossible de trouver le groupe spécial du robot.',
));

?>