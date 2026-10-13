<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Use the path only; host headers and substring checks are not authentication signals.
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isLoginPage = basename($requestPath) === 'login.php';
if(!isset($_SESSION['userdata']) && !$isLoginPage){
	redirect('admin/login.php');
}
if(isset($_SESSION['userdata']) && $isLoginPage){
	redirect('admin/index.php');
}
if(isset($_SESSION['userdata'])){
    $loginType = filter_var($_SESSION['userdata']['login_type'] ?? null, FILTER_VALIDATE_INT);
    if($loginType !== 1){
        http_response_code(403);
        exit('Access denied.');
    }
}
