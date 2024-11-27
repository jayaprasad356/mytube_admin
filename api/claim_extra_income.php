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
    $response['message'] = "User ID is empty";
    echo json_encode($response);
    return;
}

$user_id = $db->escapeString($_POST['user_id']);
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

if ($total_referrals == 5 ) {
    $refer_id = 1;
} elseif ($total_referrals >= 6 && $total_referrals <= 10) {
    $refer_id = 2;
} elseif ($total_referrals >= 11 && $total_referrals <= 20) {
    $refer_id = 3;
} elseif ($total_referrals >= 21 && $total_referrals <= 30) {
    $refer_id = 4;
} else {
    $response['success'] = false;
    $response['message'] = "No bonus applicable for this referral count";
    echo json_encode($response);
    return;
}

$sql = "SELECT refer_count, bonus FROM refers_target WHERE id = $refer_id";
$db->sql($sql);
$refers = $db->getResult();

if (empty($refers)) {
    $response['success'] = false;
    $response['message'] = "Refer target not found";
    echo json_encode($response);
    return;
}

$bonus = $refers[0]['bonus'];

$sql = "UPDATE users SET bonus_wallet = bonus_wallet + $bonus WHERE id = $user_id";
$db->sql($sql);

$sql = "INSERT INTO transactions (user_id, amount, datetime, type) VALUES ('$user_id', '$bonus', '$datetime', 'extra_income')";
$db->sql($sql);

$sql = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql);
$updated_user_data = $db->getResult();

$response['success'] = true;
$response['message'] = "Extra income claimed successfully!";
$response['user_data'] = $updated_user_data;

echo json_encode($response);
?>
