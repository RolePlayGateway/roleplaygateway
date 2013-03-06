<?php
/** 
*
* install [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: install.php, v1.26 2010/02/09 19:43:00 Elglobo Exp $
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
	'ADMIN_CONFIG'				=> 'Configuration de l’administration',
	'ADMIN_PASSWORD'			=> 'Mot de passe administrateur',
	'ADMIN_PASSWORD_CONFIRM'	=> 'Confirmez le mot de passe',
	'ADMIN_PASSWORD_EXPLAIN'	=> 'Entrez un mot de passe entre 6 et 30 caractères.',
	'ADMIN_TEST'				=> 'Vérification des paramètres de l’administrateur',
	'ADMIN_USERNAME'			=> 'Nom de l’administrateur',
	'ADMIN_USERNAME_EXPLAIN'	=> 'Entrez un nom d’utilisateur entre 3 et 20 caractères.',
	'APP_MAGICK'				=> 'Support Imagemagick [ Fichiers joints ]',
	'AUTHOR_NOTES'				=> 'Notes de l’auteur<br />» %s',
	'AVAILABLE'					=> 'Disponible',
	'AVAILABLE_CONVERTORS'		=> 'Convertisseurs disponibles',

	'BEGIN_CONVERT'					=> 'Démarrer la conversion',
	'BLANK_PREFIX_FOUND'			=> 'Une vérification de vos tables a détecté une installation possible sans préfixe de table.',
	'BOARD_NOT_INSTALLED'			=> 'Aucune installation détectée',
	'BOARD_NOT_INSTALLED_EXPLAIN'	=> 'Pour permettre à phpBB de fonctionner correctement, vous devez procéder par une installation vierge de phpBB3. Procédez <a href="%s">par une première installation de phpBB3</a>.',

	'CATEGORY'					=> 'Catégorie',
	'CACHE_STORE'				=> 'Type de cache',
	'CACHE_STORE_EXPLAIN'		=> 'Emplacement physique où les données sont mises en cache, un système de fichiers est préférable.',
	'CAT_CONVERT'				=> 'Convertir',
	'CAT_INSTALL'				=> 'Installer',
	'CAT_OVERVIEW'				=> 'Préambule',
	'CAT_UPDATE'				=> 'Mise à jour',
	'CHANGE'					=> 'Modifier',
	'CHECK_TABLE_PREFIX'		=> 'Vérifiez votre préfixe de table et recommencez.',
	'CLEAN_VERIFY'				=> 'Nettoyage et vérification de la structure finale',
	'CLEANING_USERNAMES'		=> 'Nettoyage des noms d’utilisateurs',
	'COLLIDING_CLEAN_USERNAME'	=> '<strong>%s</strong> est le nom d’utilisateur propre pour:',
	'COLLIDING_USERNAMES_FOUND'	=> 'Un même nom d’utilisateur est utilisé par plusieurs personnes différentes. Pour continuer la conversion, effacez ou renommez ces utilisateurs pour qu’il n’y ait plus de nom d’utilisateur en double sur votre ancien forum.',
	'COLLIDING_USER'			=> '» id utilisateur: <strong>%d</strong> nom d’utilisateur: <strong>%s</strong> (%d messages)',
	'CONFIG_CONVERT'			=> 'Configuration de la conversion',
	'CONFIG_FILE_UNABLE_WRITE'	=> 'Impossible d’écrire le fichier de configuration. Des méthodes alternatives pour créer ce fichier sont indiquées ci-dessous.',
	'CONFIG_FILE_WRITTEN'		=> 'Le fichier de configuration a été écrit, vous pouvez désormais procéder à la prochaine étape de l’installation.',
	'CONFIG_PHPBB_EMPTY'		=> 'La variable de configuration de phpBB3 pour “%s” est vide.',
	'CONFIG_RETRY'				=> 'Réessayer',
	'CONTACT_EMAIL_CONFIRM'		=> 'Confirmez l’e-mail de contact',
	'CONTINUE_CONVERT'			=> 'Continuer la conversion',
	'CONTINUE_CONVERT_BODY'		=> 'Une conversion est déjà en cours. Vous pouvez choisir de la continuer ou d’en effectuer une nouvelle.',
	'CONTINUE_LAST'				=> 'Continuer les dernières instructions',
	'CONTINUE_OLD_CONVERSION'	=> 'Continuer la conversion commencée précédemment',
	'CONVERT'					=> 'Convertir',
	'CONVERT_COMPLETE'			=> 'La conversion est terminée',
	'CONVERT_COMPLETE_EXPLAIN'	=> 'Vous avez converti votre forum vers phpBB 3.0. Assurez-vous que les paramètres aient été correctement transférés avant d’activer votre forum en supprimant le répertoire install. Vous pouvez désormais vous connecter et <a href="../">accéder à votre forum</a>. Souvenez-vous que l’aide sur l’utilisation de phpBB est disponible dans le <a href="http://www.phpbb.com/support/documentation/3.0/">Guide de l’utilisateur</a> et le <a href="http://forums.phpbb-fr.com/">forum d’assistance</a>',
	'CONVERT_INTRO'				=> 'Bienvenue sur la “phpBB Unified Convertor Framework”',
	'CONVERT_INTRO_BODY'		=> 'D’ici, vous pouvez importer des données à partir d’autres systèmes de forum. La liste suivante montre tous les modules de conversion actuellement disponibles. Si le module de conversion de votre forum ne s’y trouve pas, visitez notre site Internet pour vérifier si le convertisseur est disponible.',
	'CONVERT_NEW_CONVERSION'	=> 'Nouvelle conversion',
	'CONVERT_NOT_EXIST'			=> 'Le convertisseur indiqué n’existe pas.',
	'CONVERT_OPTIONS'        	=> 'Options',
	'CONVERT_SETTINGS_VERIFIED'	=> 'Les informations saisies ont été vérifiées. Pour commencer la conversion, cliquez sur le bouton ci-dessous.',
	'CONV_ERR_FATAL'			=> 'Erreur fatale lors de la conversion',

	'CONV_ERROR_ATTACH_FTP_DIR'			=> 'Le chargement par FTP des fichiers joints est activé sur votre ancien forum. Copiez tous les fichiers joints dans un répertoire, désactivez le chargement FTP, et vérifiez que le répertoire de chargement indiqué est correct. Vous devrez ensuite redémarrer la conversion.',
	'CONV_ERROR_CONFIG_EMPTY'			=> 'Il n’y a aucune information de configuration disponible pour la conversion.',
	'CONV_ERROR_FORUM_ACCESS'			=> 'Impossible d’obtenir les informations d’accès au forum.',
	'CONV_ERROR_GET_CATEGORIES'			=> 'Impossible d’obtenir les catégories.',
	'CONV_ERROR_GET_CONFIG'				=> 'Impossible de récupérer la configuration de votre forum.',
	'CONV_ERROR_COULD_NOT_READ'			=> 'Impossible d’accéder/lire “%s”.',
	'CONV_ERROR_GROUP_ACCESS'			=> 'Impossible d’obtenir les informations d’authentification des groupes.',
	'CONV_ERROR_INCONSISTENT_GROUPS'	=> 'Contradiction détectée dans la table des groupes dans add_bots() - vous devez ajouter tous les groupes spéciaux manuellement.',
	'CONV_ERROR_INSERT_BOT'				=> 'Impossible d’insérer le robot dans la table des utilisateurs.',
	'CONV_ERROR_INSERT_BOTGROUP'		=> 'Impossible d’insérer le robot dans la table des robots.',
	'CONV_ERROR_INSERT_USER_GROUP'		=> 'Impossible d’insérer l’utilisateur dans la table user_group.',
	'CONV_ERROR_MESSAGE_PARSER'			=> 'Erreur lors de l’analyse du message',
	'CONV_ERROR_NO_AVATAR_PATH'			=> 'Note au développeur: vous devez indiquer $convertor[\'avatar_path\'] pour utiliser %s.',
	'CONV_ERROR_NO_FORUM_PATH'			=> 'Le chemin relatif au forum source n’a pas été indiqué.',
	'CONV_ERROR_NO_GALLERY_PATH'		=> 'Note au développeur: vous devez indiquer $convertor[\'avatar_gallery_path\'] pour utiliser %s.',
	'CONV_ERROR_NO_GROUP'				=> 'Le groupe “%1$s” est introuvable dans %2$s.',
	'CONV_ERROR_NO_RANKS_PATH'			=> 'Note au développeur: vous devez indiquer $convertor[\'ranks_path\'] pour utiliser %s.',
	'CONV_ERROR_NO_SMILIES_PATH'		=> 'Note au développeur: vous devez indiquer $convertor[\'smilies_path\'] pour utiliser %s.',
	'CONV_ERROR_NO_UPLOAD_DIR'			=> 'Note au développeur: vous devez indiquer $convertor[\'upload_path\'] pour utiliser %s.',
	'CONV_ERROR_PERM_SETTING'			=> 'Impossible d’insérer/mettre à jour les paramètres de permissions.',
	'CONV_ERROR_PM_COUNT'				=> 'Impossible de sélectionner le compteur de dossiers de messagerie privée.',
	'CONV_ERROR_REPLACE_CATEGORY'		=> 'Impossible d’insérer le nouveau forum en remplacement de l’ancienne catégorie.',
	'CONV_ERROR_REPLACE_FORUM'			=> 'Impossible d’insérer le nouveau forum en remplacement de l’ancien forum.',
	'CONV_ERROR_USER_ACCESS'			=> 'Impossible d’obtenir les informations d’authentification de l’utilisateur.',
	'CONV_ERROR_WRONG_GROUP'			=> 'Mauvais groupe “%1$s” défini dans %2$s.',
	'CONV_OPTIONS_BODY'					=> 'Cette page collecte les informations qui sont requises pour accéder à votre forum source. Entrez les informations de la base de données de votre ancien forum; Le convertisseur ne modifiera en rien la base de données ci-dessous. Le forum source devrait être désactivé pour permettre une conversion sans risque.',
	'CONV_SAVED_MESSAGES'				=> 'Messages sauvegardés',

	'COULD_NOT_COPY'			=> 'Impossible de copier le fichier <strong>%1$s</strong> vers <strong>%2$s</strong><br /><br />Vérifiez que le répertoire cible existe et qu’il est autorisé en écriture.',
	'COULD_NOT_FIND_PATH'		=> 'Impossible de trouver le chemin vers votre ancien forum. Vérifiez vos paramètres et recommencez.<br />» Le chemin indiqué était %s.',

	'DBMS'						=> 'Type de base',
	'DB_CONFIG'					=> 'Configuration de la base de données',
	'DB_CONNECTION'				=> 'Connexion à la base de données',
	'DB_ERR_INSERT'				=> 'Erreur pendant l’exécution d’une requête <code>INSERT</code>.',
	'DB_ERR_LAST'				=> 'Erreur pendant l’exécution de <var>query_last</var>.',
	'DB_ERR_QUERY_FIRST'		=> 'Erreur pendant l’exécution de <var>query_first</var>.',
	'DB_ERR_QUERY_FIRST_TABLE'	=> 'Erreur pendant l’exécution de <var>query_first</var>, %s (“%s”).',
	'DB_ERR_SELECT'				=> 'Erreur pendant l’exécution d’une requête <code>SELECT</code>.',
	'DB_HOST'					=> 'Serveur de base de données ou DSN',
	'DB_HOST_EXPLAIN'			=> 'DSN signifie Data Source Name (source de données) et n’est utilisé que pour une installation ODBC.',
	'DB_NAME'					=> 'Nom de la base',
	'DB_PASSWORD'				=> 'Mot de passe',
	'DB_PORT'					=> 'Port du serveur',
	'DB_PORT_EXPLAIN'			=> 'Laissez cette case vide à moins que le serveur n’utilise un port non standard.',
	'DB_UPDATE_NOT_SUPPORTED'	=> 'Nous sommes désolés, mais ce script ne supporte pas la mise à jour des versions de phpBB avant “%1$s”. La version que vous avez actuellement installé est “%2$s”. Mettez à jour à une précédente version avant de lancer ce script. Une assistance à ce sujet est disponible dans le forums de support sur phpBB.com ou phpBB-fr.com.',
	'DB_USERNAME'				=> 'Nom d’utilisateur',
	'DB_TEST'					=> 'Test de connexion',
	'DEFAULT_LANG'				=> 'Langue par défaut',
	'DEFAULT_PREFIX_IS'			=> 'Le convertisseur n’a pas trouvé de tables avec le préfixe indiqué. Vérifiez que ce préfixe est celui du forum que vous désirez convertir. Le préfixe par défaut pour %1$s est <strong>%2$s</strong>.',
	'DEV_NO_TEST_FILE'			=> 'Aucune valeur n’a été indiquée pour la variable test_file dans le convertisseur. Si vous utilisez ce convertisseur, vous ne devriez pas voir cette erreur, rapportez cette erreur à l’auteur du convertisseur. Si vous êtes l’auteur du convertisseur, vous devez indiquer le nom du fichier qui existe dans le forum source pour permettre au chemin d’être vérifié.',
	'DIRECTORIES_AND_FILES'		=> 'Installation du répertoire et du fichier',
	'DISABLE_KEYS'				=> 'Désactivation des clés',
	'DLL_FIREBIRD'				=> 'Firebird',
	'DLL_FTP'					=> 'Support FTP distant [ Installation ]',
	'DLL_GD'					=> 'Support de la librairie graphique GD [ Confirmation visuelle ]',
	'DLL_MBSTRING'				=> 'Support des caractères multi-octets',
	'DLL_MSSQL'					=> 'MSSQL Server 2000+',
	'DLL_MSSQL_ODBC'			=> 'MSSQL Server 2000+ via ODBC',
	'DLL_MYSQL'					=> 'MySQL',
	'DLL_MYSQLI'				=> 'MySQL avec l’extension MySQLi',
	'DLL_ORACLE'				=> 'Oracle',
	'DLL_POSTGRES'				=> 'PostgreSQL 7.x/8.x',
	'DLL_SQLITE'				=> 'SQLite',
	'DLL_XML'					=> 'Support du XML [ Jabber ]',
	'DLL_ZLIB'					=> 'Support de la compression zlib [ gz, .tar.gz, .zip ]',
	'DL_CONFIG'					=> 'Télécharger la configuration',
	'DL_CONFIG_EXPLAIN'			=> 'Vous pouvez télécharger le fichier config.php sur votre ordinateur. Vous devrez ensuite charger ce fichier manuellement sur votre FTP, en écrasant le fichier config.php existant à la racine du répertoire de phpBB 3.0. Veillez à charger le fichier au format ASCII (consultez la documentation de votre logiciel FTP si vous ne savez pas faire cela). Lorsque vous aurez chargé le fichier config.php, cliquez sur “Terminé” pour passer à l’étape suivante.',
	'DL_DOWNLOAD'				=> 'Télécharger',
	'DONE'						=> 'Terminé',

	'ENABLE_KEYS'				=> 'Réactivation des clés. Cela peut prendre un moment.',

	'FILES_OPTIONAL'			=> 'Fichiers et répertoires optionnels',
	'FILES_OPTIONAL_EXPLAIN'	=> '<strong>Optionnel</strong> - Ces fichiers, répertoires ou permissions ne sont pas obligatoires. L’installation utilisera diverses techniques pour les créer s’ils n’existent pas ou ne sont pas permis en écriture. Toutefois, la présence de ces fichiers, ou permissions accéléreront l’installation.',
	'FILES_REQUIRED'			=> 'Répertoires et fichiers',
	'FILES_REQUIRED_EXPLAIN'	=> '<strong>Requis</strong> - Pour fonctionner correctement, phpBB doit pouvoir accéder ou écrire sur certains fichiers ou répertoires. Si vous voyez “Introuvable”, vous devez créer les fichiers ou répertoires adéquats. Si vous voyez “Non autorisé en écriture”, vous devez modifier les permissions sur le fichier ou répertoire pour autoriser phpBB à y écrire.',
	'FILLING_TABLE'				=> 'Remplissage de la table <strong>%s</strong>',
	'FILLING_TABLES'			=> 'Remplissage des tables',
	'FIREBIRD_DBMS_UPDATE_REQUIRED'		=> 'phpBB ne supporte plus Firebird/Interbase avant la version 2.1. Mettez à jour votre installation de Firebird au moins à la version 2.1.0 avant de procéder à cette mise à jour.',
	'FINAL_STEP'				=> 'Etape finale du processus',
	'FORUM_ADDRESS'				=> 'Adresse du forum',
	'FORUM_ADDRESS_EXPLAIN'		=> 'Ceci est l’URL de votre ancien forum, par exemple <samp>http://www.exemple.com/phpBB2/</samp>. Si une adresse est entrée ici et non vide à gauche, chaque exemple de cette adresse sera remplacé par vos nouvelles adresses dans les messages, messages privés et signatures.',
	'FORUM_PATH'				=> 'Chemin du forum',
	'FORUM_PATH_EXPLAIN'		=> 'Ceci est le chemin <strong>relatif</strong> vers votre ancien forum depuis <strong>la racine de cette installation phpBB3</strong>',
	'FOUND'						=> 'Trouvé',
	'FTP_CONFIG'				=> 'Transférer le fichier de configuration par FTP',
	'FTP_CONFIG_EXPLAIN'		=> 'phpBB a détecté la présence d’un module FTP sur ce serveur. Vous pouvez essayer de transférer le fichier config.php par ce moyen si vous le désirez. Vous devrez fournir les informations demandées ci-dessous. Les noms et mots de passe sont ceux du serveur FTP! (Contactez votre fournisseur d’hébergement si vous ne les connaissez pas)',
	'FTP_PATH'					=> 'Chemin FTP',
	'FTP_PATH_EXPLAIN'			=> 'Chemin relatif vers votre forum phpBB depuis la racine FTP, par exemple: htdocs/phpBB3/',
	'FTP_UPLOAD'				=> 'Charger',

	'GPL'						=> 'Licence publique générale',

	'INITIAL_CONFIG'			=> 'Configuration de base',
	'INITIAL_CONFIG_EXPLAIN'	=> 'L’installation a déterminé que votre serveur est à même de supporter phpBB, vous devez fournir quelques informations particulières. Si vous ne savez pas vous connecter à votre base de données, contactez votre fournisseur d’hébergement (en premier lieu) ou utilisez <a href="http://forums.phpbb-fr.com/"> le forum d’assistance</a>.Lors de la saisie des données, assurez-vous de bien les vérifier, avant de continuer.',
	'INSTALL_CONGRATS'			=> 'Félicitations!',
	'INSTALL_CONGRATS_EXPLAIN'   => '
      <p>Vous avez installé votre forum phpBB %1$s. Choisissez maintenant une des options suivantes:</p>
      <h2>Convertir un forum existant vers phpBB3</h2>
      <p>Le processus de conversion de phpBB3 permet de convertir un forum phpBB2 et d’autres systèmes de forum en un forum phpBB3. Si vous avez déjà un forum existant que vous souhaitez convertir, <a href="%2$s">convertissez-le</a>.</p>
      <h2>Utiliser votre forum maintenant!</h2>
      <p>En cliquant sur le bouton ci-dessous vous serez redirigé vers un formulaire pour soumettre votre rapport de statistiques à phpBB dans votre panneau d’administration (ACP). Nous apprécierions si vous pouviez nous aider en envoyant cette information. Prenez le temps d’examiner les différentes options disponibles. Souvenez-vous qu’une aide en ligne est accessible via <a href="http://www.phpbb.com/support/documentation/3.0/">la documentation </a> et le <a href="http://forums.phpbb-fr.com/">forum de support</a>, consultez le fichier <a href="%3$s">Lisez-moi</a> pour plus d’informations.</p><p><strong>Effacez, renommez ou déplacez le répertoire install de votre FTP avant d’utiliser votre forum. Tant que ce répertoire est présent, seul le panneau d’administration (ACP) sera disponible.</strong></p>',
	'INSTALL_INTRO'				=> 'Bienvenue dans l’installation',

	'INSTALL_INTRO_BODY'		=> 'Avec cette option, il est possible d’installer phpBB3 sur votre serveur.</p><p>Pour cela, vous aurez besoin des paramètres de connexion à votre base de données. Si vous ne les connaissez pas, contactez votre fournisseur d’hébergement pour les lui demander. Vous ne pourrez pas continuer l’installation sans les paramètres suivants:</p>

   <ul>
      <li>Le type de votre base de données.</li>
      <li>L’adresse du serveur de votre base de données ou DSN.</li>
      <li>Le port du serveur de votre base de données.</li>
      <li>Le nom de votre base de données.</li>
      <li>Le login et le mot de passe d’accès à votre base de données.</li>
   </ul>

   <p><strong>Note:</strong> Si vous faites une installation en utilisant SQLite, vous devrez entrer le chemin complet d’accès à votre base de données dans le champ DSN, et laisser les champs nom d’utilisateur et mot de passe vides. Pour des raisons de sécurité, assurez-vous que votre fichier de base de données n’est pas situé dans un répertoire accessible depuis le Web.</p>

   <p>phpBB3 supporte les bases de données suivantes:</p>
   <ul>
      <li>MySQL 3.23 ou supérieur (MySQLi supporté)</li>
      <li>PostgreSQL 7.3+</li>
      <li>SQLite 2.8.2+</li>
      <li>Firebird 2.1+</li>
      <li>MS SQL Server 2000 ou supérieur (directement ou via ODBC)</li>
      <li>Oracle</li>
   </ul>

   <p>Seules ces bases de données, si elles sont supportées par votre serveur, seront affichées.',
	'INSTALL_INTRO_NEXT'		=> 'Pour commencer l’installation, appuyez sur le bouton ci-dessous.',
	'INSTALL_LOGIN'				=> 'Se connecter',
	'INSTALL_NEXT'				=> 'Etape suivante',
	'INSTALL_NEXT_FAIL'			=> 'Certains tests ont échoué et vous devez corriger ces problèmes avant de passer à l’étape suivante. Sans cela l’installation risque d’être incomplète.',
	'INSTALL_NEXT_PASS'			=> 'Tous les tests de base ont réussi, vous pouvez donc passer à l’étape suivante de l’installation. Si vous avez modifié certains modules, permissions, etc., vous pouvez les vérifier à nouveau.',
	'INSTALL_PANEL'				=> 'Panneau d’installation',
	'INSTALL_SEND_CONFIG'		=> 'Malheureusement PhpBB n’a pas réussi à écrire les informations de configuration directement dans le fichier config.php. Le fichier n’existe peut-être pas ou n’est pas autorisé en écriture. Voici quelques options pour vous permettre d’installer le fichier.',
	'INSTALL_START'				=> 'Commencer l’installation',
	'INSTALL_TEST'				=> 'Tester à nouveau',
	'INST_ERR'					=> 'Erreur d’installation',
	'INST_ERR_DB_CONNECT'		=> 'Impossible de se connecter à la base de données, consultez le message d’erreur ci-dessous.',
	'INST_ERR_DB_FORUM_PATH'	=> 'Le fichier de la base de données indiqué est dans le répertoire racine de votre forum. Vous devez déplacer ce fichier dans un emplacement inaccessible depuis Internet.',
	'INST_ERR_DB_NO_ERROR'		=> 'Aucune erreur n’est survenue.',
	'INST_ERR_DB_NO_MYSQLI'		=> 'La version de MySQL installée sur cette machine est incompatible avec l’option “MySQL avec extension MySQLi”. Essayez avec l’option “MySQL” à la place.',
	'INST_ERR_DB_NO_SQLITE'		=> 'La version de SQLite installée est trop ancienne, elle doit être mise à jour au minimum à la version 2.8.2.',
	'INST_ERR_DB_NO_ORACLE'		=> 'La version d’Oracle installée nécessite de définir le paramètre <var>NLS_CHARACTERSET</var> sur <var>UTF8</var>. Mettez-la à jour à la version 9.2+ ou changez ce paramètre.',
	'INST_ERR_DB_NO_FIREBIRD'	=> 'La version installée de Firebird est plus ancienne que la version 2.1, mettez-la à jour vers une version plus récente.',
	'INST_ERR_DB_NO_FIREBIRD_PS'=> 'La base de données sélectionnée pour firebird a une taille inférieure à 8192, la taille minimale doit être de 8192.',
	'INST_ERR_DB_NO_POSTGRES'	=> 'La base de données sélectionnée n’a pas été créée avec l’encodage <var>UNICODE</var> ou <var>UTF8</var>. Réessayez l’installation avec une base encodée en <var>UNICODE</var> ou bien <var>UTF8</var>',
	'INST_ERR_DB_NO_NAME'		=> 'Aucun nom de base indiqué.',
	'INST_ERR_EMAIL_INVALID'	=> 'L’adresse e-mail saisie est invalide',
	'INST_ERR_EMAIL_MISMATCH'	=> 'Les e-mails saisis ne correspondent pas.',
	'INST_ERR_FATAL'			=> 'Erreur fatale lors de l’installation',
	'INST_ERR_FATAL_DB'			=> 'Une erreur fatale et irrécupérable de base de données est survenue. Vous n’avez peut-être pas les droits appropriés pour effectuer <code>CREATE TABLES</code> ou <code>INSERT</code>, etc. Des informations supplémentaires peuvent être données ci-dessous. Contactez votre hébergeur en premier lieu ou le <a href="http://forums.phpbb-fr.com/">forum d’assistance</a>',
	'INST_ERR_FTP_PATH'			=> 'Impossible d’accéder au répertoire donné, vérifiez le chemin.',
	'INST_ERR_FTP_LOGIN'		=> 'Impossible de s’identifier sur le serveur FTP, vérifiez vos identifiant et mot de passe',
	'INST_ERR_MISSING_DATA'		=> 'Vous devez remplir tous les champs de ce bloc',
	'INST_ERR_NO_DB'			=> 'Impossible de charger le module PHP pour le type de base sélectionné',
	'INST_ERR_PASSWORD_MISMATCH'	=> 'Les mots de passe saisis ne correspondent pas.',
	'INST_ERR_PASSWORD_TOO_LONG'	=> 'Le mot de passe saisi est trop long. La taille maximale est de 30 caractères.',
	'INST_ERR_PASSWORD_TOO_SHORT'	=> 'Le mot de passe saisi est trop court. La taille minimale est de 6 caractères.',
	'INST_ERR_PREFIX'			=> 'Des tables avec le préfixe indiqué existent déjà, choisissez-en un autre.',
	'INST_ERR_PREFIX_INVALID'	=> 'Le préfixe de table indiqué est incompatible avec votre base de données. Essayez-en un autre, en supprimant les caractères comme les tirets',
	'INST_ERR_PREFIX_TOO_LONG'	=> 'Le préfixe de table indiqué est trop long. La taille maximale est de %d caractères.',
	'INST_ERR_USER_TOO_LONG'	=> 'Le nom d’utilisateur saisi est trop long. La taille maximale est de 20 caractères.',
	'INST_ERR_USER_TOO_SHORT'	=> 'le nom d’utilisateur saisi est trop court. La taille minimale est de 3 caractères.',
	'INVALID_PRIMARY_KEY'		=> 'Clé primaire invalide: %s',
	
	'LONG_SCRIPT_EXECUTION'		=> 'Notez que cela peut prendre un certain temps... N’arrêtez pas le script.',

	// mbstring
	'MBSTRING_CHECK'						=> 'Vérification de l’extension <samp>mbstring</samp>',
	'MBSTRING_CHECK_EXPLAIN'				=> '<strong>Requis</strong> - <samp>mbstring</samp> est une extension PHP qui propose des fonctions de chaînes à caractères multi-octets. Certaines fonctionnalités de mbstring ne sont pas compatibles avec phpBB et doivent être désactivées.',
	'MBSTRING_FUNC_OVERLOAD'				=> 'Fonction de surcharge',
	'MBSTRING_FUNC_OVERLOAD_EXPLAIN'		=> '<var>mbstring.func_overload</var> doit être configuré sur 0 ou 4',
	'MBSTRING_ENCODING_TRANSLATION'			=> 'Caractères d’encodage transparent',
	'MBSTRING_ENCODING_TRANSLATION_EXPLAIN'	=> '<var>mbstring.encoding_translation</var> doit être configuré sur 0',
	'MBSTRING_HTTP_INPUT'					=> 'Conversion des caractères d’entrée HTTP',
	'MBSTRING_HTTP_INPUT_EXPLAIN' 			=> '<var>mbstring.http_input</var> doit être configuré sur <samp>pass</samp>',
	'MBSTRING_HTTP_OUTPUT' 					=> 'Conversion des caractères de sortie HTTP',
	'MBSTRING_HTTP_OUTPUT_EXPLAIN'   		=> '<var>mbstring.http_output</var> doit être configuré sur <samp>pass</samp>',

	'MAKE_FOLDER_WRITABLE'   	=> 'Vérifiez que ce dossier existe et qu’il est autorisé en écriture par le serveur Web et recommencez:<br />»<strong>%s</strong>',
	'MAKE_FOLDERS_WRITABLE'   	=> 'Vérifiez que ces dossiers existent et sont accessibles en écriture par le serveur Web et recommencez:<br />»<strong>%s</strong>',

	'MYSQL_SCHEMA_UPDATE_REQUIRED'	=> 'Le schéma de votre base de données MySQL n’est pas à jour. phpBB a détecté un schéma pour MySQL 3.x/4.x, mais le serveur fonctionne avec MySQL %2$s.<br /><strong>Avant de procéder à la mise à jour, vous devez mettre à jour le schéma.</strong><br /><br />Consultez l’<a href="http://www.phpbb.com/kb/article/doesnt-have-a-default-value-errors/">article de la base de connaissances à propos de la mise à jour du schéma MySQL</a>. Utilisez <a href="http://www.phpbb.com/community/viewforum.php?f=46">les forums de support officiels</a> ou <a href="http://forums.phpbb-fr.com/support-phpbb3/">nos forums de support</a> si vous rencontrez des difficultés.',
		
	'NAMING_CONFLICT'   		=> 'Conflit de noms: %s et %s sont tous deux des alias<br /><br />%s',
	'NEXT_STEP' 				=> 'Etape suivante',
	'NOT_FOUND'   				=> 'Introuvable',
	'NOT_UNDERSTAND'   			=> 'Impossible d’interpréter %s #%d, table %s (“%s”)',
	'NO_CONVERTORS'  			=> 'Aucun convertisseur disponible',
	'NO_CONVERT_SPECIFIED'   	=> 'Aucun convertisseur indiqué',
	'NO_LOCATION'   			=> 'Impossible de déterminer l’emplacement. Si vous savez que Imagemagick est installé, vous pourrez indiquer son emplacement plus tard dans le panneau d’administration',
	'NO_TABLES_FOUND'   		=> 'Aucune table trouvée.',

	'OVERVIEW_BODY'				=> 'Bienvenue sur phpBB3!<br /><br />phpBB™ est la solution de forum libre la plus répandue dans le monde. phpBB3 est l’aboutissement d’un long processus débuté en 2000. Comme ces prédécesseurs, phpBB3 est riche en fonctionnalités, convivial, et complètement supporté par l’équipe phpBB. phpBB3 s’améliore considérablement sur ce qui a rendu phpBB2 populaire, et ajoute des fonctionnalités généralement demandées, qui étaient non présentes dans les versions précédentes. Nous espérons qu’il dépassera vos attentes.<br /><br />Cet outil vous guidera à travers l’installation de phpBB3, la mise à jour de votre forum phpBB3, ou la conversion depuis un autre système de forum (y compris phpBB2). Pour plus d’informations, nous vous invitons à prendre connaissance de <a href="../docs/INSTALL.html">notre guide d’installation</a>.<br /><br />Pour consulter la licence de phpBB3, ou vous renseigner sur l’obtention de support ainsi que notre position, choisissez l’option respective à partir du menu latéral. Pour continuer, choisissez l’option appropriée dans les onglets ci-dessus.',

	'PCRE_UTF_SUPPORT'				=> 'Support de PCRE UTF-8',
	'PCRE_UTF_SUPPORT_EXPLAIN'		=> 'phpBB <strong>ne fonctionnera que</strong> si votre installation PHP est compilée avec le support de l’extension PCRE UTF-8',
	'PHP_GETIMAGESIZE_SUPPORT'			=> 'La fonction PHP getimagesize() est disponible',
	'PHP_GETIMAGESIZE_SUPPORT_EXPLAIN'	=> '<strong>Requis</strong> - Pour que phpBB fonctionne correctement, la fonction getimagesize() doit être disponible.',
	'PHP_OPTIONAL_MODULE'  				=> 'Modules optionnels',
	'PHP_OPTIONAL_MODULE_EXPLAIN'   	=> '<strong>Optionnel</strong> - Ces modules ou applications sont optionnels, vous n’en avez pas besoin pour utiliser phpBB 3.0. Toutefois si vous les avez, ils activeront des fonctionnalités supplémentaires.',
	'PHP_SUPPORTED_DB'   				=> 'Bases de données supportées',
	'PHP_SUPPORTED_DB_EXPLAIN'   		=> '<strong>Requis</strong> - Vous devez avoir au moins une base de données comportant PHP. Si PHP n’est pas disponible, contactez votre hébergeur ou consultez la documentation d’installation de PHP.',
	'PHP_REGISTER_GLOBALS'   			=> 'Le paramètre PHP <var>register_globals</var> est désactivé',
	'PHP_REGISTER_GLOBALS_EXPLAIN'   	=> 'phpBB fonctionne si ce paramètre est activé, mais si possible, il est recommandé de désactiver register_globals pour des raisons de sécurité.',
	'PHP_SAFE_MODE'   					=> 'Mode sécurisé',
	'PHP_SETTINGS'   					=> 'Version de PHP et paramètres',
	'PHP_SETTINGS_EXPLAIN'   			=> '<strong>Requis</strong> - Vous devez utiliser au moins la version 4.3.3 de PHP pour installer phpBB. Si <var>safe mode</var> est affiché, PHP fonctionne dans ce mode. Cela implique des limitations sur l’administration distante et des fonctionnalités similaires.',
	'PHP_URL_FOPEN_SUPPORT'   			=> 'Le paramètre PHP <var>allow_url_fopen</var> est activé',
	'PHP_URL_FOPEN_SUPPORT_EXPLAIN'  	=> '<strong>Optionnel</strong> - Ce paramètre est optionnel, il permet entre autres d’attacher des avatars depuis un site externe.',
	'PHP_VERSION_REQD'   				=> 'Votre version de PHP doit être la 4.3.3 au minimum',
	'POST_ID'  							=> 'Id du message',
	'PREFIX_FOUND'   					=> 'Une vérification de vos tables a trouvé une installation de phpBB avec le préfixe <strong>%s</strong>.',
	'PREPROCESS_STEP'   				=> 'Exécution des fonctions/requêtes de pré-traitement',
	'PRE_CONVERT_COMPLETE'   			=> 'Toutes les étapes de pré-conversion sont terminées. Vous pouvez commencer le processus de conversion. Notez que vous pouvez avoir à faire et ajuster plusieurs choses manuellement. Après la conversion, vérifiez particulièrement les permissions assignées, reconstruisez votre index de recherche si nécessaire, et assurez-vous que les fichiers ont été correctement copiés, par exemple, les avatars et les smileys.',
	'PROCESS_LAST'   					=> 'Exécution des dernières instructions',

	'REFRESH_PAGE'				=> 'Rafraîchir la page pour continuer la conversion',
	'REFRESH_PAGE_EXPLAIN'		=> 'Si OUI, le convertisseur va rafraîchir la page après chaque étape. S’il s’agit de votre première conversion pour effectuer des tests et voir les erreurs durant l’avancement, nous vous conseillons de laisser NON.',

	'REQUIREMENTS_TITLE'		=> 'Compatibilité de l’installation',
	'REQUIREMENTS_EXPLAIN'		=> 'Avant d’effectuer une installation complète, phpBB va vérifier la configuration des fichiers de votre serveur et s’assurer que vous pouvez installer phpBB. Lisez attentivement les résultats et ne continuez pas tant que tous les tests ne sont pas validés. Si vous voulez activer une fonctionnalité liée à des tests optionnels, vous devez vous assurer que ces tests soient aussi validés.',
	'RETRY_WRITE'				=> 'Réessayer l’écriture du fichier de configuration',
	'RETRY_WRITE_EXPLAIN'		=> 'Si vous voulez, vous pouvez modifier les droits sur config.php pour permettre à phpBB d’écrire dessus. Dans ce cas, cliquez sur réessayer pour recommencer. Rappelez-vous de réinitialiser les permissions de config.php après l’installation de phpBB.',

	'SCRIPT_PATH'				=> 'Chemin du script',
	'SCRIPT_PATH_EXPLAIN'   	=> 'Le chemin où phpBB est situé par rapport au répertoire racine du forum, par exemple: /<samp>phpbb3</samp>',
	'SELECT_LANG'   			=> 'Choisissez une langue',
	'SERVER_CONFIG'   			=> 'Configuration du serveur',
	'SEARCH_INDEX_UNCONVERTED'  => 'L’index de recherche n’a pas été converti',
	'SEARCH_INDEX_UNCONVERTED_EXPLAIN'   => 'Votre ancien index de recherche n’a pas été converti. La recherche ne fonctionnera plus jusqu’à ce que vous réindexiez votre recherche via le panneau d’administration, sélectionnez Maintenance puis Index de recherche dans le sous-menu.',
	'SOFTWARE'   				=> 'Logiciel de forum',
	'SPECIFY_OPTIONS'   		=> 'Indiquer les options de conversion',
	'STAGE_ADMINISTRATOR'   	=> 'Informations sur l’administrateur',
	'STAGE_ADVANCED'   			=> 'Paramètres avancés',
	'STAGE_ADVANCED_EXPLAIN'   	=> 'Les paramètres de cette page ne sont nécessaires que pour définir des paramètres différents de ceux par défaut. En cas de doute, allez à la page suivante, ils pourront être modifiés plus tard via l’administration.',
	'STAGE_CONFIG_FILE'   		=> 'Fichier de configuration',
	'STAGE_CREATE_TABLE'   		=> 'Création des tables de base de données',
	'STAGE_CREATE_TABLE_EXPLAIN'=> 'Les tables de base de données utilisées par phpBB 3.0 ont été créées et remplies avec quelques données initiales. Rendez-vous sur la page suivante pour terminer l’installation de phpBB.',
	'STAGE_DATABASE'   			=> 'Paramètres de la base de données',
	'STAGE_FINAL'   			=> 'Etape finale',
	'STAGE_INTRO'   			=> 'Introduction',
	'STAGE_IN_PROGRESS'   		=> 'Conversion en cours',
	'STAGE_REQUIREMENTS'   		=> 'Conditions',
	'STAGE_SETTINGS'   			=> 'Paramètres',
	'STARTING_CONVERT'   		=> 'Démarrage du processus de conversion',
	'STEP_PERCENT_COMPLETED'   	=> 'Etape <strong>%d</strong> sur <strong>%d</strong>',
	'SUB_INTRO'   				=> 'Introduction',
	'SUB_LICENSE'   			=> 'Licence',
	'SUB_SUPPORT'   			=> 'Support',
	'SUCCESSFUL_CONNECT'   		=> 'Connexion réussie',

	'SUPPORT_BODY'            	=> 'Un support complet et gratuit est fourni pour l’actuelle version stable de phpBB3. Ceci inclut les questions concernant:</p><ul><li>l’installation</li><li>la configuration</li><li>les questions techniques</li><li>les problèmes liés aux potentiels bugs du logiciel</li><li>la mise à jour depuis une version Release Candidate (RC) à la dernière version stable</li><li>la conversion depuis un forum phpBB2.0.x vers phpBB3</li><li>la conversion depuis un autre système de forum vers phpBB3 (consulter <a href="http://www.phpbb.com/community/viewforum.php?f=65">le forum des convertisseurs</a>)</li></ul><p>Nous encourageons les utilisateurs d’une des versions béta de phpBB3 à remplacer celle-ci par une installation propre de la dernière version stable.</p><h2>MODs / Styles</h2><p>Pour des problèmes liés au MODs, nous vous invitons à créer votre sujet dans le <a href="http://www.phpbb.com/community/viewforum.php?f=81">forum des MODs</a>.<br />Pour des problèmes liés aux styles, templates, et jeu d’images, nous vous invitons à créer votre sujet dans le <a href="http://www.phpbb.com/community/viewforum.php?f=80">forum des Styles</a>.<br /><br />Si votre question est en relation avec une archive précise, créez votre message directement dans le sujet dédié à l’archive.</p><h2>Obtention du support</h2><p><a href="http://www.phpbb.com/community/viewtopic.php?f=14&amp;t=571070">Le package de bienvenue de phpBB</a><br /><a href="http://www.phpbb.com/support/">Section de support</a><br /><a href="http://www.phpbb.com/support/documentation/3.0/quickstart/">Guide de démarrage rapide</a><br /><br />Pour vous assurer d’être à jour et au courant des dernières nouvelles, nous vous conseillons de souscrire à notre <a href="http://www.phpbb.com/support/">lettre d’informations</a>.<br /><br />',
	'SYNC_FORUMS'   			=> 'Synchronisation des forums',
	'SYNC_POST_COUNT'			=> 'Synchronisation de post_counts',
	'SYNC_POST_COUNT_ID'		=> 'Synchronisation de post_counts de <var>l’entrée</var> %1$s à %2$s.',
	'SYNC_TOPICS'  				=> 'Synchronisation des sujets',
	'SYNC_TOPIC_ID'   			=> 'Synchronisation des sujets du <var>topic_id</var> $1%s à $2%s.',

	'TABLES_MISSING'   			=> 'Impossible de trouver ces tables<br />» <strong>%s</strong>.',
	'TABLE_PREFIX'  			=> 'Préfixe de tables',
	'TABLE_PREFIX_SAME'   		=> 'Le préfixe de table doit être celui utilisé par le logiciel à convertir.<br />» Le préfixe indiqué était %s',
	'TESTS_PASSED'  			=> 'Vérifications réussies',
	'TESTS_FAILED'   			=> 'Echec des vérifications',

	'UNABLE_WRITE_LOCK'   		=> 'Impossible d’écrire sur un fichier verrouillé',
	'UNAVAILABLE'   			=> 'Indisponible',
	'UNWRITABLE'   				=> 'Non autorisé en écriture',
	'UPDATE_TOPICS_POSTED'      => 'Mise à jour des informations de sujets',
	'UPDATE_TOPICS_POSTED_ERR'  => 'Une erreur est survenue lors de la mise à jour des informations des sujets. Vous pourrez réessayer plus tard via le panneau d’administration.',
	'VERIFY_OPTIONS'			=> 'Vérification des options de conversion',
	'VERSION'               	=> 'Version',

	'WELCOME_INSTALL'  			=> 'Bienvenue dans l’installation de phpBB 3',
	'WRITABLE'   				=> 'Autorisé en écriture',
));

// Updater
$lang = array_merge($lang, array(
	'ALL_FILES_UP_TO_DATE'      => 'Tous les fichiers ont été mis à jour. Vous devriez maintenant <a href="../ucp.php?mode=login&amp;redirect=adm/index.php%3Fi=send_statistics%26mode=send_statistics">vous connecter à votre forum</a> afin de vérifier que tout fonctionne correctement. N’oubliez pas de supprimer, renommer ou déplacer le répertoire install de votre système! Merci de nous envoyer les informations mises à jour au sujet de votre serveur et des configurations de votre forum depuis le module <a href="../ucp.php?mode=login&amp;redirect=adm/index.php%3Fi=send_statistics%26mode=send_statistics">d’envoi du rapport de statistiques</a> dans votre ACP.',
	'ARCHIVE_FILE'  			=> 'Fichier source dans l’archive',

	'BACK'				=> 'Retour',
	'BINARY_FILE'		=> 'Fichier binaire',
	'BOT'				=> 'Aspirateur/Robot',

	'CHANGE_CLEAN_NAMES'			=> 'La méthode utilisée, pour être sûr qu’un nom d’utilisateur n’est pas utilisé plusieurs fois, a été modifiée. Au moment de comparer, certains utilisateurs auront le même nom avec la nouvelle méthode. Avant de procéder, vous devrez supprimer ou renommer ces utilisateurs pour être sûr que chaque nom ne soit utilisé que par un seul utilisateur.',
	'CHECK_FILES'					=> 'Vérifier les fichiers',
	'CHECK_FILES_AGAIN'				=> 'Vérifier à nouveau les fichiers',
	'CHECK_FILES_EXPLAIN'   		=> 'Pendant la prochaine étape, tous les fichiers seront comparés aux fichiers de mise à jour - cela peut prendre du temps si c’est la première vérification de fichiers.',
	'CHECK_FILES_UP_TO_DATE'		=> 'Selon votre base de données, votre forum est à jour. Vous pouvez effectuer la vérification de fichiers pour vous assurer que tous les fichiers sont bien à jour avec la dernière version de phpBB.',
	'CHECK_UPDATE_DATABASE'  		=> 'Continuer la mise à jour',
	'COLLECTED_INFORMATION'   		=> 'Informations du fichier',
	'COLLECTED_INFORMATION_EXPLAIN' => 'La liste ci-dessous vous montre les informations sur les fichiers à mettre à jour. Lisez ces informations afin de mettre à jour correctement ces fichiers.',
	'COLLECTING_FILE_DIFFS'			=> 'Collecte les différences entre les fichiers',
	'COMPLETE_LOGIN_TO_BOARD'   	=> 'Vous pouvez maintenant vous <a href="../ucp.php?mode=login">connecter à votre forum</a> et vérifier si tout fonctionne correctement. N’oubliez pas de supprimer, renommer ou déplacer le dossier <em>install</em>!',
	'CONTINUE_UPDATE_NOW'			=> 'Continuer la procédure de mise à jour maintenant',	// Shown within the database update script at the end if called from the updater
	'CONTINUE_UPDATE'				=> 'Continuer la mise à jour maintenant',				// Shown after file upload to indicate the update process is not yet finished
	
	'CURRENT_FILE'   				=> 'Début du conflit - Code du fichier original avant la mise à jour',
	'CURRENT_VERSION'   			=> 'Version actuelle',

	'DATABASE_TYPE'						=> 'Type de base de données',
	'DATABASE_UPDATE_INFO_OLD'			=> 'Le fichier de mise à jour de la base dans votre dossier d’installation est obsolète. Vérifiez que vous avez chargé la bonne version du fichier.',
	'DELETE_USER_REMOVE'				=> 'Supprimer l’utilisateur et ses messages',
	'DELETE_USER_RETAIN'				=> 'Supprimer l’utilisateur mais conserver ses messages',
	'DESTINATION'						=> 'Fichier de destination',
	'DIFF_INLINE'						=> 'Inclus',
	'DIFF_RAW'							=> 'Modification unie brute',
	'DIFF_SEP_EXPLAIN'					=> 'Bloc de code utilisé dans le fichier mis à jour ou le nouveau fichier',
	'DIFF_SIDE_BY_SIDE'					=> 'Côte à côte',
	'DIFF_UNIFIED'						=> 'Modification unie',
	'DO_NOT_UPDATE'						=> 'Ne pas mettre à jour ce fichier',
	'DONE'								=> 'Terminé',
	'DOWNLOAD'							=> 'Télécharger',
	'DOWNLOAD_AS'						=> 'Télécharger sous',
	'DOWNLOAD_UPDATE_METHOD_BUTTON' 	=> 'Télécharger l’archive des fichiers modifiés (recommandé)',
	'DOWNLOAD_CONFLICTS'				=> 'Télécharger les conflits de ce fichier',
	'DOWNLOAD_CONFLICTS_EXPLAIN'		=> 'Rechercher &lt;&lt;&lt; afin de repérer les conflits',
	'DOWNLOAD_UPDATE_METHOD'			=> 'Télécharger une archive de fichiers modifiés',
	'DOWNLOAD_UPDATE_METHOD_EXPLAIN'	=> 'Une fois téléchargée, vous devez décompresser l’archive. Vous y trouverez les fichiers modifiés que vous devez charger dans votre répertoire à la racine de phpBB. Chargez les fichiers à leur emplacement respectif. Après avoir chargé tous les fichiers, vérifiez à nouveau les fichiers avec l’autre bouton ci-dessous.',

	'ERROR'		=> 'Erreur',
	'EDIT_USERNAME'	=> 'Editer le nom d’utilisateur',

	'FILE_ALREADY_UP_TO_DATE'		=> 'Le fichier est déjà à jour.',
	'FILE_DIFF_NOT_ALLOWED'			=> 'Le fichier n’est pas autorisé à être modifié.',
	'FILE_USED'						=> 'Informations utilisées de',			// Single file
	'FILES_CONFLICT'				=> 'Fichiers en conflit',
	'FILES_CONFLICT_EXPLAIN'		=> 'Les fichiers suivants sont modifiés et ne représentent pas les fichiers originaux de l’ancienne version. phpBB a déterminé qu’il ne pouvait pas fusionner ce fichier sans créer de conflit. Merci de rechercher les conflits et essayez de les résoudre manuellement ou continuez la mise à jour en choisissant une méthode de mise à jour. Si vous résolvez les conflits manuellement, vérifiez à nouveau les fichiers après leur modification. Vous pouvez aussi choisir une méthode de mise à jour pour chaque fichier. La première donnera un fichier où les modifications contenues dans les lignes en conflit seront perdues, l’autre ignorera les modifications du nouveau fichier.',
	'FILES_MODIFIED'				=> 'Fichiers modifiés',
	'FILES_MODIFIED_EXPLAIN'   		=> 'Les fichiers suivants sont modifiés et ne représentent pas les fichiers originaux de l’ancienne version. Le fichier mis à jour sera une fusion entre vos modifications et le nouveau fichier.',
	'FILES_NEW'						=> 'Nouveaux fichiers',
	'FILES_NEW_EXPLAIN'				=> 'Les fichiers suivants n’existent actuellement pas dans votre installation. Ces fichiers seront ajoutés à votre installation.',
	'FILES_NEW_CONFLICT'			=> 'Nouveaux fichiers en conflit',
	'FILES_NEW_CONFLICT_EXPLAIN'	=> 'Les fichiers suivants sont nouveaux dans la dernière version, mais il existe déjà un fichier de même nom au même emplacement. Ce fichier sera écrasé par le nouveau fichier.',
	'FILES_NOT_MODIFIED'			=> 'Fichiers non modifiés',
	'FILES_NOT_MODIFIED_EXPLAIN'	=> 'Les fichiers suivants ne sont pas modifiés et représentent les fichiers originaux de l’ancienne version.',
	'FILES_UP_TO_DATE'				=> 'Fichiers déjà à jour',
	'FILES_UP_TO_DATE_EXPLAIN'		=> 'Les fichiers suivants sont déjà à jour et ne nécessitent pas d’être mis à jour.',
	'FTP_SETTINGS'					=> 'Paramètres FTP',
	'FTP_UPDATE_METHOD'				=> 'Chargement FTP',

	'INCOMPATIBLE_UPDATE_FILES'		=> 'Les fichiers de mise à jour trouvés sont incompatibles avec votre version installée. Votre version installée est la %1$s et les fichiers de mise à jour sont pour la mise à jour de phpBB %2$s vers %3$s.',
	'INCOMPLETE_UPDATE_FILES'		=> 'Les fichiers de mise à jour sont incomplets.',
	'INLINE_UPDATE_SUCCESSFUL'		=> 'La mise à jour de la base de données a été réalisée. Vous avez besoin de continuer le processus de mise à jour à présent.',

	'KEEP_OLD_NAME'		=> 'Conserver le nom d’utilisateur',
	
	'LATEST_VERSION'		=> 'Dernière version',
	'LINE'					=> 'Ligne',
	'LINE_ADDED'			=> 'Ajoutée',
	'LINE_MODIFIED'			=> 'Modifiée',
	'LINE_REMOVED'			=> 'Supprimée',
	'LINE_UNMODIFIED'		=> 'Non modifiée',
	'LOGIN_UPDATE_EXPLAIN'	=> 'Afin de mettre à jour votre installation, vous devez d’abord vous connecter.',

	'MAPPING_FILE_STRUCTURE'	=> 'Pour faciliter le chargement, vous avez ici les emplacements des fichiers qui conduisent à votre installation de phpBB.',
	
	'MERGE_MODIFICATIONS_OPTION'   => 'Modifications de la fusion',
	
	'MERGE_NO_MERGE_NEW_OPTION'	=> 'Ne pas fusionner - utiliser un nouveau fichier',
	'MERGE_NO_MERGE_MOD_OPTION'	=> 'Ne pas fusionner - utiliser le fichier installé actuel',
	'MERGE_MOD_FILE_OPTION'		=> 'Fusionner les modifications (supprime le nouveau code de phpBB dans le bloc en conflit)',
	'MERGE_NEW_FILE_OPTION'		=> 'Fusionner les modifications (supprime le code modifié dans le bloc en conflit)',
	'MERGE_SELECT_ERROR'		=> 'Les modes du fichier fusionné en conflit ne sont pas correctement sélectionnés.',
	'MERGING_FILES'				=> 'Fusion des différences',
	'MERGING_FILES_EXPLAIN'		=> 'Collecte actuellement les modifications finales des fichiers.<br /><br />Patientez jusqu’à ce que phpBB termine toutes les opérations sur les fichiers modifiés.',

	'NEW_FILE'						=> 'Fin du conflit',
	'NEW_USERNAME'					=> 'Nouveau nom d’utilisateur',
	'NO_AUTH_UPDATE'				=> 'Non autorisé à mettre à jour',
	'NO_ERRORS'						=> 'Aucune erreur',
	'NO_UPDATE_FILES'				=> 'Ne pas mettre à jour les fichiers suivants',
	'NO_UPDATE_FILES_EXPLAIN'		=> 'Les fichiers suivants sont nouveaux ou modifiés, mais leur répertoire est introuvable dans votre installation. Si cette liste contient des fichiers vers d’autres répertoires que language/ ou styles/ que vous pouvez avoir à modifier, votre structure de répertoire et la mise à jour peuvent être incomplètes.',
	'NO_UPDATE_FILES_OUTDATED'		=> 'Aucun répertoire de mise à jour valide n’a été trouvé, assurez-vous de bien avoir chargé les fichiers nécessaires.<br /><br />Votre installation ne semble <strong>pas</strong> à jour. Des mises à jour sont disponibles pour votre version de phpBB %1$s, visitez <a href="http://www.phpbb.com/downloads/" rel="external">http://www.phpbb.com/downloads/</a> afin d’obtenir le pack correct pour mettre à jour votre Version %2$s vers la Version %3$s.',
	'NO_UPDATE_FILES_UP_TO_DATE'	=> 'Votre version est à jour. Il n’est pas nécessaire d’utiliser l’outil de mise à jour. Si vous souhaitez faire une vérification intégrale de vos fichiers, assurez-vous d’avoir chargé les fichiers de mise à jour corrects.',
	'NO_UPDATE_INFO'				=> 'Les informations du fichier de mise à jour sont introuvables.',
	'NO_UPDATES_REQUIRED'			=> 'Aucune mise à jour nécessaire',
	'NO_VISIBLE_CHANGES'			=> 'Aucune modification visible',
	'NOTICE'						=> 'Avertissement',
	'NUM_CONFLICTS'					=> 'Nombre de conflits',
	'NUMBER_OF_FILES_COLLECTED'		=> 'Actuellement, %1$d des %2$d fichiers vérifiés comportent des différences.<br />Patientez jusqu’à ce que la vérification des fichiers soit terminée.',

	'OLD_UPDATE_FILES'		=> 'Les fichiers de mise à jour ne sont pas à jour. Les fichiers trouvés pour la mise à jour sont pour phpBB %1$s vers phpBB %2$s mais la dernière version de phpBB est la %3$s.',

	'PACKAGE_UPDATES_TO'				=> 'Le package courant est à jour à la version',
	'PERFORM_DATABASE_UPDATE'			=> 'Exécuter la mise à jour de la base de données',
	'PERFORM_DATABASE_UPDATE_EXPLAIN'	=> 'Vous trouverez ci-dessous un bouton vers le script de mise à jour de la base de données. La mise à jour de la base de données peut prendre un moment, merci de ne pas arrêter son exécution même si elle semble bloquer. Après la mise à jour de la base de données, suivez les instructions pour continuer la procédure de mise à jour.',
	'PREVIOUS_VERSION'					=> 'Version précédente',
	'PROGRESS'							=> 'En cours',

	'RESULT'					=> 'Résultat',
	'RUN_DATABASE_SCRIPT'		=> 'Mettre à jour ma base de données maintenant',

	'SELECT_DIFF_MODE'			=> 'Sélectionner le mode de comparaison',
	'SELECT_DOWNLOAD_FORMAT'	=> 'Sélectionner le format de l’archive à télécharger',
	'SELECT_FTP_SETTINGS'		=> 'Sélectionner les paramètres FTP',
	'SHOW_DIFF_CONFLICT'		=> 'Afficher les différences/conflits',
	'SHOW_DIFF_FINAL'			=> 'Afficher le fichier résultant',
	'SHOW_DIFF_MODIFIED'		=> 'Afficher les différences fusionnées',
	'SHOW_DIFF_NEW'				=> 'Afficher le contenu des fichiers',
	'SHOW_DIFF_NEW_CONFLICT'	=> 'Afficher les conflits',
	'SHOW_DIFF_NOT_MODIFIED'	=> 'Afficher les différences',
	'SOME_QUERIES_FAILED'		=> 'Certaines requêtes ont échoué, les instructions et les erreurs sont listées ci-dessous.',
	'SQL'						=> 'SQL',
	'SQL_FAILURE_EXPLAIN'		=> 'Il n’y a probablement pas lieu de s’inquiéter, la mise à jour va continuer. Si elle échoue, vous pourrez demander de l’aide sur nos forums de support. Consultez le <a href="../docs/README.html">README</a> pour plus d’informations sur comment obtenir de l’assistance.',
	'STAGE_FILE_CHECK'			=> 'Vérifier les fichiers',
	'STAGE_UPDATE_DB'			=> 'Mettre à jour la base de données',
	'STAGE_UPDATE_FILES'		=> 'Mettre à jour les fichiers',
	'STAGE_VERSION_CHECK'		=> 'Vérifier la version',
	'STATUS_CONFLICT'			=> 'Fichier modifié qui produit des conflits',
	'STATUS_MODIFIED'			=> 'Fichier modifié',
	'STATUS_NEW'				=> 'Nouveau fichier',
	'STATUS_NEW_CONFLICT'		=> 'Nouveau fichier en conflit',
	'STATUS_NOT_MODIFIED'		=> 'Fichier non modifié',
	'STATUS_UP_TO_DATE'			=> 'Fichier déjà à jour',

	'TOGGLE_DISPLAY'			=> 'Voir/Masquer la liste des fichiers',
	'TRY_DOWNLOAD_METHOD' 		=> 'Vous pouvez vouloir essayer la méthode de téléchargement des fichiers modifiés.<br />Cette méthode fonctionne toujours et constitue aussi le moyen recommandé pour la mise à jour.',
	'TRY_DOWNLOAD_METHOD_BUTTON'=> 'Essayer cette méthode maintenant',
	
	'UPDATE_COMPLETED'				=> 'La mise à jour est terminée',
	'UPDATE_DATABASE'				=> 'Mettre à jour la base de données',
	'UPDATE_DATABASE_EXPLAIN'		=> 'Dans la prochaine étape, la base de données sera mise à jour.',
	'UPDATE_DATABASE_SCHEMA'		=> 'Mise à jour du schéma de la base de données',
	'UPDATE_FILES'					=> 'Mettre à jour les fichiers',
	'UPDATE_FILES_NOTICE'			=> 'Assurez-vous d’avoir mis également à jour tous les fichiers de votre forum, ce fichier met uniquement à jour la base de données.',
	'UPDATE_INSTALLATION'			=> 'Mettre à jour l’installation de phpBB',
	'UPDATE_INSTALLATION_EXPLAIN'	=> 'Avec cette option, il est possible de mettre à jour votre installation de phpBB vers la dernière version.<br />Pendant le processus, tous vos fichiers seront vérifiés dans leur intégralité. Vous pouvez revoir toutes les différences et les fichiers avant la mise à jour.<br /><br />Le fichier de mise à jour lui-même peut être réalisé de deux manières différentes.</p><h2>Mise à jour manuelle</h2><p>Avec cette mise à jour, vous ne téléchargez que vos paramètres personnels des fichiers modifiés pour être sûr de ne pas perdre les modifications du fichier que vous avez apportées. Après avoir téléchargé ce pack, vous devez charger manuellement les fichiers à leur emplacement correct à la racine de votre répertoire phpBB. Une fois terminé, vous pouvez recommencer l’étape de vérification du fichier pour vérifier si vous avez déplacé les fichiers correctement.</p><h2>Mise à jour automatique par FTP</h2><p>Cette méthode est similaire à la première, mais sans la nécessité de télécharger les fichiers modifiés et de les charger vous-même. Cela sera fait à votre place. Afin d’utiliser cette méthode, vous devez connaître les informations de votre connexion FTP car elles vous seront demandées. Une fois terminé, vous serez redirigé à la vérification du fichier une fois de plus pour savoir si tout a été mis à jour correctement.<br /><br />',
	'UPDATE_INSTRUCTIONS'         => '

      <h1>Annonce de mise à jour</h1>

      <p>Lisez <a href="%1$s" title="%1$s"><strong>cette annonce pour la dernière mise à jour</strong></a> avant de continuer le processus de mise à jour, celle-ci pourrait contenir des informations utiles. Elle contient aussi plusieurs liens ainsi que le détail des changements effectués depuis la dernière version.</p>

      <br />

      <h1>Comment mettre à jour votre installation avec le pack de mise à jour automatique?</h1>

      <p>Les recommandations de mise à jour pour votre installation indiquées ici ne sont valables que pour le pack de mise à jour automatique. Vous pouvez également mettre à jour votre installation en utilisant les méthodes énumérées dans le document INSTALL.html. Les étapes pour mettre à jour automatiquement phpBB3 sont:</p>

      <ul style="margin-left: 20px; font-size: 1.1em;">
         <li>Allez sur la page <a href="http://www.phpbb.com/downloads/" title="http://www.phpbb.com/downloads/">de téléchargement de phpBB.com</a> et téléchargez l’archive de mise à jour automatique du forum: “Automatic Update Package”.<br /><br /></li>
         <li>Décompressez l’archive.<br /><br /></li>
         <li>Chargez le répertoire install sur votre serveur FTP, à la racine de votre forum (où votre fichier config.php est situé).<br /><br /></li>
      </ul>

      <p>Une fois le dossier install chargé, votre forum sera inaccessible pour les utilisateurs normaux.<br /><br />
      <strong><a href="%2$s" title="%2$s">Vous pouvez maintenant démarrer la mise à jour en pointant votre navigateur sur le répertoire install</a>.</strong><br />
      <br />
     Vous serez alors guidé dans le processus de mise à jour. Vous serez averti une fois la mise à jour effectuée.
      </p>
	',
	'UPDATE_INSTRUCTIONS_INCOMPLETE'	=> '

		<h1>Mise à jour incomplète détectée</h1>

		<p>phpBB a détecté une mise à jour automatique incomplète. Assurez-vous que vous avez suivi chaque étape avec l’outil de mise à jour automatique. Vous trouverez ci-dessous le lien pour recommencer, ou allez directement dans votre répertoire install.</p>
 ',
	'UPDATE_METHOD'					=> 'Méthode de mise à jour',
	'UPDATE_METHOD_EXPLAIN'			=> 'Vous pouvez maintenant choisir votre méthode de mise à jour préférée. En utilisant le chargement FTP, vous devrez entrer les informations de votre compte FTP dans un formulaire. Avec cette méthode, les fichiers seront déplacés automatiquement vers le nouvel emplacement et des sauvegardes des anciens fichiers seront créées en ajoutant .bak au nom du fichier. Si vous choisissez de télécharger les fichiers modifiés, vous pourrez les décompresser et les charger manuellement vers leur emplacement correct plus tard.',
	'UPDATE_REQUIRES_FILE'			=> 'L’outil de mise à jour nécessite que le fichier suivant soit présent: %s',
	'UPDATE_SUCCESS'				=> 'La mise à jour a été effectuée.',
	'UPDATE_SUCCESS_EXPLAIN'		=> 'Tous les fichiers ont été mis à jour. La prochaine étape implique de vérifier tous les fichiers une fois de plus pour vous assurer qu’ils ont été mis à jour correctement.',
	'UPDATE_VERSION_OPTIMIZE'		=> 'Mise à jour de la version et optimisation des tables',
	'UPDATING_DATA'					=> 'Mise à jour des données',
	'UPDATING_TO_LATEST_STABLE'		=> 'Mise à jour de la base de données vers la dernière version stable',
	'UPDATED_VERSION'				=> 'Version mise à jour',
	'UPLOAD_METHOD'					=> 'Méthode de chargement',

	'UPDATE_DB_SUCCESS'				=> 'La mise à jour de la base de données a été effectuée.',
	'USER_ACTIVE'					=> 'Utilisateur actif',
	'USER_INACTIVE'					=> 'Utilisateur inactif',

	'VERSION_CHECK'				=> 'Vérification de la version',
	'VERSION_CHECK_EXPLAIN'		=> 'Vérifie si votre installation de phpBB est à jour.',
	'VERSION_NOT_UP_TO_DATE'	=> 'Votre installation de phpBB n’est pas à jour. Continuez le processus de mise à jour.',
	'VERSION_NOT_UP_TO_DATE_ACP'=> 'Votre installation de phpBB n’est pas à jour.<br />Vous trouverez ci-dessous un lien vers l’annonce de publiciation, qui contient plus d’informations comme les instructions de mise à jour.',
	'VERSION_NOT_UP_TO_DATE_TITLE'	=> 'Votre installation de phpBB n’est pas à jour.',
	'VERSION_UP_TO_DATE'		=> 'Votre installation de phpBB est à jour. Bien qu’il n’y ait aucune mise à jour disponible actuellement, vous pouvez continuer afin d’exécuter un contrôle de validité des fichiers.',
	'VERSION_UP_TO_DATE_ACP'	=> 'Votre installation de phpBB est à jour. Aucune mise à jour n’est disponible actuellement.',
	'VIEWING_FILE_CONTENTS'		=> 'Consultation du contenu des fichiers',
	'VIEWING_FILE_DIFF'			=> 'Consultation des différences des fichiers',

	'WRONG_INFO_FILE_FORMAT'	=> 'Mauvais format du fichier d’information',
));

// Default database schema entries...
$lang = array_merge($lang, array(
	'CONFIG_BOARD_EMAIL_SIG'		=> 'Merci, l’équipe du forum',
	'CONFIG_SITE_DESC'				=> 'Description de votre forum',
	'CONFIG_SITENAME'				=> 'votredomaine.com',

	'DEFAULT_INSTALL_POST'			=> 'Ceci est un exemple de message de votre installation phpBB3. Tout semble fonctionner. Vous pouvez si vous le voulez supprimer ce message et continuer à configurer votre forum. Durant le processus d’installation, votre première catégorie et votre premier forum sont assignés à un ensemble de permissions appropriées aux groupes d’utilisateurs que sont les administrateurs, les robots, les modérateurs globaux, les invités, les utilisateurs inscrits et les utilisateurs COPPA inscrits. Si vous choisissez de supprimer également votre première catégorie et votre premier forum, n’oubliez pas de régler les permissions à tous les groupes d’utilisateurs, pour toutes les nouvelles catégories et forums que vous allez créer. Il est recommandé de renommer votre première catégorie et votre premier forum et de copier leurs permissions sur chaque nouvelle catégorie et nouveau forum lors de leur création. Amusez-vous bien!',

	'EXT_GROUP_ARCHIVES'			=> 'Archives',
	'EXT_GROUP_DOCUMENTS'			=> 'Documents',
	'EXT_GROUP_DOWNLOADABLE_FILES'	=> 'Fichiers téléchargeables',
	'EXT_GROUP_FLASH_FILES'			=> 'Fichiers Flash',
	'EXT_GROUP_IMAGES'				=> 'Images',
	'EXT_GROUP_PLAIN_TEXT'			=> 'Texte',
	'EXT_GROUP_QUICKTIME_MEDIA'		=> 'Quicktime Media',
	'EXT_GROUP_REAL_MEDIA'			=> 'Real Media',
	'EXT_GROUP_WINDOWS_MEDIA'		=> 'Windows Media',

	'FORUMS_FIRST_CATEGORY'			=> 'Votre première catégorie',
	'FORUMS_TEST_FORUM_DESC'		=> 'Description de votre premier forum.',
	'FORUMS_TEST_FORUM_TITLE'		=> 'Votre premier forum',

	'RANKS_SITE_ADMIN_TITLE'		=> 'Administrateur du site',
	'REPORT_WAREZ'					=> 'Le message contient un lien vers un logiciel illégal ou piraté.',
	'REPORT_SPAM'					=> 'Le message rapporté a été posté dans le seul but de promouvoir un site Internet ou un autre produit.',
	'REPORT_OFF_TOPIC'				=> 'Le message rapporté est hors sujet.',
	'REPORT_OTHER'					=> 'Le message rapporté n’entre dans aucune autre catégorie, utilisez le champ d’information complémentaire.',

	'SMILIES_ARROW'					=> 'Flèche',
	'SMILIES_CONFUSED'				=> 'Confus',
	'SMILIES_COOL'					=> 'Cool',
	'SMILIES_CRYING'				=> 'Très triste, en pleurs',
	'SMILIES_EMARRASSED'			=> 'Embarrassé',
	'SMILIES_EVIL'					=> 'Diable',
	'SMILIES_EXCLAMATION'			=> 'Exclamation',
	'SMILIES_GEEK'					=> 'Geek',
	'SMILIES_IDEA'					=> 'Idée',
	'SMILIES_LAUGHING'				=> 'Rire',
	'SMILIES_MAD'					=> 'Fou',
	'SMILIES_MR_GREEN'				=> 'M. Vert',
	'SMILIES_NEUTRAL'				=> 'Neutre',
	'SMILIES_QUESTION'				=> 'Question',
	'SMILIES_RAZZ'					=> 'Tire la langue',
	'SMILIES_ROLLING_EYES'			=> 'Yeux tournants',
	'SMILIES_SAD'					=> 'Triste',
	'SMILIES_SHOCKED'				=> 'Choqué',
	'SMILIES_SMILE'					=> 'Sourire',
	'SMILIES_SURPRISED'				=> 'Surprise',
	'SMILIES_TWISTED_EVIL'			=> 'Diable rieur',
	'SMILIES_UBER_GEEK'				=> 'Geek barbu',
	'SMILIES_VERY_HAPPY'			=> 'Très content',
	'SMILIES_WINK'					=> 'Clin d’oeil',

	'TOPICS_TOPIC_TITLE'			=> 'Bienvenue sur phpBB3',
));

?>