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
include_once('../includes/custom-functions.php');
include_once('../includes/functions.php');
$fn = new custom_functions;
$fnc = new functions;
$db = new Database();
$db->connect();
$currentdate = date('Y-m-d');
$datetime = date('Y-m-d H:i:s');
$sql = "SELECT id FROM `users` WHERE status = 1 AND code_generate = 1";
$db->sql($sql);
$res = $db->getResult();
foreach ($res as $row) {
    $id = $row['id'];
    $history_days = $fnc->get_leave($id);
    $sql = "UPDATE `users` SET  `worked_days` = $history_days WHERE `id` = $id";
    $db->sql($sql);
}

$response['success'] = true;
$response['message'] = 'Amail History Days Updated Successfully';
print_r(json_encode($response));








?>