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
$fn->monitorApi('referalslist');


if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
$user_id = $db->escapeString($_POST['user_id']);
$sql = "SELECT id,name,refer_code,sync_refer_wallet,refer_income FROM users WHERE id = '$user_id'";
$db->sql($sql);
$result = $db->getResult();
$refer_code=$result[0]['refer_code'];
$sync_refer_wallet=$result[0]['sync_refer_wallet'];
$refer_income=$result[0]['refer_income'];
$sql = "SELECT id,name,mobile,refer_code,balance,sync_refer_wallet,refer_income FROM users WHERE referred_by = '$refer_code'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    foreach ($res as $row){
        $mobile =$row['mobile'];
        $mobile_length = strlen($mobile);
        $first_two = substr($mobile, 0, 2);
        $last_two = substr($mobile, -2);
        $asterisks = str_repeat("*", $mobile_length - 4);
        $masked_mobile = $first_two . $asterisks . $last_two;
        $temp['id'] = $row['id'];
        $temp['name'] = $row['name'];
        $temp['mobile'] = $masked_mobile;
        $temp['refer_code'] = $row['refer_code'];
        // $temp['balance'] = $row['balance'];
        $rows[] = $temp;
        
    }
    $response['success'] = true;
    $response['message'] = "Referals Listed Successfully";
    $response['refer_income'] =$refer_income;
    $response['sync_refer_wallet'] =$sync_refer_wallet;
    $response['message'] = "Referals Listed Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] = "No Data Found";
    $response['refer_income'] =$refer_income;
    $response['sync_refer_wallet'] =$sync_refer_wallet;
    print_r(json_encode($response));
}

?>