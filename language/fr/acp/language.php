<?php
/**
*
* acp_language [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: language.php, v1.27 2010/02/09 19:13:00 Elglobo Exp $
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
	'ACP_FILES'						=> 'Fichiers de langue de l’administration',
	'ACP_LANGUAGE_PACKS_EXPLAIN'	=> 'Vous pouvez installer/supprimer des packs de langue. Le pack de langue par défaut est marqué d’un astérisque (*).',

	'EMAIL_FILES'			=> 'Modèles d’e-mail',

	'FILE_CONTENTS'				=> 'Contenu du fichier',
	'FILE_FROM_STORAGE'			=> 'Fichier du dossier de stockage',

	'HELP_FILES'				=> 'Fichiers d’aide',

	'INSTALLED_LANGUAGE_PACKS'	=> 'Packs de langue installés',
	'INVALID_LANGUAGE_PACK'		=> 'Le pack sélectionné semble invalide. Vérifiez-le et recommencez le chargement si nécessaire.',
	'INVALID_UPLOAD_METHOD'		=> 'La méthode de chargement choisie est invalide, choisissez-en une autre.',

	'LANGUAGE_DETAILS_UPDATED'			=> 'Informations de langue mises à jour.',
	'LANGUAGE_ENTRIES'					=> 'Entrées de langue',
	'LANGUAGE_ENTRIES_EXPLAIN'			=> 'Vous pouvez modifier les entrées de pack de langue existantes ou non encore traduites. <br /><strong>Note:</strong> Une fois le fichier de langue modifié, les modifications seront enregistrées dans un dossier séparé que vous pourrez télécharger. Les modifications ne seront pas visibles par les utilisateurs jusqu’à ce que vous remplaciez les fichiers originaux sur votre espace Web (en les chargeant).',
	'LANGUAGE_FILES'					=> 'Fichiers de langue',
	'LANGUAGE_KEY'						=> 'Clé de langue',
	'LANGUAGE_PACK_ALREADY_INSTALLED'	=> 'Ce pack de langue est déjà installé.',
	'LANGUAGE_PACK_DELETED' 			=> 'Le pack de langue <strong>%s</strong> a été supprimé. La langue est désormais celle par défaut du forum pour les membres qui utilisaient ce pack.',
	'LANGUAGE_PACK_DETAILS'				=> 'Informations du pack',
	'LANGUAGE_PACK_INSTALLED'			=> 'Le pack de langue <strong>%s</strong> a été installé.',
	'LANGUAGE_PACK_ISO'					=> 'ISO',
	'LANGUAGE_PACK_LOCALNAME'			=> 'Nom local',
	'LANGUAGE_PACK_NAME'				=> 'Nom',
	'LANGUAGE_PACK_NOT_EXIST'			=> 'Le pack de langue choisi n’existe pas.',
	'LANGUAGE_PACK_USED_BY'				=> 'Utilisé par (robots inclus)',
	'LANGUAGE_VARIABLE'					=> 'Variable de langue',
	'LANG_AUTHOR'						=> 'Auteur du pack de langue',
	'LANG_ENGLISH_NAME'					=> 'Nom Anglais',
	'LANG_ISO_CODE'						=> 'Code ISO',
	'LANG_LOCAL_NAME'					=> 'Nom local',

	'MISSING_LANGUAGE_FILE'		=> 'Fichier de langue absent: <strong style="color:red">%s</strong>',
	'MISSING_LANG_VARIABLES'	=> 'Variables de langue absentes',
	'MODS_FILES'				=> 'Fichiers de langue des MODs',

	'NO_FILE_SELECTED'				=> 'Vous n’avez pas indiqué de fichier.',
	'NO_LANG_ID'					=> 'Vous n’avez pas indiqué de pack de langue',
	'NO_REMOVE_DEFAULT_LANG'		=> 'Vous ne pouvez pas supprimer le pack de langue par défaut.<br />Si vous voulez supprimer ce pack, changez d’abord la langue par défaut du forum.',
	'NO_UNINSTALLED_LANGUAGE_PACKS'	=> 'Aucun pack de langue installé',

	'REMOVE_FROM_STORAGE_FOLDER'		=> 'Supprimer du dossier de stockage',

	'SELECT_DOWNLOAD_FORMAT'	=> 'Choisissez le format de téléchargement',
	'SUBMIT_AND_DOWNLOAD'		=> 'Soumettre et télécharger le fichier',
	'SUBMIT_AND_UPLOAD'			=> 'Soumettre et charger le fichier',

	'THOSE_MISSING_LANG_FILES'			=> 'Les fichiers de langue suivants sont absents du dossier de langue %s',
	'THOSE_MISSING_LANG_VARIABLES'		=> 'Les variables de langue suivantes sont absentes du pack <strong>%s</strong>',

	'UNINSTALLED_LANGUAGE_PACKS'	=> 'Packs non installés',

	'UNABLE_TO_WRITE_FILE'		=> 'Le fichier n’a pas pu être enregistré dans %s.',
	'UPLOAD_COMPLETED'			=> 'Le chargement est terminé',
	'UPLOAD_FAILED'				=> 'Le chargement a échoué pour une raison inconnue. Remplacez le fichier manuellement.',
	'UPLOAD_METHOD'				=> 'Méthode de chargement',
	'UPLOAD_SETTINGS'			=> 'Paramètres de chargement',

	'WRONG_LANGUAGE_FILE'		=> 'Le fichier de langue choisi est invalide.',
));

?>