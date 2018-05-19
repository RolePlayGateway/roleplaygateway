<?php
 error_reporting(E_ALL & ~E_NOTICE);
 if (!isset($_SERVER)) $_SERVER = $HTTP_SERVER_VARS;

 if ($_REQUEST['v']) {
  echo '1';
  exit;
 }

 function get_page($host, $url) {
  global $i, $fail_count;
  $handle = fsockopen($host, 80, $errno, $errstr, 30);
  if (!$handle) {
   if ($fail_count < 5) {
    $i--;
    $fail_count++;
   }
  } else {
   if ($_REQUEST['c']) $cookie = "Cookie: $_REQUEST[c]\r\n";
   fwrite ($handle, "GET $url HTTP/1.1\r\nHost: $host\r\nConnection: Close\r\n$cookie\r\n");
   while (!feof ($handle)) {
    $string = fgetc ($handle);
    if ($string == '<') break;
   }
   while (!feof($handle)) {
    $string .= fread($handle, 40960);
   }
   fclose($handle);
   return $string;
  }
 }
 
 function get_google($start) {
  global $i, $fail_count;
  $handle = fsockopen('api.google.com', 80, $errno, $errstr, 30);
  if (!$handle) {
   if ($fail_count < 5) {
    $i--;
    $fail_count++;
   }
  } else {
   $body = "<?xml version='1.0' encoding='UTF-8'?>\n\n" . 
    "<SOAP-ENV:Envelope xmlns:SOAP-ENV=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:xsi=\"http://www.w3.org/1999/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/1999/XMLSchema\">\n" . 
    "<SOAP-ENV:Body>\n" . 
    "<ns1:doGoogleSearch xmlns:ns1=\"urn:GoogleSearch\" \n" . 
    "SOAP-ENV:encodingStyle=\"http://schemas.xmlsoap.org/soap/encoding/\">\n" . 
    "<key xsi:type=\"xsd:string\">" . $_REQUEST['key'] . "</key>\n" . 
    "<q xsi:type=\"xsd:string\">" . str_replace ("&", "&", $_REQUEST['q']) . "</q>\n" . 
    "<start xsi:type=\"xsd:int\">" . ($start - 1) . "</start>\n" . 
    "<maxResults xsi:type=\"xsd:int\">10</maxResults>\n" . 
    "<filter xsi:type=\"xsd:boolean\">true</filter>\n" . 
    "<restrict xsi:type=\"xsd:string\">" . $_REQUEST['country'] . "</restrict>\n" . 
    "<safeSearch xsi:type=\"xsd:boolean\">false</safeSearch>\n" . 
    "<lr xsi:type=\"xsd:string\">" . $_REQUEST['language'] . "</lr>\n" . 
    "<ie xsi:type=\"xsd:string\"></ie>\n" . 
    "<oe xsi:type=\"xsd:string\"></oe>\n" . 
    "</ns1:doGoogleSearch>\n" . 
    "</SOAP-ENV:Body>\n" . 
    "</SOAP-ENV:Envelope>\n\n";
  
   fwrite ($handle, "POST /search/beta2 HTTP/1.0\n");
   fwrite ($handle, "Host: api.google.com\n");
   fwrite ($handle, "Content-Type: text/xml; charset=utf-8\n");
   fwrite ($handle, "SOAPAction: \"urn:GoogleSearchAction\"\n");
   fwrite ($handle, "Content-length: " . strlen($body) . "\n\n");
   fwrite ($handle, $body);

   while (!feof ($handle)) {
    $string = fgetc ($handle);
    if ($string == '<') break;
   }
   while (!feof($handle)) {
    $string .= fread($handle, 40960);
   }
   fclose($handle);
   return $string;
  }
 }

 if ($_REQUEST['t']) {
  $query_order = array(1);
 } else {
  $num = 10;
  if ($_REQUEST['se'] == 'y') $num = 50;
 
  for ($i = 1; $i <= $_REQUEST['d']; $i += $num) {
   $query_order[] = $i;
  }
 
  if ($_REQUEST['l'] > 0 && $_REQUEST['l'] < 1001) {
   $x = $_REQUEST['l'] - 1;
   $y = $x - ($x % $num) + 1;
   $query_order[$y / $num] = 1;
   $query_order[0] = $y;
   $slice = array_slice($query_order, 1, max (0, ($y / $num) - 1));
   rsort ($slice);  
   foreach ($slice as $array_key => $value) {
    $query_order[$array_key + 1] = $value;
   }
  }
 }

 if ($_REQUEST['se'] == 'g') {
  $error = '';
  $fail_count = 0;
  for ($i = 0; $i < count($query_order); $i++) {
   @set_time_limit(30);
   $start = $query_order[$i];
   
   $data = get_google ($start);

   $parser = xml_parser_create('UTF-8');
   xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
   xml_parse_into_struct($parser, $data, $vals, $index); 
   xml_parser_free($parser);
  
  
   if ($index['FAULTSTRING'][0]) {
    $error = $vals[$index['FAULTSTRING'][0]]['value'];
   } elseif (!isset ($vals[$index['ESTIMATEDTOTALRESULTSCOUNT'][0]]['value'])) {
    $error = 'Unknown Google server error';
   } elseif (isset ($vals[$index['ENDINDEX'][0]]['value']) && $vals[$index['ENDINDEX'][0]]['value'] == 0) {
    $error = 'Google gave no results';
   } else {
    if ($_REQUEST['u']) {
     unset ($results_detail);
     $position = $start;
     foreach ($index['URL'] as $array_key => $url_key) {
      if (substr_count ($vals[$url_key]['value'], $_REQUEST['u'])) $results[] = $position;
       
      if ($_REQUEST['s']) {
       $results_detail[$position]['title'] = $vals[$index['TITLE'][$array_key]]['value'];
       $results_detail[$position]['url'] = $vals[$index['URL'][$array_key]]['value'];
      }
      $position++;
     }
    }
   }
   $results_total = $vals[$index['ESTIMATEDTOTALRESULTSCOUNT'][0]]['value'];
   if ($error && $fail_count < 5) {
    unset($error);
    $i--;
    $fail_count++;
   }
   if ($results) break;
  }
  
 } elseif ($_REQUEST['se'] == 'y') {
  $error = '';
  $fail_count = 0;
  for ($i = 0; $i < count($query_order); $i++) {
   @set_time_limit(30);
   $start = $query_order[$i];
   
   $data = get_page ('api.search.yahoo.com', '/WebSearchService/V1/webSearch?appid=keywordtracker&query=' . urlencode ($_REQUEST['q']) . '&start=' . $start . '&results=50');
   
   $parser = xml_parser_create('UTF-8');
   xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
   xml_parse_into_struct($parser, $data, $vals, $index); 
   xml_parser_free($parser);

   if ($index['ERROR']) {
    $error = $vals[$index['MESSAGE'][0]]['value'];
   }
   
   if ($_REQUEST['u']) {
    unset ($results_detail);
    $position = $start;
    foreach ($index['URL'] as $url_key) {
     if ($vals[$url_key]['level'] == 3) {
      if (substr_count ($vals[$url_key]['value'], $_REQUEST['u'])) $results[] = $position;
       
      if ($_REQUEST['s']) {
       $results_detail[$position]['title'] = $vals[$url_key - 2]['value'];
       $results_detail[$position]['summary'] = $vals[$url_key - 1]['value'];
       $results_detail[$position]['url'] = $vals[$url_key]['value'];
      }
      $position++;
     }
    }
   }
   $results_total = $vals[$index['RESULTSET'][0]]['attributes']['TOTALRESULTSAVAILABLE'];
   if ($error && $fail_count < 5) {
    unset($error);
    $i--;
    $fail_count++;
   }
   if ($results) {
     if ($_REQUEST['s']) {
     $position_key = max(0, min ($results[0] - 4, count ($results_detail) - 10));
     $results_detail = array_slice ($results_detail, $position_key, 10);
     foreach ($results_detail as $result) {
      $position_key++;
      $results_new[$position_key] = $result;
     }
     $results_detail = $results_new;
    }
    break;
   }   
  }
  
 } elseif ($_REQUEST['se'] == 'm') {
  $error = '';
  $fail_count = 0;
  for ($i = 0; $i < count($query_order); $i++) {
   @set_time_limit(30);
   $start = $query_order[$i];
  
   $data = get_page ('search.msn.com', '/results.aspx?q=' . urlencode ($_REQUEST['q']) . '&first=' . $start . '&count=10&format=rss');

   $parser = xml_parser_create('UTF-8');
   xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
   xml_parse_into_struct($parser, $data, $vals, $index); 
   xml_parser_free($parser);

   unset ($results_detail);
   $position = $start;
   foreach ($index['LINK'] as $url_key) {
    if ($vals[$url_key]['level'] == 4) {

     if (substr_count ($vals[$url_key]['value'], $_REQUEST['u'])) $results[] = $position;
     
     if ($_REQUEST['s']) {
      $results_detail[$position]['title'] = $vals[$url_key - 1]['value'];
      $results_detail[$position]['summary'] = $vals[$url_key + 1]['value'];
      $results_detail[$position]['url'] = $vals[$url_key]['value'];
     }
     $position++;
    }
   }
   if ($results) break;
   if ($error && $fail_count < 5) {
    unset($error);
    $i--;
    $fail_count++;
   }
  }
 }
 if (!$results) $results[] = 9999;
 $output['results'] = implode ('|', $results);
 
 if ($error) $output['error'] = $error;
 
 if ($_REQUEST['t']) {
  $output['total'] = $results_total;
  echo serialize($output);
 } elseif ($_REQUEST['s']) {
  $output['total'] = $results_total;
  $output['detail'] = $results_detail;
  echo serialize ($output);
 } else {
  echo serialize ($output);
 }
 
?>