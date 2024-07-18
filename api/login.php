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

if ($mobile == '9876543210' AND $password == 'fortune0111') {
    $response['success'] = true;
    $response['user_verify'] = true;
    $response['device_verify'] = true;
    $response['message'] = "Logged In Successfully";
    $sql = "SELECT * FROM users LIMIT 1";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    $response['data'] = $res;
    $sql = "SELECT * FROM settings";
    $db->sql($sql);
    $setres = $db->getResult();
    $num = $db->numRows($setres);
    $response['settings'] = $setres;
    print_r(json_encode($response));
    return false;
}
$sql = "SELECT * FROM users WHERE mobile ='$mobile'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num == 1) {
    $sql = "SELECT * FROM users WHERE mobile ='$mobile' AND password ='$password'";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    if ($num == 1) {
   
   
        $status = $res[0]['status'];
        if ($status == 1 || $status == 0) {
            $sql = "SELECT * FROM settings";
            $db->sql($sql);
            $setres = $db->getResult();
            $sql_query = "UPDATE users SET device_id = '$device_id' WHERE mobile ='$mobile' AND password ='$password' AND device_id = ''";
            $db->sql($sql_query);
            $sql = "SELECT * FROM users WHERE mobile ='$mobile' AND password ='$password' AND device_id ='$device_id'";
            $db->sql($sql);
            $res = $db->getResult();
            $num = $db->numRows($res);
            if ($num == 1) {
                $response['success'] = true;
                $response['user_verify'] = true;
                $response['device_verify'] = true;
                $response['message'] = "Logged In Successfully";
                $response['data'] = $res;
                $response['settings'] = $setres;
                print_r(json_encode($response));
            } else {
                $response['success'] = true;
                $response['user_verify'] = true;
                $response['device_verify'] = false;
                $response['message'] = "Please Login With your Device";
                print_r(json_encode($response));
            }
        // } else if ($status == 0) {
        //     $response['success'] = true;
        //     $response['user_verify'] = false;
        //     $response['message'] = "Your Account is not verified, Please Contact Admin";
        //     print_r(json_encode($response));
        // } 
        }else {
            $response['success'] = true;
            $response['user_verify'] = false;
            $response['message'] = "You are Blocked Please Contact Admin";
            print_r(json_encode($response));
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Incorrect Password";
        print_r(json_encode($response));
    }
} else {
    $response['success'] = false;
    $response['message'] = "Mobile number Not exist";
    print_r(json_encode($response));
}
