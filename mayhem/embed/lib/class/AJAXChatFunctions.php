<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) 2007 Sebastian Tschan
 * @license http://creativecommons.org/licenses/by-sa/
 * @link https://blueimp.net/ajax/
 */

class AJAXChatFunctions {
	
	// Function to display alternating table row colors:
	function alternateRow($rowOdd='rowOdd', $rowEven='rowEven') {
		static $i;
		$i += 1;
		if($i % 2 == 0)
			return $rowEven;
		else
			return $rowOdd;
	}
	
}
?>