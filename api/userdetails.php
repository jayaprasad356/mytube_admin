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

    // Fetch the minimum withdrawal from settings
    $sql_settings = "SELECT min_withdrawal FROM settings WHERE id = 1";
    $db->sql($sql_settings);
    $res_settings = $db->getResult();
    $min_withdrawal = $res_settings[0]['min_withdrawal'];

    $user_details['min_withdrawal'] = $min_withdrawal;

    // Fetch user plans
    $sql_plans = "SELECT plan.* FROM user_plan
                  LEFT JOIN plan ON user_plan.plan_id = plan.id
                  WHERE user_plan.user_id = $user_id";
    $db->sql($sql_plans);
    $res_plans = $db->getResult();

    // Initialize the response array for associated plans
    $user_details['plan_activated'] = [];
    $free_plan = 0; // Default value for free_plan
    $paid_plan = 0; // Default value for paid_plan

    // Populate plan_activated and check for free_plan and paid_plan
    if (!empty($res_plans)) {
        foreach ($res_plans as $user_plan) {
            // Check if there's an image and prepend DOMAIN_URL
            if (!empty($user_plan['image'])) {
                $user_plan['image'] = DOMAIN_URL . $user_plan['image']; // Adjust path as needed
            }

            // Check if the plan_id is 5 and claim is 1 for free_plan
            if ($user_plan['id'] == 5 && $user_plan['claim'] == 1) {
                $free_plan = 1;
            }

            // Check if the plan_id is 1, 2, 4, or 6 and claim is 1 for paid_plan
            if (in_array($user_plan['id'], [1, 2, 4, 6]) && $user_plan['claim'] == 1) {
                $paid_plan = 1;
            }

            $user_details['plan_activated'][] = $user_plan; // Add full plan details with updated image URL
        }
    }

    // Set the free_plan and paid_plan flags
    $user_details['free_plan'] = $free_plan;
    $user_details['paid_plan'] = $paid_plan;

    // Fetch extra claim plans for the user
    $sql_extra_plans = "SELECT extra_claim_plan.* FROM user_extra_claim_plan
                        LEFT JOIN extra_claim_plan ON user_extra_claim_plan.extra_claim_plan_id = extra_claim_plan.id
                        WHERE user_extra_claim_plan.user_id = '$user_id'";
    $db->sql($sql_extra_plans);
    $res_extra_plans = $db->getResult();
    $user_details['extra_plan_activated'] = $res_extra_plans;

    $response['success'] = true;
    $response['message'] = "Users listed Successfully";
    $response['data'] = array($user_details);
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "No Users Found";
    print_r(json_encode($response));
}
?>
