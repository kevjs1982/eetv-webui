<?
class RemoteFiles
{
        function get($url)
        {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
                return $output;
        }

        function getBasic($userpass,$url)
        {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERPWD, "$userpass");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
                return $output;
        }

        /*
        * Make a request to a remote server and save the requestedURI to a file on disk
        * @authour Kev Swindells
        * @modified 2011-02-18
        * @version 1.0.3
        */
        function toFile($url,$file,$debug=false,$user_agent = false)
        {
                if ($debug) echo "File : $file ; URL = $url ;";
        $ch = curl_init();
                $fp = fopen($file, 'w');
                if ($debug) echo curl_setopt($ch, CURLOPT_VERBOSE, true); // Display communication with server
                if ($debug) echo curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return data instead of display to std out
                if ($user_agent != false)
                {
                        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
                }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $output = curl_exec($ch);
                if ($debug)
                {
                        echo "<pre>";
                        print_r(curl_getinfo($ch));
                        echo "</pre>";
                }
                curl_close($ch);
                fclose($fp);
                return $output;
        }
}
?>