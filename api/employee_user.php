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


if (empty($_POST['employee_id'])) {
    $response['success'] = false;
    $response['message'] = "Employee Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User Id is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);
$employee_id = $db->escapeString($_POST['employee_id']);


$sql = "INSERT INTO employee_users (`employee_id`,`user_id`) VALUES ('$employee_id','$user_id')";
$db->sql($sql);
$res = $db->getResult();
$response['success'] = true;
$response['message'] = "Employee User Added Successfully";
print_r(json_encode($response));

?>