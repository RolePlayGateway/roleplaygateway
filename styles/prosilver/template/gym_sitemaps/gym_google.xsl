<xsl:stylesheet version="2.0" 
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  
<xsl:output 
	method="html" 
	version="1.0" 
	encoding="utf-8" 
	omit-xml-declaration="yes"		
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" 
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
	indent="yes" />

  <!-- Root template -->    
  <xsl:template match="/">
     <!-- Store in $fileType if we are in a sitemap or in a SitemapIndex -->
      <xsl:variable name="fileType">
        <xsl:choose>
		  <xsl:when test="//sitemap:url">Sitemap</xsl:when>
		  <xsl:otherwise>SitemapIndex</xsl:otherwise>
        </xsl:choose>      
</xsl:variable>
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
<xsl:variable name="home_link">{ROOT_URL}</xsl:variable>

<html xmlns="http://www.w3.org/1999/xhtml" dir="{S_CONTENT_DIRECTION}" lang="{S_USER_LANG}" xml:lang="{S_USER_LANG}">
<head>
	<base href="{PHPBB_URL}"/>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title>
		<xsl:choose><xsl:when test="$fileType='Sitemap'">{L_SITEMAP} : <xsl:choose><xsl:when test="sitemap:urlset/sitemap:url/sitemap:loc='{ROOT_URL}'"><xsl:value-of select="substring-after('{ROOT_URL}', 'http://')"></xsl:value-of></xsl:when>
			<xsl:otherwise><xsl:value-of select="substring-after(sitemap:urlset/sitemap:url/sitemap:loc, '{ROOT_URL}')"/></xsl:otherwise></xsl:choose></xsl:when>
		<xsl:otherwise>{L_SITEMAPINDEX}</xsl:otherwise>
		</xsl:choose>
	</title>
	<link rel="stylesheet" href="{T_CSS_PATH}" type="text/css"  media="screen, projection"/>
	<link href="{T_STYLE_PATH}normal.css" rel="stylesheet" type="text/css" title="A" />
	<link href="{T_STYLE_PATH}medium.css" rel="alternate stylesheet" type="text/css" title="A+" />
	<link href="{T_STYLE_PATH}large.css" rel="alternate stylesheet" type="text/css" title="A++" />
	<script type="text/javascript" src="{T_STYLE_PATH}gym_js.js"></script>
</head>
      <!-- Body -->
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
							<div id="site-description"><a href="{$home_link}" title="{L_HOME}" id="logo"><img src="{T_IMAGE_PATH}site_logo.gif" alt="{SITENAME}" /></a>
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
									<a href="{$home_link}" accesskey="h">{L_HOME}</a>
									<xsl:if test="$home_link != '{PHPBB_URL}'">
										&#160;<strong>&#8249;</strong>&#160;<a href="{PHPBB_URL}">{L_FORUM_INDEX}</a>
									</xsl:if>
									<xsl:if test="$fileType='Sitemap'">
										<xsl:variable name="sitemap_link"><xsl:value-of select="sitemap:urlset/sitemap:url/sitemap:loc"/></xsl:variable>
										<xsl:if test="'{PHPBB_URL}' != $sitemap_link">&#160;<strong>&#8249;</strong>&#160;<a href="{$sitemap_link}"> <xsl:value-of select="substring-after($sitemap_link, '{ROOT_URL}')"/></a></xsl:if>
									</xsl:if></li>
								<li class="rightside"><a href="#" onclick="fontsizeup(); return false;" class="fontsize" title="{L_CHANGE_FONT_SIZE}">{L_CHANGE_FONT_SIZE}</a></li>
							</ul>
							<ul class="linklist leftside">
								<li class="icon-ucp">
								<xsl:choose>
								<xsl:when test="$fileType='Sitemap'">{L_SITEMAP_OF} : <a href="{sitemap:urlset/sitemap:url/sitemap:loc}"> <xsl:value-of select="substring-after(sitemap:urlset/sitemap:url/sitemap:loc, '{ROOT_URL}')"/></a></xsl:when>
								<xsl:otherwise>{L_SITEMAPINDEX}</xsl:otherwise>
								</xsl:choose>
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
					<div class="post bg3">
						<div class="inner"><span class="corners-top"><span></span></span>
	<xsl:choose>
		<xsl:when test="$fileType='Sitemap'"><h2><a href="{sitemap:urlset/sitemap:url/sitemap:loc}">{L_SITEMAP_OF} : <xsl:choose><xsl:when test="sitemap:urlset/sitemap:url/sitemap:loc='{ROOT_URL}'"><xsl:value-of select="substring-after('{ROOT_URL}', 'http://')"></xsl:value-of></xsl:when>
			<xsl:otherwise><xsl:value-of select="substring-after(sitemap:urlset/sitemap:url/sitemap:loc, '{ROOT_URL}')"/></xsl:otherwise>
  		</xsl:choose>
			</a></h2>
				  <h4>{L_NUMBER_OF_URL} : <xsl:value-of select="count(sitemap:urlset/sitemap:url)"></xsl:value-of></h4>  </xsl:when>
			  <xsl:otherwise><h2>{L_SITEMAPINDEX}</h2>
		<h4>{L_NUMBER_OF_SITEMAP} : <xsl:value-of select="count(sitemap:sitemapindex/sitemap:sitemap)"></xsl:value-of></h4></xsl:otherwise>
