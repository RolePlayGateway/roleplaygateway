<?php

/**
 * Library for easy communication with MailGun REST API. 
 *
 * @package Mailgun
 * @author  MailGun Inc
 * @version 1.0
*/

/**
* Mailgun uses patched ActiveResource.php. 
* Modifications:
* 1) Throw error in case when server returns not 2xx response.
* 2) Check content-type and parse only text/xml response
*/
require_once 'ActiveResource.php';

/** 
 * Initialize the ibrary
 *
 * @param string $api_key   Your API key 
 * @param string $api_url   API base URL. Must end with backslash. Default is http://maligunhq.com/api/
 */
function mailgun_init($api_key, $api_url = "https://mailgun.net/api/") {
    global $_mailgun_api_url, $_maigun_api_key;

    if ($api_url[strlen($api_url)-1] != "/")
        $api_url .= '/';

    $_mailgun_api_url = $api_url;
    $_maigun_api_key = $api_key;
}

/**
* MailgunMessage class: sends messages via Mailgun HTTP gateway
*
* @package Mailgun
*/
define("MAILGUN_TAG", "X-Mailgun-Tag");

class MailgunMessage {

    /**
    * Send plain-text message
    *
    * @param string $sender      sender specification 
    * @param string $recipients  comma- or semicolon-separated list of recipients specifications.
    * @param string $subject     message subject
    * @param string $text        message text
    * @param string $servername  sending server (can be empty, use 'best' server)
    * @param string $options     JSON dictionary with objects, array("headers" => array(MAILGUN_TAG => "bounce"))
    */
    static function send_text($sender, $recipients, $subject, $text, $servername="", $options = NULL) {
        $curl = _mailgun_init_curl("messages.txt");

        $params =  'sender='.urlencode($sender).'&recipients='.urlencode($recipients);
        $params .= '&subject='.urlencode($subject).'&servername='.$servername;
        $params .= '&body='.urlencode($text);
        if($options != NULL) {
            $params .= '&options='.urlencode(json_encode($options));
        }

        curl_setopt($curl, CURLOPT_POST, true); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params); 
        curl_setopt($curl, CURLOPT_HEADER, false); 

        _mailgun_exec_curl($curl, "Send text message failed");
    }

    /**
    * Send MIME-formatted message, as it is
    * 
    * @param string $sender		sender specification. Mailgun will not add it to message "From" header.		
    * @param string $recipients	comma- or semicolon-separated list of recipients specifications.
    * @param string $raw_body	valid MIME message.
    */
    static function send_raw($sender, $recipients, $raw_body, $servername="") {
        if ($servername)
            $curl = _mailgun_init_curl("messages.eml?servername=".$servername);
        else
            $curl = _mailgun_init_curl("messages.eml");

        $params = '&servername='.urlencode($servername);
        $req =  $sender."\n".$recipients."\n\n".$raw_body;
        
        curl_setopt($curl, CURLOPT_POST, true); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $req); 
        curl_setopt($curl, CURLOPT_HEADER, false); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/plain"));
        _mailgun_exec_curl($curl, "Send raw message failed");
    } 

}

/**
*
* Base class for Mailgun Resources
*
* @package Mailgun
* @author  MailGun Inc.
*/
class MailgunResource extends ActiveResource {
    var $user = 'api_key';
    var $site = 'http://mailgun.net/api/';
    var $password = 'my-api-key';
    var $request_format = 'xml';
       
    function __construct($data = array ()) {
        global $_mailgun_api_url, $_maigun_api_key; 

        parent::__construct($data);
        $this->site     = $_mailgun_api_url;
        $this->password = $_maigun_api_key;    
    }
    
    /**
    Get ID of REST resource. Same as $resource->id
    */
    function id() {
        return $this->id;
    }

    /**
    * Create new resource or update it if resource already exist.
    * 
    * There are 2 differences between upsert() and save(). 
    * Upsert does not throw exception if object already exist. 
    * Upsert does not load id of the object.
    *
    * It ensures that resource exists on the server and does not syncronize client object instance.
    * In order to modify "upserted" object, you need to find() it first.
    * Example: <br>
    * <code>
    * Route route = new Route('*@example.com', 'http://example.com/reply');
    * route->upsert();
    * </code>
    */
    function upsert() {
        $this->post("upsert", $this->_data);
    }
}

/**
* A Route has 2 properties: pattern and destination. 
*
* There are 4 types of patterns: 
* 
* 1 '*' - match all 
* 2 exact string match (foo@bar.com)
* 3 a domain pattern, i.e. a string like "*@example.com" - matches all emails going to example.com
* 4 a regular expression
*
* Destination can be: <br>
* 1 An email address. 
* 2 HTTP/HTTPS URL. A message will be HTTP POSTed there.
*
* @package Mailgun
*/
class Route extends MailgunResource {

    /**
     *
     * @param string pattern	The pattern for matching the recipient
     *
    */
    function __construct($pattern = "", $destination = "") {
        parent::__construct(array('pattern' => $pattern, 'destination' => $destination));
    }

    /**
    Get route pattern. Same as $route->pattern
    */       
    function pattern() {
        return $this->pattern;
    }
    
    /**
    Get route destination. Same as $route->destination
    */       
    function detination() {
        return $this->destination;
    }       
}

/**
*   All mail arriving to email addresses that have mailboxes associated
*   will be stored on the server and can be later accessed via IMAP or POP3
*   protocols.
*   
*   Mailbox has several properties:
*
*   alex@gmail.com
*    ^      ^
*    |      |
*   user    domain
*
*   and a password
*
*   user and domain can not be changed for an existing mailbox.
* @package Mailgun
*/
class Mailbox extends MailgunResource {

    /**
     *
     * @param string user 
     * @param string domain
     * @param string password
     *
    */
    function __construct($user = "", $domain = "", $password = "") {
        parent::__construct(array('user' => $user, 'domain' => $domain, 'password' => $password));
    }

    /**
    * Upsert mailboxes contained in a csv string,        
    * @param string $mailboxes  CSV like string with mailboxes, like that: 
    *                           john@domain.com, password
    *                           doe@domain.com, password2
    */
    static function upsert_from_csv($mailboxes) {
        $curl = _mailgun_init_curl("mailboxes.txt");
        curl_setopt($curl, CURLOPT_POST, true); 
        curl_setopt($curl, CURLOPT_POSTFIELDS, $mailboxes); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/plain"));
        _mailgun_exec_curl($curl, "Upsert from csv failed");
    }
}

// Init curl with common parameters 
function _mailgun_init_curl($suffix){
    global $_mailgun_api_url, $_maigun_api_key;
    $ch = curl_init ();
    curl_setopt ($ch, CURLOPT_URL, $_mailgun_api_url."".$suffix);
    curl_setopt ($ch, CURLOPT_MAXREDIRS, 3);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_VERBOSE, 0);
    curl_setopt ($ch, CURLOPT_HEADER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt ($ch, CURLOPT_USERPWD, "api:" . $_maigun_api_key);
    return $ch;
}

// Executes and close cURL session, return server response.
// Throw error if response is not 2xx.
// Error descrition will include server response.
//
// cURL must NOT return headers, or they will be added to error descrition!
//
function _mailgun_exec_curl($curl, $errmsg) {
    $res = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($http_code < 200 || 299 < $http_code) {
        throw new Exception($errmsg.". Server says: ".strip_tags($res));
    }
    return $res;
}

$_mailgun_api_url = 'https://mailgun.net/api/';
$_maigun_api_key = 'api-key-dirty-secret';

// error_reporting(E_ALL);

?>
