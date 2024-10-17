

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

// Check if required POST parameters are provided
if (empty($_POST['mobile'])) {
    $response['success'] = false;
    $response['message'] = "Mobile is Empty";
    echo json_encode($response);
    return false;
}
if (empty($_POST['device_id'])) {
    $response['success'] = false;
    $response['message'] = "Device Id is Empty";
    echo json_encode($response);
    return false;
}
if (empty($_POST['password'])) {
    $response['success'] = false;
    $response['message'] = "Password is Empty";
    echo json_encode($response);
    return false;
}

$mobile = $db->escapeString($_POST['mobile']);
$device_id = $db->escapeString($_POST['device_id']);
$password = $db->escapeString($_POST['password']);

// Check if the user exists
$sql = "SELECT * FROM users WHERE mobile = '$mobile'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num == 1) {
    // User exists, check for device_id
    $sql_query = "UPDATE users SET device_id = '$device_id' WHERE mobile = '$mobile' AND device_id = ''";
    $db->sql($sql_query);
    
    // Check for valid login credentials
    $sql = "SELECT * FROM users WHERE mobile = '$mobile' AND password = '$password'";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    
    if ($num == 1) {
        $response['success'] = true;
        $response['registered'] = true;
        $response['message'] = "Logged In Successfully";
        $response['data'] = $res;
    } else {
        $response['success'] = true;
        $response['registered'] = false;
        $response['message'] = "Password is incorrect";
    }
} else {
    $sql = "INSERT INTO devices (`mobile`, `device_id`) VALUES ('$mobile', '$device_id')";
    $db->sql($sql);
    
    $response['success'] = false;
    $response['registered'] = false;
    $response['message'] = "Mobile Number Not Registered.";
}

echo json_encode($response);
