<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');



if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}


$user_id = $db->escapeString($_POST['user_id']);
$sql = "UPDATE users SET task_type='champion' WHERE id='$user_id'";
$db->sql($sql);
$result = $db->getResult();
$response['success'] = true;
$response['message'] = "Successfully Switched To Champion Task";
$response['task_type'] = 'champion';
print_r(json_encode($response));
?>
