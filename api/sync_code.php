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

// Validate inputs
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

$current_date = date('Y-m-d');
$sql_check_leave = "SELECT * FROM leaves WHERE date = '$current_date'";
$db->sql($sql_check_leave);
$leave_data = $db->getResult();

if (!empty($leave_data)) {
    $response['success'] = false;
    $response['message'] = "Today Holiday";
    echo json_encode($response);
    return;
}

if (in_array($plan_id, [1, 2, 4, 6])) {
    $sql_check = "SELECT * FROM user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
    $db->sql($sql_check);
    $user_plan = $db->getResult();

    if (empty($user_plan)) {
        $response['success'] = false;
        $response['message'] = "Plan not activated for this user";
        echo json_encode($response);
        return;
    }

    $claim = $user_plan[0]['claim'];
    if ($claim == 0) {
        $response['success'] = false;
        $response['message'] = "Your plan is expired";
        echo json_encode($response);
        return;
    }
}

$sql = "SELECT * FROM settings WHERE id=1";
$db->sql($sql);
$result = $db->getResult();
$code_generate = $result[0]['code_generate'];
$sync_time = $result[0]['sync_time'] * 60; // Convert minutes to seconds

if ($code_generate == 0) {
    $response['success'] = false;
    $response['message'] = "Code Generation is disabled";
    print_r(json_encode($response));
    return false;
}

$sql = "SELECT referred_by,code_generate,c_referred_by FROM users WHERE id = $user_id";
$db->sql($sql);
$users = $db->getResult();
if (empty($users)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    echo json_encode($response);
    return;
}
$code_generate = $users[0]['code_generate'];
if ($code_generate == 0) {
    $response['success'] = false;
    $response['message'] = "Code Generation is disabled for this user";
    echo json_encode($response);
    return;
}

$referred_by = $users[0]['referred_by'];
$c_referred_by = $users[0]['c_referred_by'];

$sql = "SELECT per_code_cost, num_sync ,refund FROM plan WHERE id = $plan_id";
$db->sql($sql);
$plan = $db->getResult();
if (empty($plan)) {
    $response['success'] = false;
    $response['message'] = "Plan not found";
    echo json_encode($response);
    return;
}

$per_code_cost = $plan[0]['per_code_cost'];
$num_sync = $plan[0]['num_sync'];
$refund = $plan[0]['refund'];

$current_date = date('Y-m-d');
$sql_check_sync = "SELECT COUNT(*) as sync_count FROM transactions WHERE user_id = $user_id AND type = 'Generated' AND DATE(datetime) = '$current_date'";
$db->sql($sql_check_sync);
$transaction_count = $db->getResult();

if ($transaction_count[0]['sync_count'] >= $num_sync) {
    $response['success'] = false;
    $response['message'] = "Sync Limit Exceeded for today";
    echo json_encode($response);
    return;
}

$sql_last_sync = "SELECT datetime FROM transactions WHERE user_id = $user_id AND type = 'Generated' ORDER BY datetime DESC LIMIT 1";
$db->sql($sql_last_sync);
$last_sync_result = $db->getResult();

if (!empty($last_sync_result)) {
    $last_sync_time = strtotime($last_sync_result[0]['datetime']);
    $current_time = time();
    $time_difference = $current_time - $last_sync_time;

    if ($time_difference < $sync_time) {
        $remaining_time = $sync_time - $time_difference;
        $response['success'] = false;
        $response['message'] = "Please wait " . ceil($remaining_time / 60) . " more minutes before your next sync.";
        echo json_encode($response);
        return;
    }
}

$codes = 50;

$total_cost = $codes * $per_code_cost;

$sql = "UPDATE users SET earning_wallet = earning_wallet + $total_cost , today_codes = today_codes + $codes , total_codes = total_codes + $codes , today_earnings = today_earnings + $total_cost , total_earnings = total_earnings + $total_cost , refund_wallet = refund_wallet + $refund WHERE id = $user_id";
$db->sql($sql);

$sql = "INSERT INTO transactions (`user_id`, `amount`, `datetime`,`type`, `codes`) VALUES ('$user_id', '$total_cost', '$datetime','Generated','$codes')";
$db->sql($sql);

if ($plan_id != 5) {
    // Check if the passed user_id has joined_date > 2025-01-01
    $sql = "SELECT joined_date FROM user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
    $db->sql($sql);
    $user_result = $db->getResult();
    
    if (!empty($user_result) && $user_result[0]['joined_date'] >= '2025-01-01') {
        // Check 'referred_by' eligibility for level income
        $sql = "SELECT id FROM users WHERE refer_code = '$referred_by'";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if ($num == 1) {
            $refer_id = $res[0]['id'];
            $level_income = $total_cost * 0.05;
            $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $level_income, team_income = team_income + $level_income WHERE id = $refer_id";
            $db->sql($sql);
            $sql_insert_transaction = "INSERT INTO transactions (`user_id`, `amount`, `datetime`, `type`) VALUES ('$refer_id', '$level_income', '$datetime', 'level_income')";
            $db->sql($sql_insert_transaction);
        }

        // Check 'c_referred_by' eligibility for level income
        $sql = "SELECT id FROM users WHERE refer_code = '$c_referred_by'";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);
        if ($num == 1) {
            $refer_id = $res[0]['id'];
            $level_income = $total_cost * 0.02;
            $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $level_income, team_income = team_income + $level_income WHERE id = $refer_id";
            $db->sql($sql);
            $sql_insert_transaction = "INSERT INTO transactions (`user_id`, `amount`, `datetime`, `type`) VALUES ('$refer_id', '$level_income', '$datetime', 'level_income')";
            $db->sql($sql_insert_transaction);
        }
    }
}

$sql = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql);
$user_data = $db->getResult();

$response['success'] = true;
$response['message'] = "Sync Completed Successfully";
$response['user_data'] = $user_data;
echo json_encode($response);
?>
