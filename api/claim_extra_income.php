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

if (empty($_POST['refer_id'])) {
    $response['success'] = false;
    $response['message'] = "Refer Id is Empty";
    echo json_encode($response);
    return;
}

$user_id = $db->escapeString($_POST['user_id']);
$refer_id = $db->escapeString($_POST['refer_id']);

$sql = "SELECT refer_count, bonus FROM refers_target WHERE id = $refer_id";
$db->sql($sql);
$refers = $db->getResult();
if (empty($refers)) {
    $response['success'] = false;
    $response['message'] = "Refer target not found";
    echo json_encode($response);
    return;
}

$refer_count = $refers[0]['refer_count'];
$bonus = $refers[0]['bonus'];

$sql = "SELECT total_referrals FROM users WHERE id = $user_id";
$db->sql($sql);
$user_data = $db->getResult();
if (empty($user_data)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    echo json_encode($response);
    return;
}

$total_referrals = $user_data[0]['total_referrals'];

if ($total_referrals == $refer_count) {

    $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $bonus WHERE id = $user_id";
    $db->sql($sql);

    $sql = "INSERT INTO transactions (user_id, amount, datetime, type) VALUES ('$user_id', '$bonus', '$datetime', 'Generated')";
    $db->sql($sql);

    $sql = "SELECT * FROM users WHERE id = $user_id";
    $db->sql($sql);
    $updated_user_data = $db->getResult();

    $response['success'] = true;
    $response['message'] = "Bonus awarded successfully!";
    $response['user_data'] = $updated_user_data;
} else {
    $response['success'] = false;
    $response['message'] = "Referral target not met. No bonus awarded.";
}

echo json_encode($response);
?>
