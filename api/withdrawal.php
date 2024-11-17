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
date_default_timezone_set('Asia/Kolkata');
include_once('../includes/functions.php');
$fn = new functions;

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['amount'])) {
    $response['success'] = false;
    $response['message'] = "Amount is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['type'])) {
    $response['success'] = false;
    $response['message'] = "type is Empty";
    print_r(json_encode($response));
    return false;
}
$date = date('Y-m-d');
function isBetween10AMand6PM() {
    $currentHour = date('H');
    $startTimestamp = strtotime('10:00:00');
    $endTimestamp = strtotime('18:00:00');
    return ($currentHour >= date('H', $startTimestamp)) && ($currentHour < date('H', $endTimestamp));
}

$user_id = $db->escapeString($_POST['user_id']);
$amount = $db->escapeString($_POST['amount']);
$type = $db->escapeString($_POST['type']);
$datetime = date('Y-m-d H:i:s');
$dayOfWeek = date('w', strtotime($datetime));

if (!isBetween10AMand6PM()) {
    $response['success'] = false;
    $response['message'] = "Withdrawal time morning 10:00AM to 6PM";
    print_r(json_encode($response));
    return false;
}

$dayOfWeek = date('w');

if ($dayOfWeek == 0 || $dayOfWeek == 7) {
    $response['success'] = false;
    $response['message'] = "Withdrawal time Monday to Saturday";
    print_r(json_encode($response));
    return false;
} 

$sql = "SELECT * FROM settings";
$db->sql($sql);
$mres = $db->getResult();
$main_ws = $mres[0]['withdrawal_status'];
$min_withdrawal = $mres[0]['min_withdrawal'];
$sql = "SELECT balance,withdrawal_status FROM users WHERE id = $user_id ";
$db->sql($sql);
$res = $db->getResult();
$balance = $res[0]['balance'];
$withdrawal_status = $res[0]['withdrawal_status'];
$datetime = date('Y-m-d H:i:s');
$date = date('Y-m-d');
$sql = "SELECT id FROM bank_details WHERE user_id = $user_id ";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if($withdrawal_status == 1 &&  $main_ws == 1 ){
    if (($type == 'bank_transfer' && $num >= 1) || $type == 'cash_payment') {
        if($amount >= $min_withdrawal){
                if($balance >= $amount){

                    $sql = "SELECT * FROM withdrawals WHERE user_id='$user_id' AND status = 0";
                    $db->sql($sql);
                    $pendingWithdrawals = $db->getResult();
                    if (!empty($pendingWithdrawals)) {
                        $response['success'] = false;
                        $response['message'] = "Please withdraw again after your pending withdrawal is paid";
                        print_r(json_encode($response));
                        return false;
                    }
                    
                    $sql = "UPDATE `users` SET `balance` = balance - $amount,`withdrawal` = withdrawal + $amount WHERE `id` = $user_id";
                    $db->sql($sql);
                    $sql = "INSERT INTO withdrawals (`user_id`,`amount`,`datetime`,`type`)VALUES('$user_id','$amount','$datetime','$type')";
                    $db->sql($sql);
                    $sql = "SELECT balance FROM users WHERE id = $user_id ";
                    $db->sql($sql);
                    $res = $db->getResult();
                    $balance = $res[0]['balance'];     
                    $response['success'] = true;
                    $response['balance'] = $balance;
                    $response['message'] = "Withdrawal Requested Successfully";
                    print_r(json_encode($response));
            
                }
                else{
                    $response['success'] = false;
                    $response['message'] = "Insufficent Balance";
                    print_r(json_encode($response)); 
                }
            // }

        
        }
        else{
            $response['success'] = false;
            $response['message'] = "Required Minimum Amount to Withdrawal is ".$min_withdrawal;
            print_r(json_encode($response)); 
        }
    }else{
        $response['success'] = false;
        $response['message'] = "Update Bank Details first";
        print_r(json_encode($response)); 
    
    }
}else{
    $response['success'] = false;
    $response['message'] = "Withdrawal Disabled Right Now,Please Try Again";
    print_r(json_encode($response));    
}

?>