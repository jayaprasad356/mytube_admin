<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


include_once('../includes/crud.php');

$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
include_once('../includes/custom-functions.php');
$fn = new custom_functions;
include_once('../includes/functions.php');
$function = new functions;

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}



$user_id = $db->escapeString($_POST['user_id']);
$function->monitorUserApi('wallet',$user_id);
$codes = (isset($_POST['codes']) && $_POST['codes'] != "") ? $db->escapeString($_POST['codes']) : 0;
$sync_unique_id = (isset($_POST['sync_unique_id']) && $_POST['sync_unique_id'] != "") ? $db->escapeString($_POST['sync_unique_id']) : '';
$datetime = date('Y-m-d H:i:s');

$type = 'generate';
$sql = "SELECT app_version FROM users WHERE id = $user_id";

$db->sql($sql);
$ures = $db->getResult();
$sql = "SELECT code_generate,num_sync_times,sync_codes,code_min_sync_time FROM settings";
$db->sql($sql);
$set = $db->getResult();
$code_generate = $set[0]['code_generate'];
$sync_codes = $set[0]['sync_codes'];
$app_version = $ures[0]['app_version'];

$t_sync_unique_id = '';
$sql = "SELECT datetime,sync_unique_id FROM transactions WHERE user_id = $user_id AND type = 'generate' ORDER BY datetime DESC LIMIT 1 ";
$db->sql($sql);
$tres = $db->getResult();
$num = $db->numRows($tres);
if ($num >= 1) {
    $dt1 = $tres[0]['datetime'];
    $t_sync_unique_id = $tres[0]['sync_unique_id'];
    $date1 = new DateTime($dt1);
    $date2 = new DateTime($datetime);

    $diff = $date1->diff($date2);
    $totalMinutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
    if($totalMinutes < $code_min_sync_time){
        $response['success'] = false;
        $response['message'] = "Cannot Sync Right Now, Try again after few mins";
        print_r(json_encode($response));
        return false;

    }


}
if($code_generate == 1){
    if($codes != 0){
        if($codes <= $sync_codes){
            $currentdate = date('Y-m-d');
            $per_code_cost = $fn->get_code_per_cost($user_id);
            $amount = $codes  * $per_code_cost;
            $sql = "SELECT COUNT(id) AS count  FROM transactions WHERE user_id = $user_id AND DATE(datetime) = '$currentdate'";
            $db->sql($sql);
            $tres = $db->getResult();
            $t_count = $tres[0]['count'];
            if ($t_count > $set[0]['num_sync_times']) {
                $response['success'] = false;
                $response['message'] = "You Reached Daily Sync Limit";
                print_r(json_encode($response));
                return false;
            }

            // if($user_black_box == '1'){
            //     $sql = "SELECT * FROM `suspect_codes` WHERE user_id = $user_id AND status = 0 ORDER BY id DESC LIMIT 1";
            //     $db->sql($sql);
            //     $res = $db->getResult();
            //     $num = $db->numRows($res);
            //     if ($num == 1) {
            //         $s_id = $res[0]['id'];
            //         $sql = "UPDATE `suspect_codes` SET  `status` = 1 WHERE `id` = $s_id";
            //         $db->sql($sql);
                    
            //     }else{
            //         $response['success'] = false;
            //         $response['message'] = "You cannot Sync now,please contact admin";
            //         print_r(json_encode($response));
            //         return false;
            
            //     }
            
            // }

            if(($app_version == 18 && $sync_unique_id != $t_sync_unique_id && $sync_unique_id != '') || $app_version != 18){
                $sql = "INSERT INTO transactions (`user_id`,`codes`,`amount`,`datetime`,`type`,`sync_unique_id`)VALUES('$user_id','$codes','$amount','$datetime','$type','$sync_unique_id')";
                $db->sql($sql);
                $res = $db->getResult();
            
                $sql = "UPDATE `users` SET  `today_codes` = today_codes + $codes,`total_codes` = total_codes + $codes,`earn` = earn + $amount,`balance` = balance + $amount,`last_updated` = '$datetime' WHERE `id` = $user_id";
                $db->sql($sql);
   
        

            }
            $mentiondate = '2023-03-13';
            $sql = "SELECT referred_by  FROM users WHERE id = $user_id AND `joined_date` >= '$mentiondate' AND status = 1";
            $db->sql($sql);
            $res = $db->getResult();
            $num = $db->numRows($res);

        }
    }
     
    
    $sql = "SELECT today_codes,total_codes,balance,code_generate,status,referred_by FROM users WHERE id = $user_id ";
    $db->sql($sql);
    $res = $db->getResult();
    
    $response['success'] = true;
    $response['message'] = "Code Added Successfully";
    $response['status'] = $res[0]['status'];
    $response['balance'] = $res[0]['balance'];
    $response['today_codes'] = $res[0]['today_codes'];
    $response['total_codes'] = $res[0]['total_codes'];
    $response['code_generate'] = $res[0]['code_generate'];
    $response['status'] = $res[0]['status'];
    print_r(json_encode($response));
}
else{
  
    $response['success'] = false;
    $response['message'] = "Cannot Sync Right Now, Code Generate is turned off";
    print_r(json_encode($response));
}



?>