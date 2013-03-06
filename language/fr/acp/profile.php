<?php
/**
*
* acp_profile [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: profile.php, v1.26 2010/02/09 18:54:00 Elglobo Exp $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
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
   'ADDED_PROFILE_FIELD'   	=> 'Le champ de profil personnalisé a été ajouté.',
   'ALPHA_ONLY'   			=> 'Alphanumérique uniquement',
   'ALPHA_SPACERS'   		=> 'Alphanumérique et espaces',
   'ALWAYS_TODAY'   		=> 'Toujours la date actuelle',
   
   'BOOL_ENTRIES_EXPLAIN'   => 'Saisissez vos options',
   'BOOL_TYPE_EXPLAIN'   	=> 'Détermine le type, soit une case à cocher, soit un bouton radio. Les cases à cocher seront affichées uniquement si cela est coché pour un utilisateur donné. Dans ce cas, la <strong>seconde</strong> option de langue sera utilisée. Les boutons radios seront affichés indépendamment de leur valeur.',
	
   'CHANGED_PROFILE_FIELD' 			=> 'Le champ de profil a été modifié.',
   'CHARS_ANY'   					=> 'N’importe quel caractère',
   'CHECKBOX'   					=> 'Case à cocher',
   'COLUMNS'   						=> 'Colonnes',
   'CP_LANG_DEFAULT_VALUE'  		=> 'Valeur par défaut',
   'CP_LANG_EXPLAIN'   				=> 'Description du champ',
   'CP_LANG_EXPLAIN_EXPLAIN'   		=> 'L’explication de ce champ présenté à l’utilisateur.',
   'CP_LANG_NAME'   				=> 'Nom de champ/titre présenté à l’utilisateur',
   'CP_LANG_OPTIONS'   				=> 'Options',
   'CREATE_NEW_FIELD'   			=> 'Créer un nouveau champ',
   'CUSTOM_FIELDS_NOT_TRANSLATED'   => 'Au moins un champ personnalisé de profil n’a pas encore été traduit. Saisissez l’information nécessaire en cliquant sur le lien “Traduire”.',
   
   'DEFAULT_ISO_LANGUAGE'   			=> 'Langue par défaut [%s]',
   'DEFAULT_LANGUAGE_NOT_FILLED'   		=> 'L’entrée de langue pour la langue par défaut n’a pas été renseignée pour ce champs de profil.',
   'DEFAULT_VALUE'   					=> 'Valeur par défaut',
   'DELETE_PROFILE_FIELD'   			=> 'Supprimer le champ de profil',
   'DELETE_PROFILE_FIELD_CONFIRM'   	=> 'Êtes-vous sûr de voir supprimer ce champ de profil?',
   'DISPLAY_AT_PROFILE'   				=> 'Afficher dans le panneau de l’utilisateur',
   'DISPLAY_AT_PROFILE_EXPLAIN'   		=> 'L’utilisateur peut modifier ce champ de profil dans le panneau de l’utilisateur.',
   'DISPLAY_AT_REGISTER'   				=> 'Afficher sur l’écran d’inscription',
   'DISPLAY_AT_REGISTER_EXPLAIN'   		=> 'Si cette option est activée, le champ sera affiché à l’inscription.',
   'DISPLAY_ON_VT'						=> 'Afficher dans les sujets',
   'DISPLAY_ON_VT_EXPLAIN'				=> 'Si cette option est activée, le champ sera affiché dans le mini-profil des sujets.',
   'DISPLAY_PROFILE_FIELD'   			=> 'Afficher publiquement le champ de profil',
   'DISPLAY_PROFILE_FIELD_EXPLAIN'   	=> 'Le champ de profil sera visible dans tous les endroits autorisés dans les paramètres de charge. Réglez cela sur “Non” masquera le champ des pages de sujets, des profils et de la liste des membres.',
   'DROPDOWN_ENTRIES_EXPLAIN'   		=> 'Saisissez vos options, chaque option doit être sur une ligne différente.',
   
   'EDIT_DROPDOWN_LANG_EXPLAIN'   	=> 'Notez que vous pouvez modifier le texte de vos options et ajouter de nouvelles options en fin de liste. Il est déconseillé d’insérer de nouvelles options entre celles existantes - cela pourrait entraîner l’attribution d’options erronées à vos utilisateurs. Ceci peut également se produire si vous supprimez des options parmi d’autres. La suppression d’options à partir de la fin pourrait avoir comme conséquence une mauvaise redirection des utilisateurs sur un article.',
   'EMPTY_FIELD_IDENT'   			=> 'L’identification du champ est vide',
   'EMPTY_USER_FIELD_NAME'   		=> 'Saisissez un nom/titre du champ',
   'ENTRIES'   						=> 'Entrées',
   'EVERYTHING_OK'   				=> 'Tout est correct',
   
   'FIELD_BOOL'   				=> 'Booléen (oui/non)',
   'FIELD_DATE'   				=> 'Date',
   'FIELD_DESCRIPTION'   		=> 'Description du champ',
   'FIELD_DESCRIPTION_EXPLAIN'  => 'L’explication du champ sera présenté à l’utilisateur.',
   'FIELD_DROPDOWN'   			=> 'Liste déroulante',
   'FIELD_IDENT'   				=> 'Identification du champ',
   'FIELD_IDENT_ALREADY_EXIST'  => 'L’identification du champ choisie existe déjà. Entrez un autre nom.',
   'FIELD_IDENT_EXPLAIN'   		=> 'L’identification du champ est un nom qui vous permet d’identifier le champ de profil dans la base de données et les thèmes.',
   'FIELD_INT'   				=> 'Nombres',
   'FIELD_LENGTH'   			=> 'Taille de la zone de saisie',
   'FIELD_NOT_FOUND'   			=> 'Le champ de profil est introuvable.',
   'FIELD_STRING'   			=> 'Champ de texte simple',
   'FIELD_TEXT'   				=> 'Zone de texte',
   'FIELD_TYPE'   				=> 'Type de champ',
   'FIELD_TYPE_EXPLAIN'   		=> 'Vous ne pourrez pas modifier le type de champ plus tard.',
   'FIELD_VALIDATION'   		=> 'Validation du champ',
   'FIRST_OPTION'   			=> 'Première option',
   
   'HIDE_PROFILE_FIELD'   			=> 'Masquer le champ de profil',
   'HIDE_PROFILE_FIELD_EXPLAIN'		=> 'Masque le champ de profil à tous les autres utilisateurs mis à part à l’utilisateur concerné, aux administrateurs et aux modérateurs qui pourront toujours voir ce champ. Si l’option d’affichage dans le panneau de l’utilisateur est désactivée, l’utilisateur ne pourra pas voir ou modifier ce champ, seuls les administrateurs le pourront.',
   
   'INVALID_CHARS_FIELD_IDENT'   	=> 'L’identification du champ ne peut contenir que des minuscules a-z et _',
   'INVALID_FIELD_IDENT_LEN'   		=> 'La longueur de l’identification du champ ne peut dépasser 17 caractères',
   'ISO_LANGUAGE'   				=> 'Langue [%s]',
   
   'LANG_SPECIFIC_OPTIONS'   => 'Options particulières à la langue [<strong>%s</strong>]',
   
   'MAX_FIELD_CHARS'   	=> 'Nombre maximum de caractères',
   'MAX_FIELD_NUMBER' 	=> 'Nombre maximal autorisé',
   'MIN_FIELD_CHARS'   	=> 'Nombre minimum de caractères',
   'MIN_FIELD_NUMBER'   => 'Nombre minimal autorisé',
   
   'NO_FIELD_ENTRIES'   		=> 'Aucune entrée définie',
   'NO_FIELD_ID'   				=> 'Aucun ID de champ indiqué.',
   'NO_FIELD_TYPE'   			=> 'Aucun type de champ indiqué.',
   'NO_VALUE_OPTION'   			=> 'Option égale à la valeur de non-saisie',
   'NO_VALUE_OPTION_EXPLAIN'   	=> 'Valeur de non-saisie. Si le champ est obligatoire, une erreur est affichée lorsque cette valeur est saisie par l’utilisateur.',
   'NUMBERS_ONLY'   			=> 'Uniquement des chiffres (0-9)',
   
   'PROFILE_BASIC_OPTIONS'   		=> 'Options de base',
   'PROFILE_FIELD_ACTIVATED'   		=> 'Le champ de profil a été activé.',
   'PROFILE_FIELD_DEACTIVATED'   	=> 'Le champ de profil a été désactivé.',
   'PROFILE_LANG_OPTIONS'   		=> 'Options particulières de langue',
   'PROFILE_TYPE_OPTIONS'   		=> 'Options particulières du type de profil',
   
   'RADIO_BUTTONS'   			=> 'Boutons radio',
   'REMOVED_PROFILE_FIELD'   	=> 'Le champ de profil a été supprimé.',
   'REQUIRED_FIELD'   			=> 'Champ obligatoire',
   'REQUIRED_FIELD_EXPLAIN'   	=> 'Oblige l’utilisateur ou les administrateurs à remplir ou à préciser le champ. Si l’option d’affichage sur l’écran d’inscription est désactivée, le champ sera seulement requis lorsque l’utilisateur éditera son profil.',
   'ROWS'   					=> 'Lignes',
   
   'SAVE'   						=> 'Sauvegarder',
   'SECOND_OPTION'   				=> 'Deuxième option',
   'STEP_1_EXPLAIN_CREATE'   		=> 'Vous pouvez saisir les premiers paramètres de base du nouveau champ de profil. Ces informations sont requises pour la seconde étape où vous pourrez régler les options restantes et améliorer davantage votre champ de profil.',
   'STEP_1_EXPLAIN_EDIT'   			=> 'Vous pouvez modifier les paramètres de base de votre champ de profil. Les options appropriées sont recalculées dans la seconde étape.',
   'STEP_1_TITLE_CREATE'			=> 'Ajouter un champ de profil',
   'STEP_1_TITLE_EDIT'   			=> 'Editer le champ de profil',
   'STEP_2_EXPLAIN_CREATE'   		=> 'Vous pouvez définir quelques options courantes que vous pouvez vouloir ajuster.',
   'STEP_2_EXPLAIN_EDIT'   			=> 'Vous pouvez modifier quelques options courantes.<br /><strong>Notez que les modifications faites aux champs de profil n’affecteront pas les valeurs déjà saisies par les utilisateurs.</strong>',
   'STEP_2_TITLE_CREATE'   			=> 'Options particulières du type de profil',
   'STEP_2_TITLE_EDIT'   			=> 'Options particulières du type de profil',
   'STEP_3_EXPLAIN_CREATE'   		=> 'Comme vous avez plus d’une langue installée, vous devez aussi remplir les éléments de langue restants. Le champ de profil fonctionnera avec la langue activée par défaut, vous pourrez également remplir ces éléments restants ultèrieurement.',
   'STEP_3_EXPLAIN_EDIT'   			=> 'Comme vous avez plus d’une langue installée, vous pouvez également modifier ou ajouter les éléments de langue restants. Le champ de profil fonctionnera avec la langue activée par défaut.',
   'STEP_3_TITLE_CREATE'   			=> 'Définitions des langues restantes',
   'STEP_3_TITLE_EDIT'   			=> 'Définitions des langues',
   'STRING_DEFAULT_VALUE_EXPLAIN'   => 'Saisissez une phrase, une valeur par défaut à afficher. Laissez cette case vide si vous préférez ne rien afficher en premier.',
   
   'TEXT_DEFAULT_VALUE_EXPLAIN'   	=> 'Saisissez un texte, une valeur par défaut à afficher. Laissez cette case vide si vous préférez ne rien afficher en premier.',
   'TRANSLATE'   					=> 'Traduire',
   
   'USER_FIELD_NAME'   	=> 'Nom/titre du champ affiché à l’utilisateur',
   
   'VISIBILITY_OPTION'   => 'Options de visibilité',
));

?>