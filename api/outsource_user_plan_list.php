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
    $response['message'] = "User ID is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);

$sql = "SELECT * FROM users WHERE id = $user_id ";
$db->sql($sql);
$user = $db->getResult();

if (empty($user)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    print_r(json_encode($response));
    return false;
}

$sql = "SELECT outsource_user_plan.* ,outsource_plan.name,outsource_plan.image,outsource_plan.demo_video,outsource_plan.monthly_codes,outsource_plan.per_code_cost,outsource_plan.price,outsource_plan.monthly_earnings,outsource_plan.sync_cost,outsource_plan.yearly_earnings
        FROM outsource_user_plan 
        LEFT JOIN outsource_plan ON outsource_user_plan.plan_id = outsource_plan.id
        WHERE outsource_user_plan.user_id = '$user_id'";

$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);


if ($num >= 1) {
    foreach ($res as &$job) {
        $imagePath = $job['image'];
        $imageURL = DOMAIN_URL . $imagePath;
        $job['image'] = $imageURL;

       
    }

    $response['success'] = true;
    $response['message'] = "Outsource User Plan Details Retrieved Successfully";
    $response['data'] = $res;
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] = "Plan Not found";
    print_r(json_encode($response));

}
?>