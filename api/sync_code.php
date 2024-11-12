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
include_once('../includes/custom-functions.php');
include_once('../includes/functions.php');
$fn = new functions;
$datetime = date('Y-m-d H:i:s');

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    echo json_encode($response);
    return;
}

if (empty($_POST['plan_id'])) {
    $response['success'] = false;
    $response['message'] = "Plan Id is Empty";
    echo json_encode($response);
    return;
}

$user_id = $db->escapeString($_POST['user_id']);
$plan_id = $db->escapeString($_POST['plan_id']);

$dayOfWeek = date('w');

$sql_check = "SELECT * FROM user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
$db->sql($sql_check);
$user_plan = $db->getResult();

if (empty($user_plan)) {
    $response['success'] = false;
    $response['message'] = "Plan not activated for this user";
    echo json_encode($response);
    return;
}

$sql = "SELECT per_code_cost FROM plan WHERE id = $plan_id";
$db->sql($sql);
$plan = $db->getResult();
if (empty($plan)) {
    $response['success'] = false;
    $response['message'] = "Plan not found";
    echo json_encode($response);
    return;
}
$per_code_cost = $plan[0]['per_code_cost'];

$per_code = 50 * $per_code_cost;

 $sql = "UPDATE users SET earning_wallet = earning_wallet + $per_code , today_codes = today_codes + 50 ,total_codes = total_codes + 50 ,today_earnings = today_earnings + 50 ,total_earnings = total_earnings + 50  WHERE id = $user_id";
 $db->sql($sql);

 $sql = "INSERT INTO transactions (`user_id`, `amount`, `datetime`, `type`) VALUES ('$user_id', '$per_code', '$datetime', 'Generated')";
 $db->sql($sql);

 $sql = "SELECT * FROM users WHERE id = $user_id";
 $db->sql($sql);
 $user_data = $db->getResult();
 
 $response['success'] = true;
 $response['message'] = "Sync Completed Successfully";
 $response['user_data'] = $user_data;
 echo json_encode($response);
 ?>
