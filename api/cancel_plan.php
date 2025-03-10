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

$date = date('Y-m-d');

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}

if (empty($_POST['plan_id'])) {
    $response['success'] = false;
    $response['message'] = "Plan Id is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);
$plan_id = $db->escapeString($_POST['plan_id']);

$sql = "SELECT * FROM users WHERE id = $user_id ";
$db->sql($sql);
$user = $db->getResult();
if (empty($user)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    print_r(json_encode($response));
    return false;
}

$sql = "SELECT * FROM plan WHERE id = $plan_id ";
$db->sql($sql);
$plan = $db->getResult();
if (empty($plan)) {
    $response['success'] = false;
    $response['message'] = "Plan not found";
    print_r(json_encode($response));
    return false;
}

$per_code_cost = $plan[0]['per_code_cost'];

$sql = "SELECT * FROM user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
$db->sql($sql);
$user_plan = $db->getResult();

if (empty($user_plan)) {
    $response['success'] = false;
    $response['message'] = "User plan not found";
    print_r(json_encode($response));
    return false;
}

$joined_date = $user_plan[0]['joined_date'];
$joined_date_obj = new DateTime($joined_date);
$current_date_obj = new DateTime($date);
$interval = $joined_date_obj->diff($current_date_obj);
$days_passed = $interval->days;
$total_days_limit = 30;
$remaining_days = max($total_days_limit - $days_passed, 0);

$amount_to_add = $remaining_days * $per_code_cost;

$sql = "UPDATE users SET recharge = recharge + $amount_to_add WHERE id = $user_id";
$db->sql($sql);

$sql_delete = "DELETE FROM user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
$db->sql($sql_delete);

$datetime = date('Y-m-d H:i:s');
$sql_insert_transaction = "INSERT INTO transactions (user_id, amount, datetime, type) VALUES ('$user_id', '$amount_to_add', '$datetime', 'plan_cancel')";
$db->sql($sql_insert_transaction);

$sql_user = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql_user);
$res_user = $db->getResult();

$response['success'] = true;
$response['message'] = "Plan cancelled successfully";
$response['data'] = $res_user;

print_r(json_encode($response));
?>
