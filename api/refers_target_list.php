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

$sql = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql);
$user = $db->getResult();

if (empty($user)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    print_r(json_encode($response));
    return false;
}

$sql = "SELECT * FROM refers_target";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    foreach ($res as &$refer) {
        $refer_id = $refer['id'];
        $sql = "SELECT * FROM refer_counts WHERE user_id = $user_id AND refer_id = $refer_id";
        $db->sql($sql);
        $refer_count = $db->getResult();

        if (!empty($refer_count)) {
            $refer['status'] = 1;
        } else {
            $refer['status'] = 0;
        }
    }

    // Return the final data with statuses
    $response['success'] = true;
    $response['message'] = "Refers Target Listed Successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
} else {
    // If no records found in refers_target
    $response['success'] = false;
    $response['message'] = "No Data Found";
    print_r(json_encode($response));
}
?>
