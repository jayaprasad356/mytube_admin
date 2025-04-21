<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


include_once('../includes/crud.php');
include_once('../includes/functions.php');
    
$fn = new functions;
$db = new Database();
$db->connect();

$fn->monitorApi('admin_fcm');

if (empty($_POST['fcm_id'])) {
    $response['success'] = false;
    $response['message'] = "FCM Id is Empty";
    print_r(json_encode($response));
    return false;
}

$fcm_id = $db->escapeString($_POST['fcm_id']);
$sql = "UPDATE admin SET fcm_id='$fcm_id' WHERE id= 2";
$db->sql($sql);


?>