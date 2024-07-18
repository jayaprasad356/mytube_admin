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
include_once('../includes/functions.php');
$fn = new functions;
$fn->monitorApi('updateuser');


$sql = "UPDATE users SET sync_refer_wallet = 0";
$db->sql($sql);
$sql = "SELECT referred_by,total_codes FROM users WHERE joined_date>='2023-02-05' AND status=1 AND referred_by != ''";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    foreach($res as $row){
        $amount=$row['total_codes']*0.01;
        $referred_by=$row['referred_by'];
        $sql = "UPDATE users SET sync_refer_wallet =sync_refer_wallet + $amount WHERE refer_code='$referred_by'";
        $db->sql($sql);
       }

    $response['success'] = true;
    $response['message'] = "Sync Wallet Updated Successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
    return false;
}
else{
    
    $response['success'] = false;
    $response['message'] ="User Not Found";
    print_r(json_encode($response));
    return false;

}

?>