#
#   XML-RPC Server for Program E
#	Copyright 2002, Paul Rydell
#	
#	This file is part of Program E.
#	
#	Program E is free software; you can redistribute it and/or modify
#   it under the terms of the GNU General Public License as published by
#   the Free Software Foundation; either version 2 of the License, or
#   (at your option) any later version.
#
#   Program E is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with Program E; if not, write to the Free Software
#    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#

use Frontier::Client;
    
# Make an object to represent the XML-RPC server.
$server_url = 'http://www.example.com/programe/src/xmlrpcserver.php';

$server = Frontier::Client->new(url => $server_url, debug => 0);

$myinput="Hello my name is mud.";
$myid="UNIQUEID000001";
$botname="MyBot";

# Call the remote server and get our result.
$result = $server->call('programe.getResponse', $server->string($myinput), $server->string($myid), $server->string($botname);
$response = $result->{'response'};
  
print "Response: $response\n\n";
