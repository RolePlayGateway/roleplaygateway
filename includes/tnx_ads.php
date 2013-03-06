<?php
//<div style="font-size: 16px;">If you see this, your server does not support PHP. Ask your web-hosting provider where you can add the PHP code on your website.</div>
/*ERROR<!--*/
class TNX_l
{
        var $_login = 'Martindale'; // your TNX login
        var $_timeout_connect = 5; // timeout - maximum time for the links to load (in seconds)
        var $_connect_using = 'fsock'; // curl or fsock - you may choose the way to connect
        var $_html_delimiter = '<br>'; // link devider - symbol between links (you may choose any symbol)
        var $_encoding = ''; // your website encoding. You may also use: KOI8-U, UTF-8, iso-8859-1 (iconv module must be installed on your server)
        var $_exceptions = 'PHPSESSID'; // here you can list URL's that you don't want to be indexed by TNX. Basically, those are URLs that are not indexed by search engines, or links to pages that don't exist. Don't change this value after it has been indexed by TNX system.
        /*******************************/
        var $_return_point = 0;
        var $_content = '';

        function TNX_l()
        {
                if($this->_connect_using == 'fsock' AND !function_exists('fsockopen')){echo 'fsock function is disabled on your server, contact your provider or try to use the CURL version of the TNX code.'; return false;}
                if($this->_connect_using == 'curl' AND !function_exists('curl_init')){echo 'Error, CURL is not supported, try using fsock.'; return false;}
                if(!empty($this->_encoding) AND !function_exists("iconv")){echo 'CURL function is disabled on your server, contact your provider or try to use the fsock version of the TNX code.'; return false;}

                if ($_SERVER['REQUEST_URI'] == '') $_SERVER['REQUEST_URI'] = '/';
                if (strlen($_SERVER['REQUEST_URI']) > 180) return false;

                if(!empty($this->_exceptions))
                {
                        $exceptions = explode(' ', $this->_exceptions);
                        for ($i=0; $i<sizeof($exceptions); $i++)
                        {
                                if($_SERVER['REQUEST_URI'] == $exceptions[$i]) return false;
                                if($exceptions[$i] == '/' AND preg_match("#^\/index\.\w{1,5}$#", $_SERVER['REQUEST_URI'])) return false;
                                if(strpos($_SERVER['REQUEST_URI'], $exceptions[$i]) !== false) return false;
                        }
                }

                $this->_login = strtolower($this->_login); $this->_host = $this->_login . '.tnx.net'; $file = base64_encode($_SERVER['REQUEST_URI']);
                $user_pref = substr($this->_login, 0, 2); $md5 = md5($file); $index = substr($md5, 0, 2);
                $site = str_replace('www.', '', $_SERVER['HTTP_HOST']);
                $this->_path = '/users/' . $user_pref . '/' . $this->_login . '/' . $site. '/' . substr($md5, 0, 1) . '/' . substr($md5, 1, 2) . '/' . $file . '.txt';
                $this->_url = 'http://' . $this->_host . $this->_path;
                $this->_content = $this->get_content();
                if($this->_content !== false)
                {
                        $this->_content_array = explode('<br>', $this->_content);
                        for ($i=0; $i<sizeof($this->_content_array); $i++)
                        {
                                $this->_content_array[$i] = trim($this->_content_array[$i]);
                        }
                }
        }
        /*!!!*/
        function show_link($num = false)
        {
                if(!isset($this->_content_array)) return false;
                $links = '';
                if(!isset($this->_content_array_count)){$this->_content_array_count = sizeof($this->_content_array);}
                if($this->_return_point >= $this->_content_array_count) return false;

                if($num === false OR $num >= $this->_content_array_count)
                {
                        for ($i = $this->_return_point; $i < $this->_content_array_count; $i++)
                        {
                                $links .= $this->_content_array[$i] . $this->_html_delimiter;
                        }
                        $this->_return_point += $this->_content_array_count;
                }
                else
                {
                        if($this->_return_point + $num > $this->_content_array_count) return false;
                        for ($i = $this->_return_point; $i < $num + $this->_return_point; $i++)
                        {
                                $links .= $this->_content_array[$i] . $this->_html_delimiter;
                        }
                        $this->_return_point += $num;
                }
                return (!empty($this->_encoding)) ? iconv("windows-1251", $this->_encoding, $links) : $links;
        }
        function get_content()
        {
                $user_agent = 'TNX_l ip: ' . $_SERVER['REMOTE_ADDR'];
                $page = '';
                if ($this->_connect_using == 'curl' OR ($this->_connect_using == '' AND function_exists('curl_init')))
                {
                        $c = curl_init($this->_url);
                        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $this->_timeout_connect);
                        curl_setopt($c, CURLOPT_HEADER, false);
                        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($c, CURLOPT_TIMEOUT, $this->_timeout_connect);
                        curl_setopt($c, CURLOPT_USERAGENT, $user_agent);
                        $page = curl_exec($c);
                        if(curl_error($c) OR (curl_getinfo($c, CURLINFO_HTTP_CODE) != '200' AND curl_getinfo($c, CURLINFO_HTTP_CODE) != '404') OR strpos($page, 'fsockopen') !== false)
                        {
                                curl_close($c);
                                return false;
                        }
                        curl_close($c);
                }
                elseif($this->_connect_using == 'fsock')
                {
                        $buff = '';
                        $fp = @fsockopen($this->_host, 80, $errno, $errstr, $this->_timeout_connect);
                        if ($fp)
                        {
                                fputs($fp, "GET " . $this->_path . " HTTP/1.0\r\n");
                                fputs($fp, "Host: " . $this->_host . "\r\n");
                                fputs($fp, "User-Agent: " . $user_agent . "\r\n");
                                fputs($fp, "Connection: Close\r\n\r\n");

                                stream_set_blocking($fp, true);
                                stream_set_timeout($fp, $this->_timeout_connect);
                                $info = stream_get_meta_data($fp);

                                while ((!feof($fp)) AND (!$info['timed_out']))
                                {
                                        $buff .= fgets($fp, 4096);
                                        $info = stream_get_meta_data($fp);
                                }
                                fclose($fp);

                                if ($info['timed_out']) return false;

                                $page = explode("\r\n\r\n", $buff);
                                $page = $page[1];
                                if((!preg_match("#^HTTP/1\.\d 200$#", substr($buff, 0, 12)) AND !preg_match("#^HTTP/1\.\d 404$#", substr($buff, 0, 12))) OR $errno!=0 OR strpos($page, 'fsockopen') !== false) return false;
                        }
                }
                if(strpos($page, '404 Not Found')) return '';
                return $page;
        }
}
/*-->*/
?>