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
$currentdate = date('Y-m-d');

$sql = "SELECT * FROM users WHERE status = 0 LIMIT 50";
$db->sql($sql);
$res = $db->getResult();
$refer = array();
foreach ($res as $row) {
    $refer_code = $row['refer_code'];
    $sql = "SELECT * FROM users WHERE referred_by = '$refer_code'";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    if ($num >= 1) {
        $rows[] = $res;

    }

    
}

$response['success'] = true;
$response['message'] = "Listed Successfully";
$response['data'] = $rows;
print_r(json_encode($response));








?>