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
$user_id = $db->escapeString($_POST['user_id']);
$amount = $db->escapeString($_POST['amount']);
$sql = "SELECT min_sync_refer_wallet FROM settings WHERE id='1'";
$db->sql($sql);
$result = $db->getResult();
$min_sync_refer_wallet = $result[0]['min_sync_refer_wallet'];
$datetime = date('Y-m-d H:i:s');
if($amount>=$min_sync_refer_wallet){
         $sql = "SELECT sync_refer_wallet FROM users WHERE id='$user_id'";
         $db->sql($sql);
         $res = $db->getResult();
         $sync_refer_wallet=$res[0]['sync_refer_wallet'];
         $refer_income=$res[0]['refer_income'];
         if($amount<= $sync_refer_wallet){
            $sql = "INSERT INTO transactions (`user_id`,`codes`,`amount`,`datetime`,`type`)VALUES('$user_id','0','$amount','$datetime','refer_bonus')";
            $db->sql($sql);
            $sql = "UPDATE `users` SET `sync_refer_wallet` = sync_refer_wallet - $amount,`earn`=earn + $amount,`balance`=balance + $amount,`refer_income`= refer_income + $amount WHERE id = '$user_id'";
            $db->sql($sql);
            $response['success'] = true;
            $response['message'] = "Successfully Transfered";
            $response['refer_income'] =$refer_income;
            $response['sync_refer_wallet'] =$sync_refer_wallet;
            print_r(json_encode($response));

         }
         else{
                    $response['success'] = false;
                    $response['message'] = "Insufficient Balance in Sync Refer Wallet";
                    print_r(json_encode($response));

         }


}
else{
    $response['success'] = false;
    $response['message'] = "Minimum Transfered Amount is $min_sync_refer_wallet";
    print_r(json_encode($response));

}

?>
