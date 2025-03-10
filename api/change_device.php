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

include_once('../includes/functions.php');
$fn = new functions;

// $datetime = date('Y-m-d H:i:s');

// $api_name = 'change_device';
// $sql_log_api_call = "INSERT INTO api_calls (api_name, datetime) VALUES ('$api_name', '$datetime')";
// $db->sql($sql_log_api_call);


if (empty($_POST['mobile'])) {
    $response['success'] = false;
    $response['message'] = "Mobile is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['password'])) {
    $response['success'] = false;
    $response['message'] = "Password is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['device_id'])) {
    $response['success'] = false;
    $response['message'] = "Device Id is Empty";
    print_r(json_encode($response));
    return false;
}
$mobile = $db->escapeString($_POST['mobile']);
$password = $db->escapeString($_POST['password']);
$device_id = $db->escapeString($_POST['device_id']);
$sql = "SELECT * FROM users WHERE mobile = '$mobile' AND password = '$password'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num == 1) {
    $user_id = $res[0]['id'];
    $sql = "SELECT * FROM device_requests WHERE user_id = '$user_id'";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    if ($num >= 1) {
        $sql = "UPDATE device_requests SET device_id='$device_id',status=0 WHERE user_id=" . $user_id;
        $db->sql($sql);
    }else{
        $sql = "INSERT INTO device_requests (`user_id`,`device_id`,`status`)VALUES('$user_id','$device_id',0)";
        $db->sql($sql);
    }
    $response['success'] = true;
    $response['message'] ="Device Request Successfully";
    print_r(json_encode($response));
    return false;

}
else{
    
    $response['success'] = false;
    $response['message'] ="User Not Found";
    print_r(json_encode($response));
    return false;

}

?>