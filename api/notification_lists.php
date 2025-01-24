<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');
date_default_timezone_set('Asia/Kolkata');
$db = new Database();
$db->connect();

$datetime = date('Y-m-d H:i:s');

$api_name = 'notification_lists';
$sql_log_api_call = "INSERT INTO api_calls (api_name, datetime) VALUES ('$api_name', '$datetime')";
$db->sql($sql_log_api_call);

$sql = "SELECT * FROM `notifications` ORDER BY id DESC LIMIT 20 ";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    foreach ($res as $row) {
        $temp['id'] = $row['id'];
        $temp['title'] = $row['title'];
        $temp['description'] = $row['description'];
        $temp['link'] = $row['link'];
        $temp['datetime'] = $row['datetime'];
        $temp['image'] = DOMAIN_URL . $row['image'];
        $rows[] = $temp;
   }
    $response['success'] = true;
    $response['message'] = "Notification listed Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Data Found";
    print_r(json_encode($response));

}

?>