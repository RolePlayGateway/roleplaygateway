<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output 
	method="html" 
	version="1.0" 
	encoding="utf-8" 
	omit-xml-declaration="yes"		
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" 
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
	indent="yes" />
<xsl:template match="/rss" >

<xsl:variable name="rss_link">
	<xsl:value-of select="channel/textInput/link" />
</xsl:variable>
<xsl:variable name="home_link">{ROOT_URL}</xsl:variable>
<xsl:variable name="browser">
	<xsl:choose><xsl:when test="system-property('xsl:vendor')='Transformiix'">mozilla</xsl:when>
		<xsl:otherwise>other</xsl:otherwise>
	</xsl:choose>
</xsl:variable>
<xsl:variable name="sorting">
	<xsl:choose><xsl:when test="$browser='mozilla'">descending</xsl:when>
		<xsl:otherwise>ascending</xsl:otherwise>
	</xsl:choose>
</xsl:variable>
<html xmlns="http://www.w3.org/1999/xhtml" dir="{S_CONTENT_DIRECTION}" lang="{S_USER_LANG}" xml:lang="{S_USER_LANG}">
<head>
	<base href="{PHPBB_URL}"/>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title><xsl:value-of select="channel/title" /> -  {L_FEED}</title>
	<link rel="alternate" type="application/rss+xml" title="{channel/title}" href="{$rss_link}" />
	<link rel="stylesheet" href="{T_CSS_PATH}" type="text/css"  media="screen, projection"/>
	<link href="{T_STYLE_PATH}normal.css" rel="stylesheet" type="text/css" title="A" />
	<link href="{T_STYLE_PATH}medium.css" rel="alternate stylesheet" type="text/css" title="A+" />
	<link href="{T_STYLE_PATH}large.css" rel="alternate stylesheet" type="text/css" title="A++" />
	<script type="text/javascript" src="{T_STYLE_PATH}gym_js.js"></script>
</head>
<body id="phpbb">
<!--
	GYM Sitemaps and RSS XSLTransform
	(C) 2006, 2007, 2008, 2009 phpBB SEO - http://www.phpbb-seo.com/
