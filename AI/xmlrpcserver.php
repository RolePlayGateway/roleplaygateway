<?php

/*
    XML-RPC Server for Program E
	Copyright 2002, Chris Jackson
	Modified by Paul Rydell
	
	This file is part of Program E.
	
	Program E is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Program E is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Program E; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/**
 * XML-RPC Server for Program E
 * 
 * XML-RPC Server for Program E
 * @author Chris Jackson
 * @copyright 2002
 * @version 0.0.8
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @package Program_E
 */

include "respond.php";

// a function to ensure the xmlrpc extension is loaded.
// xmlrpc_epi_dir = directory where libxmlrpc.so.0 is located
// xmlrpc_php_dir = directory where xmlrpc-epi-php.so is located
function xu_load_extension($xmlrpc_php_dir="no-debug-non-zts-20001222/xmlrpc") {
   $bSuccess = true;
   putenv("LD_LIBRARY_PATH=/usr/lib/php4/apache/xmlrpc/");
   if ($xmlrpc_php_dir) {
      $xmlrpc_php_dir .= '/';
   }
   if (!extension_loaded("xmlrpc")) {
      $bSuccess = dl($xmlrpc_php_dir . "xmlrpc-epi-php.so");
   }
   return $bSuccess;
}

function xmlrespondepi($method_name, $params, $app_data) {
	$input=$params[0];
	$myid=$params[1];
	$botname=$params[2];
	$response = replybotname(stripslashes($input),$myid,$botname);
	return($response);
}

function xmlrespondul($m) 
{
	global $xmlrpcerruser;
	$err="";

	$input=$m->getParam(0);
	$myid=$m->getParam(1);
	$botname=$m->getParam(2);

	if ((isset($input) && ($input->scalartyp()=="string")) && (isset($myid) && ($myid->scalartyp()=="string")) && && (isset($botname) && ($botname->scalartyp()=="string"))) {
		$response = replybotname($input->scalarval(), $myid->scalarval(), $botname->scalarval());
	} else {
		$err="Two string parameters required";
	}
	if ($err) {
		return new xmlrpcresp(0, $xmlrpcerruser, $err);
	} else {

		$arrpatternsmatched=new xmlrpcval(array(),"array");
		$arrinputs=new xmlrpcval(array(),"array");

		for ($x=0;$x<sizeof($response->inputs);$x++){
		 $ok=$arrinputs->addScalar($response->inputs[$x]);
		}

		for ($x=0;$x<sizeof($response->patternsmatched);$x++){
		 $ok=$arrpatternsmatched->addScalar($response->patternsmatched[$x]);
		}

		return new xmlrpcresp(new xmlrpcval(array(
					"errors" => new xmlrpcval($response->errors),
					"timer" => new xmlrpcval($response->timer, "double"),
					"response" => new xmlrpcval($response->response),
					"patternsmatched" => $arrpatternsmatched,
					"inputs" => $arrinputs
										  ), "struct"));
				

	}
}


// Check if they have xmlrpc-epi or if they will have to use include stuff.
if (xu_load_extension()){

	include "xmlrpc/xmlrpc_utils.php";

	$request_xml = $HTTP_RAW_POST_DATA;

	$server = xmlrpc_server_create();

	xmlrpc_server_register_method($server,'programe.getResponse', 'xmlrespondepi');

	$response = xmlrpc_server_call_method($server, $request_xml,  null);
	xu_server_send_http_response($response);

} else {

	include "xmlrpc/xmlrpc.inc";
	include "xmlrpc/xmlrpcs.inc";

	// build a signature
	$programeresponse_sig=array(array("struct", $xmlrpcString, $xmlrpcString));

	// and the doc
	$programeresponse_doc='When passed a query as a string and a unique ID as a string, Program E returns a response.';

	$server = new xmlrpc_server(array("programe.getResponse" => array("function" => "xmlrespondul", "signature" => $programeresponse_sig, "docstring" => $programeresponse_doc)));

}




?>

