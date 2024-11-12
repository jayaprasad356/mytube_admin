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
$sql_user = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql_user);
$res_user = $db->getResult();
$num = $db->numRows($res_user);

if ($num >= 1) {
    $user_details = $res_user[0];

    $sql_plans = "SELECT plan.* FROM user_plan
    LEFT JOIN plan ON user_plan.plan_id = plan.id
    WHERE user_plan.user_id = $user_id";
    $db->sql($sql_plans);
    $res_plans = $db->getResult();

    // Initialize the response array for associated plans
    $user_details['plan_activated'] = [];

    // Populate plan_activated only with plans the user has, adding DOMAIN_URL to image paths
    foreach ($res_plans as $user_plan) {
    // Check if there's an image and prepend DOMAIN_URL
    if (!empty($user_plan['image'])) {
    $user_plan['image'] = DOMAIN_URL . $user_plan['image']; // Adjust path as needed
    }

    $user_details['plan_activated'][] = $user_plan; // Add full plan details with updated image URL
    }
  

    $response['success'] = true;
    $response['message'] = "Users listed Successfully";
    $response['data'] = array($user_details);
    print_r(json_encode($response));

}else{
    $response['success'] = false;
    $response['message'] = "No Users Found";
    print_r(json_encode($response));

}

?>