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
    $sql_check = "SELECT * FROM outsource_user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
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

$sql = "SELECT sync_cost, num_sync FROM outsource_plan WHERE id = $plan_id";
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
// Check sync limit

// Check if there's an unused entry (datetime is empty)
$sql_check_unused = "SELECT id 
                     FROM outsource_user_plan 
                     WHERE user_id = $user_id 
                     AND plan_id = $plan_id 
                     AND (datetime IS NULL OR datetime = '') 
                     ORDER BY id ASC 
                     LIMIT 1";
$db->sql($sql_check_unused);
$unused_entry = $db->getResult();

if (!empty($unused_entry)) {
    // Found an unused entry, update it
    $unused_entry_id = $unused_entry[0]['id'];
    $sql_update_unused = "UPDATE outsource_user_plan 
                          SET datetime = '$datetime' 
                          WHERE id = $unused_entry_id";
    $db->sql($sql_update_unused);
} else {
    // If no unused entry, check if today's sync limit is reached
    $sql_check_sync_today = "SELECT COUNT(id) as num_sync 
                             FROM outsource_user_plan 
                             WHERE user_id = $user_id 
                             AND plan_id = $plan_id 
                             AND DATE(datetime) = CURDATE()";
    $db->sql($sql_check_sync_today);
    $sync_limit_data = $db->getResult();

    if (!empty($sync_limit_data) && $sync_limit_data[0]['num_sync'] >= $num_sync) {
        // Check for an older unused sync entry
        $sql_check_old_sync = "SELECT id 
                               FROM outsource_user_plan 
                               WHERE user_id = $user_id 
                               AND plan_id = $plan_id 
                               AND DATE(datetime) != CURDATE() 
                               ORDER BY datetime ASC 
                               LIMIT 1";
        $db->sql($sql_check_old_sync);
        $old_sync = $db->getResult();

        if (!empty($old_sync)) {
            $old_sync_id = $old_sync[0]['id'];
            $sql_update_old_sync = "UPDATE outsource_user_plan 
                                    SET datetime = '$datetime' 
                                    WHERE id = $old_sync_id";
            $db->sql($sql_update_old_sync);
        } else {
            // No available old sync slots, return error
            echo json_encode(["success" => false, "message" => "You Have Already Claimed Earnings For Today. Please Claim Tomorrow"]);
            exit;
        }
    }
}



// // Set the amount based on the plan_id
// switch ($plan_id) {
//     case 1:
//         $sync_cost = 50;
//         break;
//     case 2:
//         $sync_cost = 80;
//         break;
//     case 4:
//         $sync_cost = 110;
//         break;
//     case 6:
//         $sync_cost = 230;
//         break;
//     default:
//         $response['success'] = false;
//         $response['message'] = "Invalid Plan Id";
//         echo json_encode($response);
//         return;
// }

$total_cost = $sync_cost;

$sql = "UPDATE users SET earning_wallet = earning_wallet + $total_cost , today_earnings = today_earnings + $total_cost , total_earnings = total_earnings + $total_cost WHERE id = $user_id";
$db->sql($sql);

$sql = "INSERT INTO transactions (`user_id`, `amount`, `datetime`,`type`, `codes`) VALUES ('$user_id', '$total_cost', '$datetime','outsource_earnings',0)";
$db->sql($sql);

$datetime = date('Y-m-d H:i:s');

// Update only the most recent entry for this user_id and plan_id
$sql_update_claim = "UPDATE outsource_user_plan 
                     SET datetime = '$datetime' 
                     WHERE id = (
                         SELECT id FROM (
                             SELECT id FROM outsource_user_plan 
                             WHERE user_id = $user_id AND plan_id = $plan_id 
                             ORDER BY datetime DESC 
                             LIMIT 1
                         ) as subquery
                     )";

$db->sql($sql_update_claim);




if ($plan_id != 5) {
    // Check if the passed user_id has joined_date > 2025-01-01
    $sql = "SELECT joined_date FROM users WHERE id = $user_id";
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
            $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $level_income, today_codes = today_codes + $level_income, total_codes = total_codes + $level_income, `team_income` = `team_income` + $level_income WHERE id = $refer_id";
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
            $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $level_income, today_codes = today_codes + $level_income, total_codes = total_codes + $level_income, `team_income` = `team_income` + $level_income WHERE id = $refer_id";
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
$response['message'] = "Your claim is successfully credited to earning wallet.";
$response['user_data'] = $user_data;
echo json_encode($response);
  
?>
