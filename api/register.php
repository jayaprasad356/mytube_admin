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
date_default_timezone_set('Asia/Kolkata');

// Validation for input fields
if (empty($_POST['name'])) {
    $response['success'] = false;
    $response['message'] = "Name is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['mobile'])) {
    $response['success'] = false;
    $response['message'] = "Mobile number is Empty";
    print_r(json_encode($response));
    return false;
}

// Remove any non-numeric characters from the mobile number
$mobileNumber = preg_replace('/[^0-9]/', '', $_POST['mobile']);

// Check if the mobile number starts with '0'
if (substr($mobileNumber, 0, 1) === '0') {
    $response['success'] = false;
    $response['message'] = "Mobile number cannot start with '0'";
    print_r(json_encode($response));
    return false;
}

// Check if the length of the mobile number is exactly 10 digits
if (strlen($mobileNumber) !== 10) {
    $response['success'] = false;
    $response['message'] = "Mobile number should be exactly 10 digits, please remove if +91 is there";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['password'])) {
    $response['success'] = false;
    $response['message'] = "Password is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['email'])) {
    $response['success'] = false;
    $response['message'] = "Email Id is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['city'])) {
    $response['success'] = false;
    $response['message'] = "City Name is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['dob'])) {
    $response['success'] = false;
    $response['message'] = "Date of Birth is Empty";
    print_r(json_encode($response));
    return false;
}
if (empty($_POST['device_id'])) {
    $response['success'] = false;
    $response['message'] = "Device Id is Empty";
    print_r(json_encode($response));
    return false;
}

// Escape input data to prevent SQL injection
$name = $db->escapeString($_POST['name']);
$mobile = $db->escapeString($_POST['mobile']);
$email = $db->escapeString($_POST['email']);
$password = $db->escapeString($_POST['password']);
$city = $db->escapeString($_POST['city']);
$referred_by = $db->escapeString($_POST['referred_by']);
// $referred_by = (isset($_POST['referred_by']) && !empty($_POST['referred_by'])) ? $db->escapeString($_POST['referred_by']) : "";
$dob = $db->escapeString($_POST['dob']);
$device_id = $db->escapeString($_POST['device_id']);
$c_referred_by = '';

if ($referred_by) {
    // Check if the provided refer_code exists
    $sql = "SELECT id, referred_by FROM users WHERE refer_code = BINARY '$referred_by'";
    $db->sql($sql);
    $res = $db->getResult();
    $num = $db->numRows($res);
    
    if ($num == 0) {
        $response['success'] = false;
        $response['message'] = "Invalid Referred By";
        print_r(json_encode($response));
        return false;
    } else {
        // Get the referred_by of the refer_code owner
        $c_referred_by = $res[0]['referred_by'];
    }
}
// Check if user with the same device_id already exists
$sql = "SELECT id FROM users WHERE device_id='$device_id'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $response['success'] = false;
    $response['message'] = "User Already Registered with this device, kindly register with a new device";
    print_r(json_encode($response));
    return false;
}

// Check if user with the same mobile number already exists
$sql = "SELECT * FROM users WHERE mobile='$mobile'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);
if ($num >= 1) {
    $response['success'] = false;
    $response['message'] = "Mobile Number Already Exists";
    print_r(json_encode($response));
    return false;
} else {
    // Insert user data without refer_code
    $datetime = date('Y-m-d H:i:s');
    $sql = "INSERT INTO users (`name`, `mobile`, `email`, `password`, `city`, `dob`, `referred_by`, `c_referred_by`, `device_id`, `last_updated`,`joined_date`)
            VALUES ('$name', '$mobile', '$email', '$password', '$city', '$dob', '$referred_by', '$c_referred_by', '$device_id', '$datetime', '$datetime')";
    $db->sql($sql);

     // Get the ID of the inserted user
     $sql = "SELECT id FROM users WHERE mobile = '$mobile'";
     $db->sql($sql);
     $res = $db->getResult();
     $userId = $res[0]['id'];
 
     // Generate refer code based on user ID
     $refer_code = 'ID' . str_pad($userId, 2, '0', STR_PAD_LEFT);
 
     // Update the refer code for the user
     $sql = "UPDATE users SET refer_code = '$refer_code' WHERE id = '$userId'";
     $db->sql($sql);
 
     $sql = "SELECT * FROM users WHERE mobile = '$mobile'";
     $db->sql($sql);
     $res = $db->getResult();
 
     $response['success'] = true;
     $response['message'] = "Successfully Registered";
     $response['data'] = $res;
     print_r(json_encode($response));
 }
 ?>
