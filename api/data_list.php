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

$datetime = date('Y-m-d H:i:s');

$api_name = 'data_list';
$sql_log_api_call = "INSERT INTO api_calls (api_name, datetime) VALUES ('$api_name', '$datetime')";
$db->sql($sql_log_api_call);

$sql = "SELECT * FROM random_data ORDER BY RAND() LIMIT 10";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    $response['success'] = true;
    $response['message'] = "Random Data Listed Successfully";
    
    // Manually override the ID to be from 1 to 10
    $default_id = 1;
    foreach ($res as &$row) {
        $row['id'] = $default_id++;
    }
    
    $response['data'] = $res;
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "No Random Data Found";
    print_r(json_encode($response));
}
?>