-->
			<div id="wrap">
				<a id="top" name="top" accesskey="t"></a>
				<div id="page-header">
					<div class="headerbar">
						<div class="inner"><span class="corners-top"><span></span></span>
							<div id="site-description"><a href="{$home_link}" title="{L_HOME}" id="logo"><img src="{channel/image/url}" alt="{channel/image/title}" /></a>
								<h1>{SITENAME}</h1>
								<p>{SITE_DESCRIPTION}</p>
								<p style="display: none;"><a href="#start_here">{L_SKIP}</a></p>
							</div>
							<div id="search-box">
								<form action="{PHPBB_URL}search.php" method="post" id="search">
								<fieldset>
									<input name="keywords" id="keywords" type="text" maxlength="128" title="" class="inputbox search" value="" /> 
									<input class="button2" value="{L_SEARCH}" type="submit" /><br />
									<a href="{PHPBB_URL}search.php" title="{L_SEARCH_ADV_EXPLAIN}">{L_SEARCH_ADV}</a>
								</fieldset>
								</form>
							</div>
							<span class="corners-bottom"><span></span></span>
						</div>
					</div>
					<div class="navbar">
						<div class="inner"><span class="corners-top"><span></span></span>
							<ul class="linklist navlinks">
								<li class="icon-home">
									<a href="{$home_link}" accesskey="h">{L_HOME}</a>&#160;<strong>&#8249;</strong>&#160;
									<xsl:if test="$home_link != '{PHPBB_URL}'">
										<xsl:if test="'{PHPBB_URL}' != channel/link">
											<a href="{PHPBB_URL}">{L_FORUM_INDEX}</a>&#160;<strong>&#8249;</strong>&#160;
										</xsl:if>
									</xsl:if>
									<a href="{channel/link}" title="{channel/title}"><span class="html"><xsl:value-of select="channel/title" /></span></a>&#160;<strong>&#8249;</strong>&#160;<a href="{$rss_link}">{L_SOURCE}</a></li>
								<li class="rightside"><a href="#" onclick="fontsizeup(); return false;" class="fontsize" title="{L_CHANGE_FONT_SIZE}">{L_CHANGE_FONT_SIZE}</a></li>
							</ul>
							<ul class="linklist leftside">
								<li class="icon-ucp">
									<a href="{$rss_link}" title="{channel/title}" accesskey="u"><span class="html"><xsl:value-of select="channel/title" />&#160;-&#160;{L_FEED}</span></a>
								</li>
							</ul>
							<ul class="linklist rightside">
								<li class="icon-faq"><a href="{PHPBB_URL}faq.php" title="{L_FAQ_EXPLAIN}">{L_FAQ}</a></li>
								<li class="icon-register"><a href="{PHPBB_URL}ucp.php?mode=register">{L_REGISTER}</a></li>
							</ul>
							<span class="corners-bottom"><span></span></span>
						</div>
					</div>
				</div>
				<a name="start_here"></a>
				<div id="page-body"><br/>
					<div class="clear"></div>
					<xsl:for-each select="channel">
					<div class="post bg3">
						<div class="inner"><span class="corners-top"><span></span></span>
							<div class="postbody"><div class="html"><p><h2><a href="{link}" title="{title}"><span class="html"><xsl:value-of select="title" disable-output-escaping="yes"/></span></a></h2></p><hr/>

								<p><span class="html"><xsl:call-template name="nl2br"><xsl:with-param name="input" select="description" /></xsl:call-template></span><br/><br/>
{L_SUBSCRIBE}
							<form action="" method="POST" >
								<fieldset >
									<label><a href="{$rss_link}" title="{title}"><img src="{T_IMAGE_PATH}feed-icon.png" alt="{L_2_LINK}" align="middle"/></a> {L_2_LINK}</label>
									<input name="urlrss" type="text" value="{$rss_link}" size="80" maxlength="500"/>
								</fieldset>
							</form>
							<br/>
{L_LAST_UPDATE} : <xsl:value-of select="lastBuildDate"/><br/>
{L_UPDATE} : <xsl:value-of select="ttl"/>&#160;{L_MINUTES}.<br/><br/>
				<xsl:choose>
					<xsl:when test="count(item) = 1">&#160;<b>{L_ITEM_LISTED}</b>
						</xsl:when>
					<xsl:otherwise>
						<b><xsl:value-of select="count(item)"/>&#160;{L_ITEMS_LISTED}</b>
					</xsl:otherwise>
					</xsl:choose></p></div></div>
							<dl class="postprofile">
								<dt>{L_SUBSCRIBE_POD}<br/><br/>
								<a href="http://fusion.google.com/add?feedurl={$rss_link}" target="_google"><img src="{T_IMAGE_PATH}addGoogle.gif" border="0" alt="Add to Google" title="Add to Google"/></a><br/>
								<a href="http://add.my.yahoo.com/rss?url={$rss_link}" target="_yahoo"><img src="{T_IMAGE_PATH}addtomyyahoo.gif" border="0" alt="Add to My Yahoo" title="Add to My Yahoo"/></a><br/>
								<a href="http://my.msn.com/addtomymsn.armx?id=rss&#038;ut={$rss_link}&#038;ru={$rss_link}" target="_msn"><img src="{T_IMAGE_PATH}MyMSN.gif" alt="Add to My MSN" title="Add to My MSN"/></a><br/>
								<a href="http://feeds.my.aol.com/index.jsp?url={$rss_link}" target="_aol"><img alt="Add to MY AOL" src="{T_IMAGE_PATH}myaol.gif"  title="Add to My AOL" border="0"/></a><br/>
								<a href="http://www.newsgator.com/ngs/subscriber/subext.aspx?url={$rss_link}" target="_newsgator"><img src="{T_IMAGE_PATH}newsgator.gif" alt="Subscribe in NewsGator Online" title="Subscribe in NewsGator Online" border="0"/></a><br/>
								<a href="http://www.netvibes.com/subscribe.php?url={$rss_link}" target="_netvibes"><img src="{T_IMAGE_PATH}add2netvibes.gif" border="0" alt="Add to Netvibes" title="Add to Netvibes"/></a><br/>
								<a href="http://www.pageflakes.com/subscribe.aspx?url={$rss_link}" target="_pageflakes"><img src="{T_IMAGE_PATH}pageflakes.gif" border="0" alt="Add to Page Flakes" title="Add to Page Flakes"/></a><br/>
								</dt>
							</dl>
							
							<span class="corners-bottom"><span></span></span>
						</div>
					</div>
					<br/><br/>
					<xsl:for-each select="item">
						<xsl:sort select="substring(pubDate,12,string-length(pubDate))" order="{$sorting}" data-type="number"/> 
					<div class="post bg2">
						<div class="inner"><span class="corners-top"><span></span></span>
							<div class="postbody">
								<div class="content"><p><h2><a href="{link}" title="{title}"><span class="html"><xsl:value-of select="title" disable-output-escaping="yes"/></span></a></h2></p>
									<span class="html"><xsl:call-template name="nl2br"><xsl:with-param name="input" select="description" /></xsl:call-template></span>
									<div class="signature">
										<b>{L_BOOKMARK_THIS}</b>&#160;
										<a href="http://www.scoopeo.com/scoop/new?newurl={link}&amp;title={title}" title="Scoopeo : {title}"><img src="{T_IMAGE_PATH}scoopeo.png" alt="Scoopeo" /></a>&#160;  
										<a href="http://www.wikio.fr/publish?url={link}&amp;title={title}" title="Wikio : {title}"><img src="{T_IMAGE_PATH}wikio.gif" alt="Wikio" /></a>&#160;
										<a href="http://digg.com/submit?phase=2&amp;url={link}&amp;title={title}" title="Digg : {title}"><img src="{T_IMAGE_PATH}digg.png" alt="Digg" /></a>&#160;
										<a href="http://www.fuzz.fr/submit?url={link}&amp;title={title}" title="Fuzz : {title}"><img src="{T_IMAGE_PATH}fuzz.png" alt="Fuzz" /></a>&#160;
										<a href="http://www.nuouz.com/addNews.aspx?url={link}&amp;title={title}" title="Nuouz : {title}"><img src="{T_IMAGE_PATH}nuouz.png" alt="Nuouz" /></a>&#160;
										<a href="http://reddit.com/submit?url={link}&amp;title={title}" title="Reddit : {title}"><img src="{T_IMAGE_PATH}reddit.png" alt="Reddit" /></a>&#160;
										<a href="http://www.addthis.com/bookmark.php" onclick="window.open('http://www.addthis.com/bookmark.php?wt=nw&amp;url={link}&amp;title={title}', 'addthis', 'scrollbars=yes,menubar=no,resizable=yes,toolbar=no,location=no,status=no,width=620,height=560,left=200,top=100'); return false;" title="addThis : {title}"><img src="{T_IMAGE_PATH}addthis.gif" alt="addThis" /></a>
									</div>
								</div>
							</div>
							<dl class="postprofile">
								<dt><b>{L_LINK} :</b><br/> <a href="{link}" title="{title}" ><span class="html"><xsl:value-of select="title" disable-output-escaping="yes"/></span></a><br/>
									<b>{L_SOURCE} :</b><br/> <a href="{source/@url}" title="{source}"><img src="{T_IMAGE_PATH}feed-icon.png" alt="{L_2_LINK}" align="middle"/>&#160;<span class="html"><xsl:value-of select="source" disable-output-escaping="yes"/></span></a><br/> 
									<b>{L_LASTMOD_DATE} :</b><br/> <xsl:value-of select="pubDate" />
								</dt>
								<dd> </dd>
							</dl>
							<div class="back2top"><a href="#wrap" class="top" title="{L_BACK_TO_TOP}"></a>{L_BACK_TO_TOP}&#160;</div>
							<span class="corners-bottom"><span></span></span>
						</div>
					</div>
					<br/><hr class="divider" />
					</xsl:for-each>
				</xsl:for-each>
				</div>
			</div>

