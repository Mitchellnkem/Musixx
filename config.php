<?php
date_default_timezone_set('UTC');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once('initialize.php');
require_once('classes/DBConnection.php');
require_once('classes/SystemSettings.php');
$db = new DBConnection;
$conn = $db->conn;
function redirect($url=''){
	if(!empty($url)){
		$target = base_url . ltrim((string) $url, '/');
		if (!headers_sent()) {
			header('Location: ' . $target, true, 302);
		} else {
			echo '<script>window.location.replace(' . json_encode($target, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ');</script>';
		}
		exit;
	}
}
function validate_image($file){
    global $_settings;
	if(!empty($file)){
			// exit;
        $ex = explode("?",$file);
        $file = $ex[0];
        $ts = isset($ex[1]) ? "?".$ex[1] : '';
		if(is_file(base_app.$file)){
			return base_url.$file.$ts;
		}else{
			return base_url.($_settings->info('logo'));
		}
	}else{
		return base_url.($_settings->info('logo'));
	}
}
/** Escape untrusted text for safe HTML output. */
function e($value){
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function format_num($number = '' , $decimal = ''){
    if(is_numeric($number)){
        $ex = explode(".",$number);
        $decLen = isset($ex[1]) && abs($ex[1]) != 0 ? strlen($ex[1]) : 0;
        if(is_numeric($decimal)){
            return number_format($number,$decimal);
        }else{
            return number_format($number,$decLen);
        }
    }else{
        return "Invalid Input";
    }
}
function isMobileDevice(){
    $aMobileUA = array(
        '/iphone/i' => 'iPhone', 
        '/ipod/i' => 'iPod', 
        '/ipad/i' => 'iPad', 
        '/android/i' => 'Android', 
        '/blackberry/i' => 'BlackBerry', 
        '/webos/i' => 'Mobile'
    );

    //Return true if Mobile User Agent is detected
    foreach($aMobileUA as $sMobileKey => $sMobileOS){
        if(preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'] ?? '')){
            return true;
        }
    }
    //Otherwise return false..  
    return false;
}
?>
