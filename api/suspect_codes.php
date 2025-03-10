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
$fn->monitorApi('suspect_codes');

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
$datetime = date('Y-m-d H:i:s');
$codes = (isset($_POST['codes']) && $_POST['codes'] != "") ? $db->escapeString($_POST['codes']) : 0;
$user_id = $db->escapeString($_POST['user_id']);
$total_text = (isset($_POST['total_text']) && $_POST['total_text'] != "") ? $db->escapeString($_POST['total_text']) : 0;
$typed_text = (isset($_POST['typed_text']) && $_POST['typed_text'] != "") ? $db->escapeString($_POST['typed_text']) : 0;

$sql = "INSERT INTO suspect_codes (`user_id`,`codes`,`total_text`,`typed_text`,`datetime`)VALUES('$user_id',$codes,$total_text,$typed_text,'$datetime')";
$db->sql($sql);

$response['success'] = true;
$response['message'] = "Suspected Codes Addded";

print_r(json_encode($response));

?>
