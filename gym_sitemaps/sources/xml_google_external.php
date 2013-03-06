<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: xml_google_external.php 112 2009-09-30 17:21:34Z dcz $
* @copyright (c) 2006 - 2009 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
/**
 * Please note :
 *
 * 	If you want to include here sitemaps from a different domain than
 * 	the one used by GYM, you will have to declare it in the source domain's
 * 	robots.txt.
 *
 * Example :
 *
 *	www.host1.com with Sitemap file sitemap-host1.xml
 *	To include the sitemaps in GYM's one (http://www.sitemaphost.com/sitemap-host1.xml)
 *	you need to add :
 *
 *		Sitemap: http://www.sitemaphost.com/sitemap-host1.xml
 *
 *	In www.host1.com's robots.txt.
 *
 * Otherwise, the sitemap will be refused.
 *
 * http://www.sitemaps.org/protocol.php#sitemaps_cross_submits
 */
$external_setup = array(
	// Pattern : name => url
	// Each name must be unique and must not match an .xml file name (google_(name).xml)
	// 'site-map_name' => 'http://www.example.com/sitemap.xml',
);
?>