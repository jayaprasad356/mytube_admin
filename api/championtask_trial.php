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

if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}
$user_id = $db->escapeString($_POST['user_id']);
$sql = "SELECT id,trial_count FROM  users WHERE id='$user_id'";
$db->sql($sql);
$res = $db->getResult();
$trial_count=$res[0]['trial_count'];
if ($trial_count< 10) {
    $sql = "UPDATE users SET trial_count=trial_count+1  WHERE id = '$user_id'";
    $db->sql($sql);
    $res = $db->getResult();
    $response['success'] = true;
    $response['message'] = "Trial Added Successfully";
    $response['trial_count'] = $trial_count+1;
    print_r(json_encode($response));

}else{
    $sql = "UPDATE users SET trial_expired=1  WHERE id = '$user_id'";
    $db->sql($sql);
    $response['success'] = false;
    $response['message'] = "Your Trial Period Expired";
    $response['trial_expired'] = "1";
    $response['trial_count'] = $trial_count;
    print_r(json_encode($response));

}

?>