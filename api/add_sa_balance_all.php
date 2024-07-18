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

$mentiondate = '2023-03-13';
$datetime = date('Y-m-d H:i:s');
$sql = "SELECT id,referred_by  FROM users WHERE `joined_date` >= '$mentiondate' AND status = 1 AND referred_by != ''";
$db->sql($sql);
$res = $db->getResult();
foreach($res as $row){
    $referred_by=$row['referred_by'];
    $refer_user_id=$row['id'];
    $sql = "UPDATE users SET `salary_advance_balance`=salary_advance_balance + 200,`sa_refer_count`=sa_refer_count + 1 WHERE refer_code='$referred_by'";
    $db->sql($sql);
    $sql = "SELECT id  FROM users WHERE refer_code='$referred_by'";
    $db->sql($sql);
    $ures = $db->getResult();
    $num = $db->numRows($res);
    if ($num >= 1) {
        $user_id = $ures[0]['id'];
        $sql_query = "INSERT INTO salary_advance_trans (user_id,refer_user_id,amount,datetime,type)VALUES($user_id,$refer_user_id,'200','$datetime','credit')";
        $db->sql($sql_query);
        
    }

}

$response['success'] = true;
$response['message'] = "Salary Advance Successfully Done";
print_r(json_encode($response));
?>
