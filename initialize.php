<?php
// $dev_data = array('id'=>'-1','firstname'=>'Developer','lastname'=>'','username'=>'dev_oretnom','password'=>'5da283a2d990e8d8512cf967df5bc0d0','last_login'=>'','date_updated'=>'','date_added'=>'');
if(!defined('base_url')) {
    $configuredBaseUrl = getenv('MUSIXX_BASE_URL');
    if ($configuredBaseUrl !== false && $configuredBaseUrl !== '') {
        define('base_url', rtrim($configuredBaseUrl, '/') . '/');
    } else {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // Reject malformed Host headers before using the value in generated links.
        if (!preg_match('/^[a-z0-9.-]+(?::[0-9]{1,5})?$/i', $host)) {
            $host = 'localhost';
        }
        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        if (preg_match('#^(.*?)/(admin|classes)(?:/.*)?$#', $scriptDir, $matches)) {
            $scriptDir = $matches[1];
        }
        $scriptDir = rtrim($scriptDir, '/');
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        define('base_url', $scheme . '://' . $host . ($scriptDir === '' ? '/' : $scriptDir . '/'));
    }
}
if(!defined('base_app')) define('base_app', str_replace('\\','/',__DIR__).'/' );
// if(!defined('dev_data')) define('dev_data',$dev_data);
if(!defined('DB_SERVER')) define('DB_SERVER',getenv('MUSIXX_DB_HOST') ?: '127.0.0.1');
if(!defined('DB_USERNAME')) define('DB_USERNAME',getenv('MUSIXX_DB_USER') ?: 'root');
if(!defined('DB_PASSWORD')) define('DB_PASSWORD',getenv('MUSIXX_DB_PASSWORD') !== false ? getenv('MUSIXX_DB_PASSWORD') : '');
if(!defined('DB_NAME')) define('DB_NAME',getenv('MUSIXX_DB_NAME') ?: 'mgs_dbs');
if(!defined('DB_PORT')) define('DB_PORT',(int) (getenv('MUSIXX_DB_PORT') ?: 3306));
?>
