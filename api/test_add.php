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

if (empty($_POST['data'])) {
    $response['success'] = false;
    $response['message'] = "Data is Empty";
    print_r(json_encode($response));
    return false;
}
$datetime = date('Y-m-d H:i:s');
foreach ($_POST['data'] as $data) {
    if (empty($data['id'])) {
        $response['success'] = false;
        $response['message'] = "User Id is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($data['name'])) {
        $response['success'] = false;
        $response['message'] = "Name is Empty";
        print_r(json_encode($response));
        return false;
    }
    if (empty($data['email'])) {
        $response['success'] = false;
        $response['message'] = "Email is Empty";
        print_r(json_encode($response));
        return false;
    }
    $user_id = $db->escapeString($data['id']);
    $name = $db->escapeString($data['name']);
    $email = $db->escapeString($data['email']);
    $sql = "SELECT * FROM `test_users` WHERE user_id = $user_id";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    if ($num >= 1) {
        $sql = "UPDATE `test_users` SET name = '$name', email = '$email', datetime = '$datetime' WHERE user_id = $user_id";
        $db->sql($sql);

    }else{
        $sql = "INSERT INTO `test_users` (user_id,name,email,datetime) VALUES ($user_id,'$name','$email','$datetime')";
        $db->sql($sql);
        }

}

$response['success'] = true;
$response['message'] = "Data Inserted Successfully";
$response['data'] = $_POST['data'];
print_r(json_encode($response));
?>