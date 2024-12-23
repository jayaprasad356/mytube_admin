<?php 
header('Access-Control-Allow-Origin: *'); 
header("Content-Type: application/json"); 
header("Expires: 0"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 
date_default_timezone_set('Asia/Kolkata');  

include_once('../includes/crud.php');  
$db = new Database(); 
$db->connect();  

include_once('../includes/functions.php'); 
$fn = new functions;  


// Reset today's codes
$sql = "UPDATE users SET today_codes = 0"; 
$db->sql($sql); 
?>
