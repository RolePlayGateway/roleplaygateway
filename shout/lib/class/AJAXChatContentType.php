<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) 2007 Sebastian Tschan
 * @license http://creativecommons.org/licenses/by-sa/
 * @link https://blueimp.net/ajax/
 */

// Class to deliver the content-type
class AJAXChatContentType {

	var $_contentType = '';
	var $_constant = false;

	// Constructor:
	function AJAXChatContentType($encoding, $contentType) {
		if (isset($contentType)) {
			$this->_contentType = $contentType.'; charset='.$encoding;
			$this->_constant = true;
		} else if (isset($_SERVER['HTTP_ACCEPT']) && stristr($_SERVER['HTTP_ACCEPT'],'application/xhtml+xml')) {
			$this->_contentType = 'application/xhtml+xml; charset='.$encoding;
		} else {
 			$this->_contentType = 'text/html; charset='.$encoding;
		}
	}

	// Sends the content-type-header:
	function send() {
		// Send the content-type-header:
		header('Content-Type: '.$this->_contentType);
		// Send message that accept-header determines the delivered content (important for proxy-caches):
		if(!$this->_constant)
			header('Vary: Accept');
	}
    
	// Returns the content-type-header:
	function get() {
		// Return the content-type-header:
		return $this->_contentType;
	}

}
?>