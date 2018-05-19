<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: install.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
/**
*
* gym_common [French]
* Translated By: Mathieu M. & gowap [ www.phpbb-seo.com ]
*
*/
/**
* DO NOT CHANGE
*/
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
	// Install
	'SEO_INSTALL_PANEL'	=> 'Installation GYM Sitemaps &amp; RSS',
	'CAT_INSTALL_GYM_SITEMAPS' => 'Installer GYM Sitemaps',
	'CAT_UNINSTALL_GYM_SITEMAPS' => 'Désinstaller GYM Sitemaps',
	'CAT_UPDATE_GYM_SITEMAPS' => 'Mettre à jour GYM Sitemaps',
	'SEO_ERROR_INSTALL'	=> 'Une erreur est survenue lors de l’installation. Si vous souhaitez relancer l’installation, désinstallez d’abord le module.',
	'SEO_ERROR_INSTALLED'	=> 'Le module %s est déjà installé',
	'SEO_ERROR_ID'	=> 'Le module %s n’a pas d’ID.',
	'SEO_ERROR_UNINSTALLED'	=> 'Le module %s est déjà désinstallé',
	'SEO_ERROR_INFO'	=> 'Informations :',
	'SEO_FINAL_INSTALL_GYM_SITEMAPS'	=> 'Aller à l’ACP',
	'SEO_FINAL_UPDATE_GYM_SITEMAPS'	=> 'Aller à l’ACP',
	'SEO_FINAL_UNINSTALL_GYM_SITEMAPS'	=> 'Retour à l’index du forum',
	'SEO_OVERVIEW_TITLE'	=> 'Vue d’ensemble du MOD GYM Sitemaps de phpBB SEO',
	'SEO_OVERVIEW_BODY'	=> 'Bienvenue dans le processus d’installation du MOD GYM Sitemaps &amp; RSS %1$s de phpBB SEO.</p><p>Veuillez lire <a href="%2$s" title="Voir le sujet de mise à disposition" target="_phpBBSEO"><b>le sujet de mise à disposition</b></a> pour plus de détails.</p><p><strong style="text-transform: uppercase;">Note:</strong> Vous devez avoir édité tous les fichiers nécessaires et uploadé tous les nouveaux fichiers avant de continuer avec cet assistant d’installation.</p><p>Cet assistant vous guidera pendant le processus d’installation du module d’administration du MOD GYM Sitemaps &amp; RSS. Ce module vous permettra de mettre en place facilement des Sitemaps Google ainsi que des flux RSS sur votre forum. Son architecture modulaire vous permettra de générer des Sitemaps Google et des flux RSS pour n’importe qu’elle application PHP/SQL installée sur votre site, via l’utilisation de plugins dédiés.<br/>Rendez vous sur le <a href="%3$s" title="Voir le forum de support" target="_phpBBSEO"><b>le forum de support</b></a> pour toute question concernant le MOD.</p>',
	'CAT_SEO_PREMOD'	=> 'GYM Sitemaps &amp; RSS',
	'SEO_INSTALL_INTRO'		=> 'Bienvenue dans le script d’installation de GYM Sitemaps &amp; RSS.',
	'SEO_INSTALL_INTRO_BODY'	=> '<p>Vous êtes sur le point d’installer le module %1$s %2$s. Cet script d’installation va activer le module d’administration du MOD dans l’ACP de phpBB.</p><p>Une fois l’installation effectuée, vous devrez vous rendre dans l’ACP de phpBB pour configurer le module.</p>
	<p><strong>Note:</strong> Si c’est votre première utilisation du MOD, nous vous conseillons de prendre le temps de tester ce MOD sur un serveur local ou privé pour vous familiariser avec ses nombreuses options et possibilités.</p><br/>
	<p>Prérequis :</p>
	<ul>
		<li>Serveur Apache (linux), avec le module mod_rewrite pour activer la réécriture d’URL.</li>
		<li>Serveur IIS (windows), avec le module isapi_rewrite pour activer la réécriture d’URL. Vous devrez cependant modifier les rewriterules pour votre httpd.ini</li>
	</ul>',
	'SEO_INSTALL'		=> 'Installation',
	'UN_SEO_INSTALL_INTRO'		=> 'Bienvenue dans le script de désinstallation de GYM Sitemaps &amp; RSS',
	'UN_SEO_INSTALL_INTRO_BODY'	=> '<p>Vous êtes sur le point de désinstaller le MOD %1$s %2$s.</p>
	<p><strong>Note:</strong> Les Sitemaps et les flux RSS ne seront plus disponibles une fois le module désinstallé.</p>',
	'UN_SEO_INSTALL'		=> 'Désinstallation',
	'SEO_INSTALL_CONGRATS'		=> 'Félicitations !',
	'SEO_INSTALL_CONGRATS_EXPLAIN'	=> '<p>Vous avez correctement installé le MOD %1$s %2$s. Vous devriez maintenant vous rendre dans l’ACP de phpBB pour configurer le module.<p>
	<p>Il se trouve dans la catégorie phpBB SEO, vous pourrez notamment :
	<h3>Gérer précisément vos Sitemaps Google et vos flux RSS</h3>
	<p>Les Sitemaps Google ainsi que les flux RSS supportent la mise en page par transformation XSLT, la feuille de style de votre forum sera même appliquée à ceux-ci sans éditer la moindre ligne de code.</p>
	<p>Les Sitemaps Google ainsi que les flux RSS détectent automatiquement les MODs de réécriture phpBB SEO et leurs réglages, l’adaptation à d’autres solutions de réécriture est aisée.</p>
	<h3>Générer un .htaccess personnalisé</h3>
	<p>Avec les mod rewrite phpBB SEO et une fois que vous aurez procédé aux réglages, vous pourrez générer un fichier .htaccess personnalisé et l’enregistrer directement sur le serveur.</p><br/><h3>Rapport d’installation :</h3>',
	'UN_SEO_INSTALL_CONGRATS'	=> 'Le module d’administration GYM Sitemaps &amp; RSS à été désinstallé.',
	'UN_SEO_INSTALL_CONGRATS_EXPLAIN'	=> '<p>Vous avez désinstallé avec succès le MOD %1$s %2$s.<p>
	<p> Vos Sitemaps et vos flux RSS ne sont donc plus disponibles.</p>',
	'SEO_VALIDATE_INFO'	=> 'Validation :',
	'SEO_LICENCE_TITLE'	=> 'GNU LESSER GENERAL PUBLIC LICENSE',
	'SEO_LICENCE_BODY'	=> 'Le MOD GYM Sitemaps &amp; RSS est diffusé sous la licence GNU LESSER GENERAL PUBLIC LICENSE.',
	'SEO_SUPPORT_TITLE'	=> 'Support',
	'SEO_SUPPORT_BODY'	=> 'Un support complet sera offert sur le <a href="%1$s" title=" Visitez le forum %2$s" target="_phpBBSEO"><b>forum %2$s</b></a>. Nous fournirons des réponses aux questions portant sur l’installation, les problèmes de configuration et l’identification de problèmes courants.</p><p>Profitez de l’occasion pour visiter notre <a href="http://www.phpbb-seo.com/forums/" title="Forum référencement" target="_phpBBSEO"><b>forum d’optimisation du référencement</b></a>.</p><p>Vous devriez vous <a href="http://www.phpbb-seo.com/forums/profile.php?mode=register" title="S’inscrire sur phpBB SEO" target="_phpBBSEO"><b>inscrire</b></a>, vous connecter et <a href="%3$s" title="Etre tenu au courant des mises à jours" target="_phpBBSEO"><b>surveiller le sujet de mise à disposition</b></a> pour être tenu au courant par mail des mises à jour.',
	// Security
	'SEO_LOGIN'		=> 'Vous devez être enregistré et connecté pour pouvoir accéder cette page.',
	'SEO_LOGIN_ADMIN'	=> 'Vous devez être connecté en tant qu’administrateur pour pouvoir accéder à cette page.<br/>Votre session à été détruite pour des raisons de sécurité.',
	'SEO_LOGIN_FOUNDER'	=> 'Vous devez être connecté en tant que fondateur pour pouvoir accéder à cette page.',
	'SEO_LOGIN_SESSION'		=> 'La vérification de session à échoué.<br/>Aucune modification prise en compte.<br/>Votre session à été détruite pour des raisons de sécurité.',
	// Cache status
	'SEO_CACHE_FILE_TITLE'	=> 'Statut du cache',
	'SEO_CACHE_STATUS'		=> 'Le dossier configuré pour le cache est : <b>%s</b>',
	'SEO_CACHE_FOUND'		=> 'Le dossier du cache à bien été trouvé.',
	'SEO_CACHE_NOT_FOUND'		=> 'Le dossier du cache n’à pas été trouvé.',
	'SEO_CACHE_WRITABLE'		=> 'Le dossier du cache est utilisable.',
	'SEO_CACHE_UNWRITABLE'		=> 'Le dossier du cache n’est pas utilisable. Vous devez configurer son CHMOD sur 0777.',
	'SEO_CACHE_FORUM_NAME'		=> 'Nom du forum',
	'SEO_CACHE_URL_OK'		=> 'URL en cache',
	'SEO_CACHE_URL_NOT_OK'		=> 'L’URL du forum n’est pas en cache',
	'SEO_CACHE_URL'			=> 'URL finale',
	'SEO_CACHE_MSG_OK'	=> 'Le fichier du cache a bien été mis à jour.',
	'SEO_CACHE_MSG_FAIL'	=> 'Un erreur s’est produite lors de la mise à jour du cache.',
	'SEO_CACHE_UPDATE_FAIL'	=> 'L’URL que vous avez soumise ne peut être utilisée, le cache n’a pas été modifié.',
	// Update
	'UPDATE_SEO_INSTALL_INTRO'		=> 'Bienvenue dans le script de mise à jour de GYM Sitemaps &amp; RSS.',
	'UPDATE_SEO_INSTALL_INTRO_BODY'	=> '<p>Vous êtes sur le point de mettre à jour le module %1$s vers la version %2$s. Cet script d’installation va mettre à jour la base de donnée de phpBB.<br/>Vos réglages actuels ne seront pas affectés</p>
	<p><strong>Note:</strong> Ce script ne met pas à jour les fichiers de GYM Sitemaps &amp; RSS.<br/><br/>Pour mettre à jour depuis n’importe quelle version 2.0.x (phpBB3), vous <b>devez</b> tout d’abord uploader tous les fichiers contenus dans le dossier <b>root/</b> de l’archive dans le dossier ftp de phpBB, en prenant soin de conserver vos éventuelles modification des fichiers de template (dossier phpBB/styles/, .html, .js et .xsl) ajoutés par le module.<br/><br/>Vous <b>pouvez</b> à tout moment relancer cette mise à jour si par exemple vous aviez oublié d’uploader des fichiers ou simplement pour réafficher la liste des modifications des fichiers de phpBB3.</p>',
	'UPDATE_SEO_INSTALL'		=> 'Mettre à jour',
	'SEO_ERROR_NOTINSTALLED'	=> 'GYM Sitemaps &amp; RSS n’est pas installé!',
	'SEO_UPDATE_CONGRATS_EXPLAIN'	=> '<p>Vous avez correctement mis à jour le MOD %1$s vers la version %2$s.<p>
	<p><strong>Note:</strong> Ce script ne met pas à jour les fichiers de GYM Sitemaps &amp; RSS.<br/><b>Veuillez</b> appliquer les modifications ci-dessous.</p><br/><h3>Rapport de mise à jour :</h3>',
));
?>