</xsl:choose><br/>
							<span class="corners-bottom"><span></span></span>
						</div>
					</div><br/>
		<xsl:choose>
		<xsl:when test="$fileType='Sitemap'">
			<xsl:call-template name="sitemapTable"/></xsl:when>
	      <xsl:otherwise><xsl:call-template name="siteindexTable"/></xsl:otherwise>
  		</xsl:choose>
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
	The phpBB SEO Team : 2006.
-->
<div class="copyright">{L_COPY}</div>
      </body>
    </html>
  </xsl:template>     
  <!-- siteindexTable template -->
  <xsl:template name="siteindexTable">
		<div class="forumbg">
			<div class="inner"><span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt>{L_SITEMAP_URL}</dt>
						<dd class="lastpost"><span>{L_LASTMOD_DATE}</span></dd>
					</dl>
				</li>
			</ul>
      <xsl:apply-templates select="sitemap:sitemapindex/sitemap:sitemap"></xsl:apply-templates>  
			<span class="corners-bottom"><span></span></span></div>
		</div>       
  </xsl:template>  
  <!-- sitemapTable template -->  
  <xsl:template name="sitemapTable">
		<div class="forumbg">
			<div class="inner"><span class="corners-top"><span></span></span>
			<ul class="topiclist">
				<li class="header">
					<dl class="icon">
						<dt>{L_LINK}</dt>
						<dd class="topics">{L_PRIORITY}</dd>
						<dd class="posts">{L_CHANGEFREQ}</dd>
						<dd class="lastpost"><span>{L_LASTMOD_DATE}</span></dd>
					</dl>
				</li>
			</ul>
			<xsl:apply-templates select="sitemap:urlset/sitemap:url"></xsl:apply-templates>
			<span class="corners-bottom"><span></span></span></div>
		</div>
  </xsl:template>
  <!-- sitemap:url template -->  
  <xsl:template match="sitemap:url">
		<ul class="topiclist forums">
		<li class="row">
			<dl class="icon" style="background-image: url({T_IMAGE_PATH}forum_read.gif); background-repeat: no-repeat;">
				<dt style="overflow:hidden"><xsl:variable name="sitemapURL"><xsl:value-of select="sitemap:loc"/></xsl:variable>  
					<a href="{$sitemapURL}" class="topictitle"><span>
					<xsl:choose>
						<xsl:when test="$sitemapURL='{ROOT_URL}'">
							<xsl:value-of select="substring-after('{ROOT_URL}', 'http://')"></xsl:value-of>
						</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="contains($sitemapURL,'{ROOT_URL}')">
									<xsl:value-of select="substring-after($sitemapURL, '{ROOT_URL}')"></xsl:value-of>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="$sitemapURL"></xsl:value-of>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
					</xsl:choose>
					</span></a>
				</dt>
				<dd class="topics"><span><xsl:value-of select="sitemap:priority"/></span></dd>
				<dd class="posts"><span><xsl:value-of select="sitemap:changefreq"/></span></dd>
				<dd class="lastpost"><span><xsl:value-of select="sitemap:lastmod" /></span></dd>
			</dl>
		</li>
		</ul>
  </xsl:template>
  <!-- sitemap:sitemap template -->
  <xsl:template match="sitemap:sitemap">
		<ul class="topiclist forums">
		<li class="row">
			<dl class="icon" style="background-image: url({T_IMAGE_PATH}topic_read.gif); background-repeat: no-repeat;">
				<dt style="overflow:hidden"><xsl:variable name="sitemapURL"><xsl:value-of select="sitemap:loc"/></xsl:variable>  
					<a href="{$sitemapURL}" class="forumtitle"><span>
							<xsl:choose><xsl:when test="$sitemapURL='{ROOT_URL}'"><xsl:value-of select="substring-after('{ROOT_URL}', 'http://')"></xsl:value-of></xsl:when>
								<xsl:otherwise><xsl:value-of select="substring-after($sitemapURL, '{ROOT_URL}')"></xsl:value-of></xsl:otherwise>
  							</xsl:choose>
							</span></a></dt>
				<dd class="lastpost"><span><xsl:value-of select="sitemap:lastmod" /></span></dd>
			</dl>
		</li>
		</ul>
  </xsl:template>
</xsl:stylesheet>
