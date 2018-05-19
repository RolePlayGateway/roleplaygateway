<?php
/**
*
* @package phpBB SEO Dynamic Meta tags
* @version $Id: phpbb_seo_meta.php 222 2010-02-27 13:08:48Z dcz $
* @copyright (c) 2006 - 2010 www.phpbb-seo.com
* @license http://opensource.org/licenses/gpl-license.php GNU Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB')) {
	exit;
}
/**
* seo_meta Class
* www.phpBB-SEO.com
* @package phpBB SEO Dynamic Meta tags
*/
class seo_meta {
	var $meta = array('title' => '', 'description' => '', 'keywords' => '', 'lang' => '', 'category' => '', 'robots' => '', 'distribution' => '', 'resource-type' => '', 'copyright' => '');
	var $meta_def = array();
	var $filters = array('description' => 'meta_filter_txt', 'keywords' => 'make_keywords');
	// here you can comment a tag line to deactivate it
	var $tpl = array(
		'lang' => '<meta name="content-language" content="%s" />',
		'title' => '<meta name="title" content="%s" />',
		'description' => '<meta name="description" content="%s" />',
		'keywords' => '<meta name="keywords" content="%s" />',
		'category' => '<meta name="category" content="%s" />',
		'robots' => '<meta name="robots" content="%s" />',
		'distribution' => '<meta name="distribution" content="%s" />',
		'resource-type' => '<meta name="resource-type" content="%s" />',
		'copyright' => '<meta name="copyright" content="%s" />',
	);
	/**
	* Some config :
	*	=> keywordlimit : number of keywords (max) in the keyword tag,
	*	=> wordlimit : number of words (max) in the desc tag,
	*	=> wordminlen : only words with more than wordminlen letters will be used, default is 2,
	*	=> bbcodestrip : | separated list of bbcode to fully delete, tag + content, default is 'img|url|flash',
	*	=> ellipsis : ellipsis to use if clipping,
	*	=> topic_sql : Do a SQL to build topic meta keywords or just use the meta desc tag,
	*	=> check_ignore : Check the search_ignore_words.php list.
	*		Please note :
	*			This will require some more work for the server.
	*			And this is mostly useless if you have re-enabled the search_ignore_words.php list
	*			filtering in includes/search/fulltest_native.php (and of course use fulltest_native index).
	*	=> bypass_common : Bypass common words in viewtopic.php.
	*		Set to true by default because the most interesting keywords are as well among the most common.
	*		This of course provides with even better results when fulltest_native is used
	*		and search_ignore_words.php list was re-enabled.
	*	=> get_filter : Disallow tag based on GET var used : coma separated list, will through a disallow meta tag.
	*	=> file_filter : Disallow tag based on the physical script file name : coma separated list of file names
	* Some default values are set bellow in the seo_meta_tags() method,
	* most are acp configurable when using the Ultimate SEO URL mod :
	* => http://www.phpbb-seo.com/en/phpbb-mod-rewrite/ultimate-seo-url-t4608.html (en)
	* => http://www.phpbb-seo.com/fr/mod-rewrite-phpbb/ultimate-seo-url-t4489.html (fr)
	**/
	var $mconfig = array('keywordlimit' => 15, 'wordlimit' => 25, 'wordminlen' => 2, 'bbcodestrip' => 'img|url|flash|code', 'ellipsis' => ' ...', 'topic_sql' => true, 'check_ignore' => false, 'bypass_common' => true,
		// Consider adding ", 'p' => 1" if your forum is no indexed yet or if no post urls are to be redirected
		// to add a noindex tag on post urls
		'get_filter' => 'style,hilit,sid',
		// noindex based on physical script file name
		'file_filter' => 'ucp',
	);
	/**
	* constructor : Initialize meta tags
	* All values from here will pass through utf8_htmlspecialchars() later
	*/
	function seo_meta() {
		global $config;
		// default values, leave empty to only output the corresponding tag if filled
		$this->meta_def['robots'] = 'index,follow';
		// global values, if these are empty, the corresponding meta will not show up
		$this->meta['category'] = 'general';
		$this->meta['distribution'] = 'global';
		$this->meta['resource-type'] = 'document';
		// other settings that may be set through acp in cas the mod is not used standalone
		if (isset($config['seo_meta_desc_limit'])) {
			// defaults
			$this->meta_def['title'] = $config['seo_meta_title'];
			$this->meta_def['description'] = $config['seo_meta_desc'];
			$this->meta_def['keywords'] = $config['seo_meta_keywords'];
			$this->meta_def['robots'] = $config['seo_meta_robots'];
			// global
			$this->meta['lang'] = $config['seo_meta_lang'];
			$this->meta['copyright'] = $config['seo_meta_copy'];
			// settings
			$this->mconfig['wordlimit'] = (int) $config['seo_meta_desc_limit'];
			$this->mconfig['keywordlimit'] = (int) $config['seo_meta_keywords_limit'];
			$this->mconfig['wordminlen'] = (int) $config['seo_meta_min_len'];
			$this->mconfig['check_ignore'] = (int) $config['seo_meta_check_ignore'];
			$this->mconfig['file_filter'] = preg_replace('`[\s]+`', '', trim($config['seo_meta_file_filter'], ', '));
			$this->mconfig['get_filter'] = preg_replace('`[\s]+`', '', trim($config['seo_meta_get_filter'], ', '));
			$this->mconfig['bbcodestrip'] = str_replace(',', '|', preg_replace('`[\s]+`', '', trim($config['seo_meta_bbcode_filter'], ', ')));
		} else {
			// default values, leave empty to only output the corresponding tag if filled
			$this->meta_def['title'] = $config['sitename'];
			$this->meta_def['description'] = $config['site_desc'];
			$this->meta_def['keywords'] = $config['site_desc'];
			// global values, if these are empty, the corresponding meta will not show up
			$this->meta['lang'] = $config['default_lang'];
			$this->meta['copyright'] = $config['sitename'];
		}
		$this->mconfig['get_filter'] = !empty($this->mconfig['get_filter']) ? @explode(',', $this->mconfig['get_filter']) : array();
		$this->mconfig['topic_sql'] = $config['search_type'] == 'fulltext_native' ? $this->mconfig['topic_sql'] : false;
		return;
	}
	/**
	* assign / retrun meta tag code
	*/
	function build_meta( $page_title = '', $return = false) {
		global $phpEx, $user, $phpbb_seo, $template, $config;
		// If meta robots was not manually set
		if (empty($this->meta['robots'])) {
			// If url Rewriting is on, we shall be more strict on noindex (since we can :p)
			if (!empty($phpbb_seo->seo_opt['url_rewrite'])) {
				// If url Rewriting is on, we can deny indexing for any rewritten url with ?
				if (preg_match('`(\.html?|/)\?[^\?]*$`i', $phpbb_seo->seo_path['uri'])) {
					$this->meta['robots'] = 'noindex,follow';
				} else {
					// lets still add some more specific ones
					$this->mconfig['get_filter'] = array_merge($this->mconfig['get_filter'], array('st','sk','sd','ch'));
				}
			}
			// Do we allow indexing based on physical script file name
			if (empty($this->meta['robots'])) {
				if (strpos($this->mconfig['file_filter'], str_replace(".$phpEx", '', $user->page['page_name'])) !== false) {
					$this->meta['robots'] = 'noindex,follow';
				}
			}
			// Do we allow indexing based on get variable
			if (empty($this->meta['robots'])) {
				foreach ( $this->mconfig['get_filter'] as $get ) {
					if (isset($_GET[$get])) {
						$this->meta['robots'] = 'noindex,follow';
						break;
					}
				}
			}
			// fallback to default if necessary
			if (empty($this->meta['robots'])) {
				$this->meta['robots'] = $this->meta_def['robots'];
			}
		}
		if (!empty($config['seo_meta_noarchive'])) {
			$forum_id = isset($_GET['f']) ? max(0, (int) request_var('f', 0)) : 0;
			if ($forum_id) {
				$forum_ids = @explode(',', preg_replace('`[\s]+`', '', trim($config['seo_meta_noarchive'], ', ')));
				if (in_array($forum_id, $forum_ids)) {
					$this->meta['robots'] .= (!empty($this->meta['robots']) ? ',' : '') . 'noarchive';
				}
			}
		}
		// deal with titles, assign the tag if a default is set
		if (empty($this->meta['title']) && !empty($this->meta_def['title'])) {
			$this->meta['title'] = $page_title;
		}
		$meta_code = '';
		foreach ($this->tpl as $key => $value) {
			if (isset($this->meta[$key])) {
				// do like this so we can deactivate one particular tag on a given page,
				// by just setting the meta to an empty string
				if (trim($this->meta[$key])) {
					$this->meta[$key] = isset($this->filters[$key]) ? $this->{$this->filters[$key]}($this->meta[$key]) : $this->meta[$key];
			       }
			} else if (!empty($this->meta_def[$key])) {
				$this->meta[$key] = isset($this->filters[$key]) ? $this->{$this->filters[$key]}($this->meta_def[$key]) : $this->meta_def[$key];
			}
			if (trim($this->meta[$key])) {
				$meta_code .= sprintf($value, utf8_htmlspecialchars($this->meta[$key])) . "\n";
			}
		}
		if (!$return) {
			$template->assign_var('META_TAG', $meta_code);
		} else {
			return $meta_code;
		}
	}
	/**
	* Returns a coma separated keyword list
	*/
	function make_keywords($text, $decode_entities = false) {
		static $filter = array('`&(amp;)?[^;]+;`i', '`[[:punct:]]+`', '`[0-9]+`',  '`[\s]+`');
		$keywords = '';
		$num = 0;
		$text = $decode_entities ? html_entity_decode(strip_tags($text), ENT_COMPAT, 'UTF-8') : strip_tags($text);
		$text = utf8_strtolower(trim(preg_replace($filter, ' ', $text)));
		if (!$text) {
			return '';
		}
		$text = explode(' ', trim($text));
		if ($this->mconfig['check_ignore']) {
			global $phpbb_root_path, $user, $phpEx;
			// add stop words to $user to allow reuse
			if (empty($user->stop_words)) {
				$words = array();
				if (file_exists("{$user->lang_path}{$user->lang_name}/search_ignore_words.$phpEx")) {
					// include the file containing ignore words
					include("{$user->lang_path}{$user->lang_name}/search_ignore_words.$phpEx");
				}
				$user->stop_words = & $words;
			}
			$text = array_diff($text, $user->stop_words);
		}
		if (empty($text)) {
			return '';
		}
		// We take the most used words first
		$text = array_count_values($text);
		arsort($text);
		foreach ($text as $word => $count) {
			if ( utf8_strlen($word) > $this->mconfig['wordminlen'] ) {
				$keywords .= ', ' . $word;
				$num++;
				if ( $num >= $this->mconfig['keywordlimit'] ) {
					break;
				}
			}
		}
		return trim($keywords, ', ');
	}
	/**
	* Filter php/html tags and white spaces and string with limit in words
	*/
	function meta_filter_txt($text, $bbcode = true) {
		if ($bbcode) {
			static $RegEx = array();
			static $replace = array();
			if (empty($RegEx)) {
				$RegEx = array('`&(amp;)?[^;]+;`i', // HTML entitites
					'`<[^>]*>(.*<[^>]*>)?`Usi', // HTML code
				);
				$replace = array(' ', ' ');
				if (!empty($this->mconfig['bbcodestrip'])) {
					$RegEx[] = '`\[(' . $this->mconfig['bbcodestrip'] . ')[^\[\]]*\].*\[/\1[^\[\]]*\]`Usi'; // bbcode to strip
					$replace[] = ' ';
				}
				$RegEx[] = '`\[\/?[^\]\[]*\]`Ui'; // Strip all bbcode tags
				$replace[] = '';
				$RegEx[] = '`[\s]+`'; // Multiple spaces
				$replace[] = ' ';
			}
			return $this->word_limit(preg_replace($RegEx, $replace, $text));
		}
		return $this->word_limit(preg_replace(array('`<[^>]*>(.*<[^>]*>)?`Usi', '`\[\/?[^\]\[]*\]`Ui', '`[\s]+`'), ' ', $text));
	}
	/**
	* Cut the text according to the number of words.
	* Borrowed from www.php.net http://www.php.net/preg_replace
	*/
	function word_limit($string) {
		return count($words = preg_split('/\s+/', ltrim($string), $this->mconfig['wordlimit'] + 1)) > $this->mconfig['wordlimit'] ? rtrim(utf8_substr($string, 0, utf8_strlen($string) - utf8_strlen(end($words)))) . $this->mconfig['ellipsis'] : $string;
	}
	/**
	* add meta tag
	* $content : if empty, the called tag will show up
	* do not call to fall back to default
	*/
	function collect($type, $content = '', $combine = false) {
		if ($combine) {
			$this->meta[$type] = (isset($this->meta[$type]) ? $this->meta[$type] . ' ' : '') . (string) $content;
		} else {
			$this->meta[$type] = (string) $content;
		}
	}
}
?>