<?php
/*
 * @package AJAX_Chat
 * @author Sebastian Tschan
 * @copyright (c) Sebastian Tschan
 * @license GNU Affero General Public License
 * @link https://blueimp.net/ajax/
 */

// Class to initialize the DataBase connection:
class AJAXChatDataBase {

	var $_db;

	function AJAXChatDataBase(&$dbConnectionConfig) {
		switch($dbConnectionConfig['type']) {
			case 'mysqli':
				$this->_db = new AJAXChatDatabaseMySQLi($dbConnectionConfig);
				break;
			case 'mysql':
				$this->_db = new AJAXChatDatabaseMySQL($dbConnectionConfig);
				break;
			default:
				// Use MySQLi if available, else MySQL:
				if(function_exists('mysqli_connect')) {
					$this->_db = new AJAXChatDatabaseMySQLi($dbConnectionConfig);
				} else {
					$this->_db = new AJAXChatDatabaseMySQL($dbConnectionConfig);	
				}
		}
	}
	
	// Method to connect to the DataBase server:
	function connect(&$dbConnectionConfig) {
		return $this->_db->connect($dbConnectionConfig);
	}
	
	// Method to select the DataBase:
	function select($dbName) {
		return $this->_db->select($dbName);
	}
	
	// Method to determine if an error has occured:
	function error() {
		return $this->_db->error();
	}
	
	// Method to return the error report:
	function getError() {
		return $this->_db->getError();
	}
	
	// Method to return the connection identifier:
	function &getConnectionID() {
		return $this->_db->getConnectionID();
	}
	
	// Method to prevent SQL injections:
	function makeSafe($value) {
		return $this->_db->makeSafe($value);
	}

	// Method to perform SQL queries:
	function sqlQuery($sql) {
		$cache = false;
	
		if ($cache === TRUE && (preg_match("/ajax_chat_online/",$sql) != true)) {
		
			// Clean up the query a bit so it can be matched for fingerprinting
			$sql = preg_replace('/[\n\r\s\t]+/', ' ', $sql);
					
			$fingerprint = md5($sql);
			
			$memcache_obj = new Memcache;
			$memcache_obj = memcache_connect("localhost", 11211);
			
			
			if ($memcache_obj->get($fingerprint) == true) {
				$response = $memcache_obj->get($fingerprint);
			} else {
				$response = $this->_db->sqlQuery($sql);

				// the value at the end of this function controls how long cache objects are kept (and as a result (currently) new messages will be delayed by this amount)
				$memcache_obj->set($fingerprint, $response, false, 1);
			}
			
		} else {
		
			$response = $this->_db->sqlQuery($sql);
			
		}
		
		return $response;
	}
	
	// Method to retrieve the current DataBase name:
	function getName() {
		return $this->_db->getName();
	}

	// Method to retrieve the last inserted ID:
	function getLastInsertedID() {
		return $this->_db->getLastInsertedID();
	}

}
?>