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

// Fetch total_referrals from users table
$sql = "SELECT total_referrals FROM users WHERE id = $user_id";
$db->sql($sql);
$user = $db->getResult();

if (empty($user)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    print_r(json_encode($response));
    return false;
}

$total_referrals = (int)$user[0]['total_referrals'];

// Fetch all refers_target
$sql = "SELECT * FROM refers_target";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    foreach ($res as &$refer) {
        $refer_id = (int)$refer['id'];
        $refer['status'] = 0;

        // Check total_referrals conditions
        if (($total_referrals >= 5 && $total_referrals <= 9) && $refer_id == 1) {
            $refer['status'] = 1;
        } elseif (($total_referrals >= 10 && $total_referrals <= 19) && in_array($refer_id, [1, 2])) {
            $refer['status'] = 1;
        } elseif (($total_referrals >= 20 && $total_referrals <= 29) && in_array($refer_id, [1, 2, 3])) {
            $refer['status'] = 1;
        } elseif ($total_referrals >= 30 && in_array($refer_id, [1, 2, 3, 4])) {
            $refer['status'] = 1;
        }

        // Check refer_counts table for user_id and refer_id
        $sql_check = "SELECT * FROM refer_counts WHERE user_id = $user_id AND refer_id = $refer_id";
        $db->sql($sql_check);
        $refer_count = $db->getResult();

        if (!empty($refer_count)) {
            $refer['status'] = 2; // Set status to 2 if record exists in refer_counts
        }
    }

    $response['success'] = true;
    $response['message'] = "Refers Target Listed Successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "No Data Found";
    print_r(json_encode($response));
}
?>
