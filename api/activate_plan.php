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

$recharge = $user[0]['recharge'];
$refer_code = $user[0]['refer_code'];
$referred_by = $user[0]['referred_by'];

$sql = "SELECT * FROM plan WHERE id = $plan_id ";
$db->sql($sql);
$plan = $db->getResult();

if (empty($plan)) {
    $response['success'] = false;
    $response['message'] = "Plan not found";
    print_r(json_encode($response));
    return false;
}

$price = $plan[0]['price'];
$min_refers = $plan[0]['min_refers'];
$invite_bonus = $plan[0]['invite_bonus'];
$refer_refund_amount = $plan[0]['refer_refund_amount'];
$per_code_cost = $plan[0]['per_code_cost'];
$datetime = date('Y-m-d H:i:s');

$sql_check = "SELECT * FROM user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
$db->sql($sql_check);
$res_check_user = $db->getResult();

if (!empty($res_check_user)) {
    $response['success'] = false;
    $response['message'] = "You have already activated this plan.";
    print_r(json_encode($response));
    return false;
}

if ($plan_id != 5) {
    $sql_check_active_plan = "SELECT plan_id FROM user_plan WHERE user_id = $user_id AND plan_id IN (1, 2, 4)";
    $db->sql($sql_check_active_plan);
    $res_check_active_plan = $db->getResult();

    if (!empty($res_check_active_plan)) {
        $activated_plan = $res_check_active_plan[0]['plan_id'];

        $plan_names = [
            1 => "2999 plan",
            2 => "3999 plan",
            4 => "5999 plan",
            6 => "11999 plan"
        ];

        $activated_plan_name = $plan_names[$activated_plan];
        $response['success'] = false;
        $response['message'] = "You have already activated the $activated_plan_name. You cannot activate another plan.";
        print_r(json_encode($response));
        return false;
    }
}

if (in_array($plan_id, [1, 2, 4, 6])) {
    $sql_check_plan_5 = "SELECT * FROM user_plan WHERE user_id = $user_id AND plan_id = 5";
    $db->sql($sql_check_plan_5);
    $res_plan_5 = $db->getResult();

    if (!empty($res_plan_5)) {
        $sql_update_plan_5 = "UPDATE user_plan SET claim = 0 WHERE user_id = $user_id AND plan_id = 5";
        $db->sql($sql_update_plan_5);
    }
}

if ($recharge >= $price) {
    if ($refer_code) {
        $sql = "SELECT * FROM users WHERE refer_code = '$referred_by'";
        $db->sql($sql);
        $res = $db->getResult();
        $num = $db->numRows($res);

        if ($num == 1) {
            $r_id = $res[0]['id'];
            $r_refer_code = $res[0]['refer_code'];

            $sql_check_user_plan = "SELECT * FROM user_plan WHERE user_id = '$r_id' AND plan_id = 5 AND claim = 1";
            $db->sql($sql_check_user_plan);
            $res_check_user_plan = $db->getResult();
         
            if (!empty($res_check_user_plan)) {

                $total_cost = 0;
                $total_referrals = 0;

                if ($plan_id == 1) {
                    $total_cost = 100;
                    $total_referrals = 1;
                } elseif ($plan_id == 2) {
                    $total_cost = 200;
                    $total_referrals = 1;
                } elseif ($plan_id == 4) {
                    $total_cost = 300;
                    $total_referrals = 1;
                } elseif ($plan_id == 6) {
                    $total_cost = 1000;
                    $total_referrals = 1;
                }
            
                $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $total_cost ,total_referrals = total_referrals + $total_referrals , refund_wallet = refund_wallet + $refer_refund_amount WHERE refer_code = '$referred_by'";
                $db->sql($sql);
            
                $sql = "INSERT INTO transactions (user_id, amount, datetime, type) VALUES ('$r_id', '$total_cost', '$datetime', 'refer_bonus')";
                $db->sql($sql);
            } 
            else {
                if ($plan_id == 1 || $plan_id == 2 || $plan_id == 4 ) {
                    $codes = 2000;
                    $total_cost = $codes * $per_code_cost;
            
                    $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $total_cost, total_referrals = total_referrals + 1 , refund_wallet = refund_wallet + $refer_refund_amount WHERE refer_code = '$referred_by'";
                    $db->sql($sql);
            
                    $sql = "INSERT INTO transactions (user_id, amount, datetime, type) VALUES ('$r_id', '$total_cost', '$datetime', 'refer_bonus')";
                    $db->sql($sql);
                }
                if ($plan_id == 6) {
                    $cost = 1200;
                    $total_cost = $cost;
            
                    $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $total_cost, total_referrals = total_referrals + 1 ,  refund_wallet = refund_wallet + $refer_refund_amount WHERE refer_code = '$referred_by'";
                    $db->sql($sql);
            
                    $sql = "INSERT INTO transactions (user_id, amount, datetime, type) VALUES ('$r_id', '$total_cost', '$datetime', 'refer_bonus')";
                    $db->sql($sql);
                }

            }
            
            if ($plan_id == 5) {
            $total_cost = 5;
            $codes = 0;

            $sql = "UPDATE users SET bonus_wallet = bonus_wallet + $total_cost ,  refund_wallet = refund_wallet + $refer_refund_amount WHERE refer_code = '$referred_by'";
            $db->sql($sql);
    
            $sql = "INSERT INTO transactions (user_id, amount, datetime, type) VALUES ('$r_id', '$total_cost', '$datetime', 'refer_bonus')";
            $db->sql($sql);
           }
         
        }
    }
  
    $sql = "UPDATE users SET recharge = recharge - $price, total_assets = total_assets + $price, withdrawal_status = 1 WHERE id = $user_id";
    $db->sql($sql);

    $sql_insert_user_plan = "INSERT INTO user_plan (user_id, plan_id, joined_date, claim) VALUES ('$user_id', '$plan_id', '$date', 1)";
    $db->sql($sql_insert_user_plan);

    $sql_insert_transaction = "INSERT INTO transactions (user_id, amount, datetime, type) VALUES ('$user_id', '$price', '$datetime', 'plan_activated')";
    $db->sql($sql_insert_transaction);

    $sql_user = "SELECT * FROM users WHERE id = $user_id";
    $db->sql($sql_user);
    $res_user = $db->getResult();

    $response['success'] = true;
    $response['message'] = "Plan started successfully";
    $response['data'] = $res_user;
} else {
    $response['success'] = false;
    $response['message'] = "Low Recharge Balance. Recharge To Activate The Plan";
}

print_r(json_encode($response));
?>
