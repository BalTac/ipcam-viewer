<?php
/**
 * Check if a client IP is in our Server subnet
 *
 * @param string $client_ip
 * @param string $server_ip
 * @return boolean
 */
function clientInSameSubnet($client_ip,$server_ip) {
    //if (!$client_ip)
    //    $client_ip = $_SERVER['REMOTE_ADDR'];
    //if (!$server_ip)
    //    $server_ip = $_SERVER['SERVER_ADDR'];
    // Extract broadcast and netmask from ifconfig
    if (!($p = popen("ifconfig 2>&1","r"))) return false;
    $out = "";
    while($s=fgets($p,1024))
        $out .= fread($s);
    fclose($p);
    // This is because the php.net comment function does not
    // allow long lines.
    $match  = "/^.*".$server_ip;
    $match .= ".*Bcast:(\d{1,3}\.\d{1,3}i\.\d{1,3}\.\d{1,3}).*";
    $match .= "Mask:(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$/im";
    if (!preg_match($match,$out,$regs))
        return false;
    $bcast = ip2long($regs[1]);
    $smask = ip2long($regs[2]);
    $ipadr = ip2long($client_ip);
    $nmask = $bcast & $smask;
	print_r($client_ip." ".$server_ip); //." ".$bcast." ".$smask." ".$ipadr." ".$nmask;
    return (($ipadr & $smask) == ($nmask & $smask));
}
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
	function extip() {
    // fetching my public ip 
	ini_set('display_errors', 0);
	$count = 0;
	do {
		$count++;
		$ec = file_get_contents("https://api.ipify.org"); 
		if (!$ec) {
			$public_ip = "";
		} else {
			$public_ip = $ec;
		}
	} while ((!$public_ip) && ($count <= 10));
	if (!$public_ip) {
		$public_ip = "localhost";
	}	
	// print_r("[".$count."] - ".$public_ip);
	return $public_ip;
	ini_set('display_errors', 1);
	}
?>