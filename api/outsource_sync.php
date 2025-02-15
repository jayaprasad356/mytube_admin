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

if (empty($_POST['outsource_user_plan_id'])) {
    $response['success'] = false;
    $response['message'] = "Outsource User Plan Id is Empty";
    echo json_encode($response);
    return;
}

$user_id = $db->escapeString($_POST['user_id']);
$plan_id = $db->escapeString($_POST['plan_id']);
$outsource_user_plan_id = $db->escapeString($_POST['outsource_user_plan_id']);

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
    $sql_check = "SELECT * FROM outsource_user_plan WHERE user_id = $user_id AND plan_id = $plan_id AND id = $outsource_user_plan_id";
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

if (in_array($plan_id, [1, 2, 4, 5, 6])) {
    $sql_check = "SELECT * FROM outsource_user_plan WHERE user_id = $user_id AND plan_id = $plan_id AND id = $outsource_user_plan_id";
    $db->sql($sql_check);
    $user_plan = $db->getResult();

    $joined_date = date('Y-m-d', strtotime($user_plan[0]['joined_date']));
    $current_date = date('Y-m-d');
    if ($joined_date == $current_date) {
        $response['success'] = false;
        $response['message'] = "You are not eligible to claim today";
        echo json_encode($response);
        return;
    }
}

$sql = "SELECT * FROM settings WHERE id=1";
$db->sql($sql);
$result = $db->getResult();
$outsource_code_generate = $result[0]['outsource_code_generate'];

if ($outsource_code_generate == 0) {
    $response['success'] = false;
    $response['message'] = "Outsource Code Generation is disabled";
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

$sql = "SELECT sync_cost, num_sync , refund FROM outsource_plan WHERE id = $plan_id";
$db->sql($sql);
$plan = $db->getResult();
if (empty($plan)) {
    $response['success'] = false;
    $response['message'] = "Plan not found";
    echo json_encode($response);
    return;
}

$sync_cost = $plan[0]['sync_cost'];
$num_sync = $plan[0]['num_sync'];
$refund = $plan[0]['refund'];

// Check if claim has already been made and num_sync is 1 for today
$sql_check_claimed = "SELECT datetime FROM outsource_user_plan WHERE id = $outsource_user_plan_id AND user_id = $user_id AND plan_id = $plan_id";
$db->sql($sql_check_claimed);
$claim_check = $db->getResult();

if (!empty($claim_check) && !is_null($claim_check[0]['datetime'])) {
    $claim_date = date('Y-m-d', strtotime($claim_check[0]['datetime']));
    $claim_time = date('H:i:s', strtotime($claim_check[0]['datetime']));
    if ($claim_date == $current_date && $num_sync == 1) {
        if ($claim_time < '12:00:00' || $claim_time >= '00:00:00') {
            $response['success'] = false;
            $response['message'] = "You Have Already Claimed Earnings For Today. Please Claim Tomorrow.";
            echo json_encode($response);
            return;
        }
    }
}

// Proceed with updating the claim in outsource_user_plan
$sql_update_claim = "UPDATE outsource_user_plan SET datetime = '$datetime' WHERE id = $outsource_user_plan_id";
$db->sql($sql_update_claim);

// Proceed with the rest of the logic to credit earnings
$total_cost = $sync_cost;
$sql = "UPDATE users SET earning_wallet = earning_wallet + $total_cost , today_earnings = today_earnings + $total_cost , total_earnings = total_earnings + $total_cost , refund_wallet = refund_wallet + $refund WHERE id = $user_id";
$db->sql($sql);

$sql = "INSERT INTO transactions (`user_id`, `amount`, `datetime`,`type`, `codes`) VALUES ('$user_id', '$total_cost', '$datetime','outsource_earnings',0)";
$db->sql($sql);


// if ($plan_id != 5) {
//     // Check if the passed user_id has joined_date > 2025-01-01
//     $sql = "SELECT joined_date FROM outsource_user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
//     $db->sql($sql);
//     $user_result = $db->getResult();
    
//     if (!empty($user_result) && $user_result[0]['joined_date'] >= '2025-01-01') {
//         $sql = "SELECT id FROM users WHERE refer_code = '$referred_by'";
//         $db->sql($sql);
//         $res = $db->getResult();
//         $num = $db->numRows($res);
//         if ($num == 1) {
//             $refer_id = $res[0]['id'];
//             $level_income = $total_cost * 0.05;
//             $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $level_income,team_income = team_income + $level_income WHERE id = $refer_id";
//             $db->sql($sql);
//             $sql_insert_transaction = "INSERT INTO transactions (`user_id`, `amount`, `datetime`, `type`) VALUES ('$refer_id', '$level_income', '$datetime', 'level_income')";
//             $db->sql($sql_insert_transaction);
//         }

//         // Check 'c_referred_by' eligibility for level income
//         $sql = "SELECT id FROM users WHERE refer_code = '$c_referred_by'";
//         $db->sql($sql);
//         $res = $db->getResult();
//         $num = $db->numRows($res);
//         if ($num == 1) {
//             $refer_id = $res[0]['id'];
//             $level_income = $total_cost * 0.02;
//             $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $level_income, team_income = team_income + $level_income WHERE id = $refer_id";
//             $db->sql($sql);
//             $sql_insert_transaction = "INSERT INTO transactions (`user_id`, `amount`, `datetime`, `type`) VALUES ('$refer_id', '$level_income', '$datetime', 'level_income')";
//             $db->sql($sql_insert_transaction);
//         }
//     }
// }

$sql = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql);
$user_data = $db->getResult();

$response['success'] = true;
$response['message'] = "Your claim is successfully credited to earning wallet.";
$response['user_data'] = $user_data;
echo json_encode($response);
  
?>
