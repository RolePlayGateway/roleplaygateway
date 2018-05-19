<?php
/** 
*
* posting [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: posting.php, v1.26 2010/02/24 15:51:00 Elglobo Exp $
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
// in a URL you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

// BBCodes
// Note to translators: you can translate everything but what's between { and }
$lang = array_merge($lang, array(
	'ACP_BBCODES_EXPLAIN'		=> 'Le BBCode est une implémentation spéciale du HTML qui offre un plus grand contrôle sur l’affichage des messages. Depuis cette page, vous pouvez ajouter, supprimer ou éditer des BBCodes personnalisés.',
	'ADD_BBCODE'				=> 'Ajouter un nouveau BBCode',
	
	'BBCODE_DANGER'				=> 'Le BBcode que vous tentez d’ajouter semble utiliser un champs {TEXT} à l’intèrieur d’un attribut HTML. Ceci est une éventuelle faille de sécurité XSS. Essayez d’utiliser à la place des variables plus restrictives comme {SIMPLETEXT} ou {INTEXT}. Procédez uniquement si vous comprenez les risques potentiels et que vous jugez l’utilisation de {TEXT} absolument inévitable.',
	'BBCODE_DANGER_PROCEED'		=> 'Procédez', //'I understand the risk',

	'BBCODE_ADDED'				=> 'BBCode ajouté.',
	'BBCODE_EDITED'				=> 'BBCode édité.',
	'BBCODE_NOT_EXIST'			=> 'Le BBCode que vous avez sélectionné n’existe pas.',
	'BBCODE_HELPLINE'			=> 'Ligne d’aide',
	'BBCODE_HELPLINE_EXPLAIN'	=> 'Ce champ contient le texte du BBCode qui sera affiché lors du passage de la souris.',
	'BBCODE_HELPLINE_TEXT'		=> 'Texte de la ligne d’aide',
	'BBCODE_HELPLINE_TOO_LONG' 	=> 'Le texte saisi pour la ligne d’aide est trop long.',
	'BBCODE_INVALID_TAG_NAME'	=> 'Le nom de la balise BBCode que vous avez sélectionné existe déjà.',
	'BBCODE_INVALID'			=> 'Votre BBCode est construite dans une forme invalide.',
	'BBCODE_OPEN_ENDED_TAG'		=> 'Votre BBCode personnalisé doit contenir une balise d’ouverture et de fermeture.',
	'BBCODE_TAG'				=> 'Balise',
	'BBCODE_TAG_TOO_LONG'		=> 'Le nom de la balise que vous avez sélectionné est trop long.',
	'BBCODE_TAG_DEF_TOO_LONG'	=> 'La définition de la balise que vous avez entrée est trop longue, raccourcissez votre définition.',
	'BBCODE_USAGE'				=> 'Utilisation du BBCode',
	'BBCODE_USAGE_EXAMPLE'		=> '[highlight={COLOR}]{TEXT}[/highlight]<br /><br />[font={SIMPLETEXT1}]{SIMPLETEXT2}[/font]',
	'BBCODE_USAGE_EXPLAIN'		=> 'Vous pouvez définir la façon d’utiliser le BBCode. Remplacez n’importe quelle variable d’entrée par la chaîne de symboles correspondante (%svoir ci-dessous%s).',

	'EXAMPLE'						=> 'Exemple:',
	'EXAMPLES'						=> 'Exemples:',

	'HTML_REPLACEMENT'				=> 'Code HTML de remplacement',
	'HTML_REPLACEMENT_EXAMPLE'		=> '&lt;span style="background-color: {COLOR};"&gt;{TEXT}&lt;/span&gt;<br /><br />&lt;span style="font-family: {SIMPLETEXT1};"&gt;{SIMPLETEXT2}&lt;/span&gt;',
	'HTML_REPLACEMENT_EXPLAIN'		=> 'Vous pouvez définir le code HTML de remplacement de votre BBCode. N’oubliez pas de remettre la chaîne de symboles que vous avez utilisée ci-dessus!',

	'TOKEN'					=> 'Chaîne de symboles',
	'TOKENS'				=> 'Chaînes de symboles',
	'TOKENS_EXPLAIN'		=> 'Les marques sont des conteneurs pour les utilisateurs. Les entrées ne seront validées que si elles trouvent la définition correspondante. Si besoin, vous pouvez les numéroter en y ajoutant un nombre comme dernier caractère entre des accolades, exemple: {TEXT1}, {TEXT2}.<br /><br />Vous pouvez utiliser, en plus du remplacement HTML, une chaîne de langue dans votre répertoire language/ comme ceci: {L_<em>&lt;STRINGNAME&gt;</em>} où <em>&lt;STRINGNAME&gt;</em> est le nom de la chaîne traduite que vous souhaitez ajouter. Par exemple, {L_WROTE} sera affiché en tant que “a écrit” ou sa traduction selon la langue locale de l’utilisateur.<br /><br /><strong>Notez que seules les chaînes listées ci-dessous sont autorisées à être utilisées dans les BBCodes personnalisés.</strong>',
	'TOKEN_DEFINITION'		=> 'Que peut il être?',
	'TOO_MANY_BBCODES'		=> 'Vous ne pouvez pas créer d’autres BBCodes. Supprimez un ou plusieurs BBCodes puis réessayez.',

	'tokens'	=>	array(
		'TEXT'			=> 'Du texte, incluant des caractères étrangers, chiffres, etc. Vous ne devriez pas utiliser cette marque dans les tags HTML. Essayez d’utiliser à la place les marques IDENTIFIER, INTTEXT ou SIMPLETEXT.',
		'SIMPLETEXT'	=> 'Caractères alphabétiques latins (A-Z), chiffres, espaces, virgules, points, -, +, et _',
		'INTTEXT'		=> 'Caractères à lettre unicode, nombres, espaces, virgules, points, -, +, _ et espaces.',
		'IDENTIFIER'	=> 'Caractères alphabétiques latins (A-Z), chiffres, - et _',
		'NUMBER'		=> 'Une série de chiffres',
		'EMAIL'			=> 'Une adresse e-mail valide',
		'URL'			=> 'Une URL valide utilisant un quelconque protocole (http, ftp, etc. ne peuvent pas être utilisés pour des exploits javascripts). Si aucun n’est donné, “http://” sera préfixé par défaut à la chaîne.',
		'LOCAL_URL'		=> 'Une URL locale. L’URL doit être relative par rapport à la page du sujet et ne peut contenir un nom de serveur ou un protocol.',
		'COLOR'			=> 'Une couleur HTML, peut être au choix soit une forme numérique <samp>#FF1234</samp> ou un <a href="http://www.w3.org/TR/CSS21/syndata.html#value-def-color">nom de couleur CSS</a> comme par exemple <samp>fuchsia</samp> ou <samp>InactiveBorder</samp>'
	)
));

// Smilies and topic icons
$lang = array_merge($lang, array(
	'ACP_ICONS_EXPLAIN'		=> 'Depuis cette page, vous pouvez ajouter, supprimer ou éditer les icônes que les utilisateurs pourront ajouter à leur sujet ou message. Ces icônes sont généralement affichées à côté des titres des sujets sur la liste des forums, ou des titres des messages sur la liste des sujets. Vous pouvez également installer et créer de nouveaux packs d’icônes.',
	'ACP_SMILIES_EXPLAIN'	=> 'Les smileys ou émoticônes sont généralement de petites images, parfois des images animées qui sont utilisées pour exprimer une émotion ou un sentiment. Depuis cette page, vous pouvez ajouter, supprimer ou éditer les smileys que les utilisateurs peuvent utiliser dans leurs messages et messages privés. Vous pouvez également installer et créer de nouveaux packs de smileys.',
	'ADD_SMILIES'			=> 'Ajouter de multiples smileys',
	'ADD_SMILEY_CODE'		=> 'Ajouter un code de smiley additionnel',
	'ADD_ICONS'				=> 'Ajouter de multiples icônes',
	'AFTER_ICONS'			=> 'Après %s',
	'AFTER_SMILIES'			=> 'Après %s',

	'CODE'						=> 'Code',
	'CURRENT_ICONS'				=> 'Icônes actuelles',
	'CURRENT_ICONS_EXPLAIN'		=> 'Choisissez que faire avec les icônes actuellement installées.',
	'CURRENT_SMILIES'			=> 'Smileys actuels',
	'CURRENT_SMILIES_EXPLAIN'	=> 'Choisissez que faire avec les smileys actuellement installés.',

	'DISPLAY_ON_POSTING'	=> 'Afficher sur la page de rédaction d’un message',
	'DISPLAY_POSTING'			=> 'Sur la page de rédaction d’un message',
	'DISPLAY_POSTING_NO'		=> 'Absent de la page de rédaction d’un message',
	
	

	'EDIT_ICONS'				=> 'Editer les icônes',
	'EDIT_SMILIES'				=> 'Editer les smileys',
	'EMOTION'					=> 'Emotion',
	'EXPORT_ICONS'				=> 'Exporter et télécharger vers icons.pak',
	'EXPORT_ICONS_EXPLAIN'		=> '%sEn cliquant sur ce lien, la configuration de vos icônes installées sera regroupée dans le pack <samp>icons.pak</samp> qui, une fois téléchargé, peut être utilisé pour créer un fichier <samp>.zip</samp> ou <samp>.tgz</samp> qui contient toutes vos icônes, ainsi que le fichier de configuration <samp>icons.pak</samp>%s.',
	'EXPORT_SMILIES'			=> 'Exporter et télécharger vers smilies.pak',
	'EXPORT_SMILIES_EXPLAIN'	=> '%sEn cliquant sur ce lien, la configuration de vos smileys installés sera regroupée dans le pack <samp>smilies.pak</samp> qui, une fois téléchargé, peut être utilisé pour créer un fichier <samp>.zip</samp> ou <samp>.tgz</samp> qui contient tout vos smileys, ainsi que le fichier de configuration <samp>smilies.pak</samp>%s.',

	'FIRST'			=> 'Premier',

	'ICONS_ADD'				=> 'Ajouter une nouvelle icône',
	'ICONS_NONE_ADDED'		=> 'Aucune icône n’a été ajoutée.',
	'ICONS_ONE_ADDED'		=> 'L’icône a été ajoutée.',
	'ICONS_ADDED'			=> 'Les icônes ont été ajoutées.',
	'ICONS_CONFIG'			=> 'Configuration de l’icône',
	'ICONS_DELETED'			=> 'L’icône a été supprimée.',
	'ICONS_EDIT'			=> 'Editer l’icône',
	'ICONS_ONE_EDITED'		=> 'L’icône a été mise à jour.',
	'ICONS_NONE_EDITED'		=> 'Aucune icône n’a été mise à jour.',
	'ICONS_EDITED'			=> 'Les icônes ont été mises à jour.',
	'ICONS_HEIGHT'			=> 'Hauteur de l’icône',
	'ICONS_IMAGE'			=> 'Image de l’icône',
	'ICONS_IMPORTED'		=> 'Le pack d’icônes a été installé.',
	'ICONS_IMPORT_SUCCESS'	=> 'Le pack d’icônes a été importé.',
	'ICONS_LOCATION'		=> 'Emplacement de l’icône',
	'ICONS_NOT_DISPLAYED'	=> 'Les icônes suivantes ne sont pas affichées sur la page de rédaction',
	'ICONS_ORDER'			=> 'Classement de l’icône',
	'ICONS_URL'				=> 'Image de l’icône',
	'ICONS_WIDTH'			=> 'Largeur de l’icône',
	'IMPORT_ICONS'			=> 'Installer un pack d’icônes',
	'IMPORT_SMILIES'		=> 'Installer un pack de smileys',

	'KEEP_ALL'			=> 'Tout conserver',

	'MASS_ADD_SMILIES'	=> 'Ajouter de multiples smileys',

	'NO_ICONS_ADD'		=> 'Il n’y a aucune icône disponible à ajouter.',
	'NO_ICONS_EDIT'		=> 'Il n’y a aucune icône disponible à modifier.',
	'NO_ICONS_EXPORT'	=> 'Vous n’avez aucune icône pour créer un pack.',
	'NO_ICONS_PAK'		=> 'Aucun pack d’icônes trouvé.',
	'NO_SMILIES_ADD'	=> 'Il n’y a aucun smiley disponible à ajouter.',
	'NO_SMILIES_EDIT'	=> 'Il n’y a aucun smiley disponible à modifier.',
	'NO_SMILIES_EXPORT'	=> 'Vous n’avez aucun smiley pour créer un pack.',
	'NO_SMILIES_PAK'	=> 'Aucun pack de smileys n’a été trouvé.',

	'PAK_FILE_NOT_READABLE'		=> 'Impossible de lire le fichier <samp>.pak</samp>.',

	'REPLACE_MATCHES'	=> 'Remplacer les résultats',

	'SELECT_PACKAGE'			=> 'Sélectionner un pack',
	'SMILIES_ADD'				=> 'Ajouter un nouveau smiley',
	'SMILIES_NONE_ADDED'		=> 'Aucun smiley n’a été ajouté.',
	'SMILIES_ONE_ADDED'			=> 'Le smiley a été ajouté.',
	'SMILIES_ADDED'				=> 'Les smileys ont été ajoutés.',
	'SMILIES_CODE'				=> 'Code du smiley',
	'SMILIES_CONFIG'			=> 'Configuration du smiley',
	'SMILIES_DELETED'			=> 'Le smiley a été supprimé.',
	'SMILIES_EDIT'				=> 'Editer le smiley',
	'SMILIE_NO_CODE'			=> 'Le smiley “%s” a été ignoré car aucun code n’a été saisi.',
	'SMILIE_NO_EMOTION'			=> 'Le smiley “%s” a été ignoré car aucun smiley n’a été indiqué.',
	'SMILIES_NONE_EDITED'		=> 'Aucun smiley n’a été mis à jour.',
	'SMILIES_ONE_EDITED'		=> 'Le smiley a été mis à jour.',
	'SMILIES_EDITED'			=> 'Les smileys ont été mis à jour.',
	'SMILIES_EMOTION'			=> 'Emotion',
	'SMILIES_HEIGHT'			=> 'Hauteur du smiley',
	'SMILIES_IMAGE'				=> 'Image du smiley',
	'SMILIES_IMPORTED'			=> 'Le pack de smileys a été installé.',
	'SMILIES_IMPORT_SUCCESS'	=> 'Le pack de smileys a été importé.',
	'SMILIES_LOCATION'			=> 'Emplacement du smiley',
	'SMILIES_NOT_DISPLAYED'		=> 'Les smileys suivants ne sont pas affichés sur la page de rédaction',
	'SMILIES_ORDER'				=> 'Classement du smiley',
	'SMILIES_URL'				=> 'Image du smiley',
	'SMILIES_WIDTH'				=> 'Largeur du smiley',
	'TOO_MANY_SMILIES'			=> 'Limite de %d smileys atteinte.',

	'WRONG_PAK_TYPE'	=> 'Le pack indiqué ne contient pas les données appropriées.',
));

// Word censors
$lang = array_merge($lang, array(
	'ACP_WORDS_EXPLAIN'		=> 'Depuis ce panneau de contrôle, vous pouvez ajouter, éditer et supprimer les mots qui seront automatiquement censurés sur votre forum. Les gens seront toujours autorisés à s’inscrire avec un nom d’utilisateur contenant ces mots. Les jokers (*) sont acceptés dans le champ, exemple: *test* censurera détestable, test* censurera testament, *test censurera contest.',
	'ADD_WORD'				=> 'Ajouter un nouveau mot',

	'EDIT_WORD'		=> 'Editer la censure',
	'ENTER_WORD'	=> 'Vous devez entrer un mot et son remplaçant.',

	'NO_WORD'	=> 'Aucun mot sélectionné à édité.',

	'REPLACEMENT'	=> 'Remplacement',

	'UPDATE_WORD'	=> 'Mettre à jour la censure',

	'WORD'				=> 'Mot',
	'WORD_ADDED'		=> 'La censure a été ajoutée.',
	'WORD_REMOVED'		=> 'La censure sélectionné a été supprimée.',
	'WORD_UPDATED'		=> 'La censure sélectionné a été mise à jour.',
));

// Ranks
$lang = array_merge($lang, array(
	'ACP_RANKS_EXPLAIN'		=> 'Utilisez ce formulaire pour ajouter, éditer, visionner ou supprimer des rangs. Vous pouvez aussi créer des rangs spéciaux qui pourront être attribués à un utilisateur via la gestion de l’utilisateur.',
	'ADD_RANK'				=> 'Ajouter un nouveau rang',

	'MUST_SELECT_RANK'		=> 'Vous devez sélectionner un rang.',

	'NO_ASSIGNED_RANK'		=> 'Pas de rang spécial assigné.',
	'NO_RANK_TITLE'			=> 'Vous n’avez pas indiqué de titre pour le rang.',
	'NO_UPDATE_RANKS'		=> 'Le rang a été supprimé. Cependant les comptes d’utilisateurs utilisant ce rang n’ont pas été mis à jour. Vous devrez donc réinitialiser manuellement le rang de ces comptes.',

	'RANK_ADDED'			=> 'Le rang a été ajouté.',
	'RANK_IMAGE'			=> 'Image du rang',
	'RANK_IMAGE_EXPLAIN'	=> 'Utilisez ceci pour définir une petite image à associer au rang. Le chemin est relatif par rapport au répertoire racine de phpBB.',
	'RANK_IMAGE_IN_USE'		=> '(En service)',
	'RANK_MINIMUM'			=> 'Messages minimums',
	'RANK_REMOVED'			=> 'Le rang a été supprimé.',
	'RANK_SPECIAL'			=> 'Définir comme rang spécial',
	'RANK_TITLE'			=> 'Titre du rang',
	'RANK_UPDATED'			=> 'Le rang a été mis à jour.',
));

// Disallow Usernames
$lang = array_merge($lang, array(
	'ACP_DISALLOW_EXPLAIN'	=> 'Vous pouvez contrôler les noms d’utilisateurs qui ne sont pas autorisés à être utilisés. Les noms d’utilisateurs interdits sont autorisés à contenir un joker *. Notez que vous ne serez pas autorisé à indiquer tout nom d’utilisateur étant déjà inscrit, vous devez d’abord supprimer ce nom, puis l’interdire.',
	'ADD_DISALLOW_EXPLAIN'	=> 'Vous pouvez utiliser le caractère * pour faire une correspondance avec n’importe quel caractère.',
	'ADD_DISALLOW_TITLE'	=> 'Ajouter un nom interdit',

	'DELETE_DISALLOW_EXPLAIN'	=> 'Vous pouvez retirer un nom de la liste en le sélectionnant puis en cliquant sur envoyer.',
	'DELETE_DISALLOW_TITLE'		=> 'Supprimer un nom interdit',
	'DISALLOWED_ALREADY'		=> 'Le nom entré ne peut être interdit. Il peut soit déjà être interdit, soit être déjà utilisé sur le forum.',
	'DISALLOWED_DELETED'		=> 'Le nom interdit a été supprimé.',
	'DISALLOW_SUCCESSFUL'		=> 'Le nom interdit a été ajouté.',

	'NO_DISALLOWED'				=> 'Aucun nom interdit',
	'NO_USERNAME_SPECIFIED'		=> 'Vous n’avez indiqué aucun nom.',
));

// Reasons
$lang = array_merge($lang, array(
	'ACP_REASONS_EXPLAIN'	=> 'Vous pouvez gérer les raisons utilisées dans les rapports lors de la désapprobation de messages. Il y a une raison par défaut (marquée par une *) que vous ne pouvez pas supprimer, cette raison est utilisée normalement pour les messages personnalisés si aucune raison n’est indiquée.',
	'ADD_NEW_REASON'		=> 'Ajouter une nouvelle raison',
	'AVAILABLE_TITLES'		=> 'Titres des raisons traduits disponibles',

	'IS_NOT_TRANSLATED'			=> 'La raison n’a <strong>pas</strong> été traduite.',
	'IS_NOT_TRANSLATED_EXPLAIN'	=> 'La raison n’a <strong>pas</strong> été traduite. Si vous souhaitez renseigner le formulaire traduit, indiquez la clé correcte des fichiers de langues dans la section des raisons de rapports/refus.',
	'IS_TRANSLATED'				=> 'La raison a été traduite.',
	'IS_TRANSLATED_EXPLAIN'		=> 'La raison a été traduite. Si le titre que vous avez entré est indiqué dans les fichiers de langues dans la section des raisons de rapports/refus, le formulaire traduit du titre et de la description sera utilisé.',

	'NO_REASON'					=> 'La raison est introuvable.',
	'NO_REASON_INFO'			=> 'Vous devez indiquer un titre et une description pour cette raison.',
	'NO_REMOVE_DEFAULT_REASON'	=> 'Vous ne pouvez pas supprimer la raison par défaut “Autres”.',

	'REASON_ADD'				=> 'Ajouter une raison au rapport',
	'REASON_ADDED'				=> 'Une raison a été ajoutée au rapport.',
	'REASON_ALREADY_EXIST'		=> 'Une raison existe déjà avec ce titre, entrez un autre titre pour cette raison.',
	'REASON_DESCRIPTION'		=> 'Description de la raison',
	'REASON_DESC_TRANSLATED'	=> 'Description affichée de la raison',
	'REASON_EDIT'				=> 'Editer la raison du rapport',
	'REASON_EDIT_EXPLAIN'		=> 'Vous pouvez ajouter ou éditer une raison. Si la raison est traduite, la version traduite sera utilisée au lieu de la description entrée ici.',
	'REASON_REMOVED'			=> 'La raison du rapport a été supprimée.',
	'REASON_TITLE'				=> 'Titre de la raison',
	'REASON_TITLE_TRANSLATED'	=> 'Titre affiché de la raison',
	'REASON_UPDATED'			=> 'La raison a été mise à jour.',

	'USED_IN_REPORTS'		=> 'Utilisé dans les rapports',
));

?>