<div class="copyright">Powered by <a href="http://www.phpbb.com/">phpBB</a> &#169; 2000 - 2008 phpBB Group</div>
<!--
	We request you retain the full copyright notice below, as well as in all templates you may use,
	including the link to www.phpbb-seo.com.
	This not only gives respect to the large amount of time given freely by the developers
	but also helps build interest, traffic and use of www.phpBB-SEO.com
	If you cannot (for good reason) retain the full copyright we request you at least leave in place the
	"Copyright phpBB SEO" line, with "phpBB SEO" linked to www.phpbb-seo.com.
	If you refuse to include even this, then support and further development on our forums may be affected.
	The phpBB SEO Team : 2008.
-->
<div class="copyright">{L_COPY}&#160;<a href="http://feedvalidator.org/check.cgi?url={$rss_link}"><img src="{T_IMAGE_PATH}rss-valid.gif" alt="{L_RSS_VALID}" title="{L_RSS_VALID}" /></a></div>
</body>
</html>
</xsl:template>
<!-- nl2br template for cleaner output, no brs in xml ! -->
<xsl:template name="nl2br">
	<xsl:param name="input" />
		<xsl:choose>
			<xsl:when test="contains($input,'&#xA;')">
				<xsl:call-template name="nl2br"><xsl:with-param name="input" select="concat(substring-before($input,'&#xA;'), '&lt;br&gt;',substring-after($input,'&#xA;'))" />
				</xsl:call-template>
			</xsl:when>
		<xsl:otherwise>
			<xsl:value-of disable-output-escaping="yes" select="$input" />
		</xsl:otherwise>
		</xsl:choose>
</xsl:template>
</xsl:stylesheet>
