<?php

/*
    XML-RPC Client for Program E
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

include("xmlrpc.inc");

?>
<html>
<head><title>xmlrpc client sample</title></head>
<body>
<?php


if ($HTTP_POST_VARS["input"]!="") {
  $f=new xmlrpcmsg('programe.getResponse', array(new xmlrpcval($HTTP_POST_VARS["input"], "string"),
                                              new xmlrpcval($HTTP_POST_VARS["myid"], "string"),
											  new xmlrpcval($HTTP_POST_VARS["botname"], "string")
                                             )
                  );

//  print "<pre>" . htmlentities($f->serialize()) . "</pre>\n";
  $c=new xmlrpc_client("/dev/programe/src/xmlrpcserver.php", "www.rydell.com", 80);
  $c->setDebug(0);
  $r=$c->send($f);
  if (!$r) { die("send failed"); }
  $v=$r->value();
  $theresponse = $v->structmem('response');

  if (!$r->faultCode()) {
	print "Program E responds to:<br>". $HTTP_POST_VARS["input"] . "<br>with:<br>" . $theresponse->scalarval() . "<BR>";
  } else {
	print "Fault: ";
	print "Code: " . $r->faultCode() . 
	  " Reason '" .$r->faultString()."'<BR>";
  }
}
print "<FORM ACTION=\"phpclient.php\" METHOD=\"POST\">
Say something to Program E:
<input type='hidden' name='myid' value='test123'>
<input type='hidden' name='botname' value='MyBot'>
<INPUT NAME=\"input\" VALUE=\"${input}\"><input type=\"submit\" value=\"go\" name=\"submit\"></FORM><P>";

?>
</body>
</html>
