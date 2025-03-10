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

if (empty($_POST['email'])) {
    $response['success'] = false;
    $response['message'] = "Email Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['password'])) {
    $response['success'] = false;
    $response['message'] = "Password is Empty";
    print_r(json_encode($response));
    return false;
}

$email = $db->escapeString($_POST['email']);
$password = $db->escapeString($_POST['password']);


$sql = "SELECT * FROM employees WHERE email ='$email' AND password ='$password'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
        $response['success'] = true;
        $response['message'] = "Logged In successfully";
        $response['data'] = $res;
        print_r(json_encode($response));

} else {
    $response['success'] = false;
    $response['message'] = "Invalid Credentials or Employee Not Found";
    print_r(json_encode($response));
}
?>