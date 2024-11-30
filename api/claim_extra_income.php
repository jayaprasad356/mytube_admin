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
    print_r(json_encode($response));
    return false;
}

if (empty($_POST['refer_id'])) {
    $response['success'] = false;
    $response['message'] = "Refer Id is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);
$refer_id = $db->escapeString($_POST['refer_id']);

// Fetch user data to check total_referrals
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

$sql = "SELECT * FROM user_extra_claim_plan WHERE user_id = $user_id ";
$db->sql($sql);
$extra_claim_plan = $db->getResult();

if (empty($extra_claim_plan)) {
    $response['success'] = false;
    $response['message'] = "You must activate the extra claim plan before claiming this bonus";
    echo json_encode($response);
    return;
}
$sql = "SELECT * FROM refer_counts WHERE user_id = $user_id AND refer_id = $refer_id";
$db->sql($sql);
$refer_claim = $db->getResult();

if (!empty($refer_claim)) {
    $response['success'] = false;
    $response['message'] = "You have already claimed this referral bonus for refer_id $refer_id";
    echo json_encode($response);
    return;
}
$valid_refer_ids = [];

if ($total_referrals >= 5 && $total_referrals <= 9) {
    $valid_refer_ids = [1];
} elseif ($total_referrals >= 10 && $total_referrals <= 19) {
    $valid_refer_ids = [1, 2];
} elseif ($total_referrals >= 20 && $total_referrals <= 29) {
    $valid_refer_ids = [1, 2, 3];
} elseif ($total_referrals >= 30) {
    $valid_refer_ids = [1, 2, 3, 4];
} else {
    $response['success'] = false;
    $response['message'] = "No bonus applicable for this referral count";
    echo json_encode($response);
    return;
}

if (!in_array($refer_id, $valid_refer_ids)) {
    $response['success'] = false;
    $response['message'] = "You cannot claim this referral bonus at this stage";
    echo json_encode($response);
    return;
}

// Check if the user has claimed the previous refer_id
$previous_refer_id = $refer_id - 1; // Previous refer_id (e.g., if refer_id is 2, check for 1)
if ($previous_refer_id >= 1) {
    $sql = "SELECT * FROM refer_counts WHERE user_id = $user_id AND refer_id = $previous_refer_id";
    $db->sql($sql);
    $refer_counts = $db->getResult();

    if (empty($refer_counts)) {
        $response['success'] = false;
        $response['message'] = "You must claim the previous referral (refer_id $previous_refer_id) first";
        echo json_encode($response);
        return;
    }
}

$sql = "INSERT INTO refer_counts (user_id, refer_id) VALUES ($user_id, $refer_id)";
$db->sql($sql);

$sql = "SELECT bonus FROM refers_target WHERE id = $refer_id";
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

// Fetch updated user data
$sql = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql);
$updated_user_data = $db->getResult();

$response['success'] = true;
$response['message'] = "Extra income claimed successfully!";
$response['user_data'] = $updated_user_data;

echo json_encode($response);
?>
