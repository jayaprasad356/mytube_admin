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

$datetime = date('Y-m-d H:i:s');

if (empty($_POST['user_id'])) {
    $response = ['success' => false, 'message' => "User Id is Empty"];
    echo json_encode($response);
    exit;
}

$user_id = $db->escapeString($_POST['user_id']);
$sql_user = "SELECT * FROM users WHERE id = '$user_id'";
$db->sql($sql_user);
$res_user = $db->getResult();

if (!empty($res_user)) {
    $user_details = $res_user[0];

    // Fetch settings
    $sql_settings = "SELECT min_withdrawal, ad_link FROM settings WHERE id = 1";
    $db->sql($sql_settings);
    $res_settings = $db->getResult();
    $user_details['min_withdrawal'] = $res_settings[0]['min_withdrawal'] ?? 0;
    $user_details['ad_link'] = $res_settings[0]['ad_link'] ?? '';

    // Calculate worked days (excluding leaves)
    $joined_date = $user_details['joined_date'];
    $today_date = date('Y-m-d');
    if ($joined_date) {
        $joined = new DateTime($joined_date);
        $today = new DateTime($today_date);
        $interval = $joined->diff($today);
        $total_days = $interval->days + 1;

        $sql_leaves = "SELECT COUNT(*) AS leave_count FROM leaves WHERE date >= '$joined_date' AND date <= '$today_date'";
        $db->sql($sql_leaves);
        $res_leaves = $db->getResult();
        $leave_count = $res_leaves[0]['leave_count'] ?? 0;

        $worked_days = $total_days - $leave_count;
    } else {
        $worked_days = 0;
    }
    $user_details['worked_days'] = $worked_days;

    // Fetch user plans
    $sql_plans = "SELECT plan.*, user_plan.claim FROM user_plan
                  LEFT JOIN plan ON user_plan.plan_id = plan.id
                  WHERE user_plan.user_id = '$user_id'";
    $db->sql($sql_plans);
    $res_plans = $db->getResult();

    $user_details['plan_activated'] = [];
    $free_plan = 0;
    $paid_plan = 0;

    if (!empty($res_plans)) {
        foreach ($res_plans as &$user_plan) {
            if (!empty($user_plan['image'])) {
                $user_plan['image'] = DOMAIN_URL . $user_plan['image'];
            }

            if ((int)$user_plan['id'] === 5 && (int)$user_plan['claim'] === 1) {
                $free_plan = 1;
            }

            if (in_array((int)$user_plan['id'], [1, 2, 4, 6]) && (int)$user_plan['claim'] === 1) {
                $paid_plan = 1;
            }

            $user_details['plan_activated'][] = $user_plan;
        }
    }

    $user_details['free_plan'] = $free_plan;
    $user_details['paid_plan'] = $paid_plan;

    // Extra claim plans
    $sql_extra_plans = "SELECT extra_claim_plan.* FROM user_extra_claim_plan
                        LEFT JOIN extra_claim_plan ON user_extra_claim_plan.extra_claim_plan_id = extra_claim_plan.id
                        WHERE user_extra_claim_plan.user_id = '$user_id'";
    $db->sql($sql_extra_plans);
    $res_extra_plans = $db->getResult();
    $user_details['extra_plan_activated'] = !empty($res_extra_plans) ? $res_extra_plans : [];

    $response = [
        'success' => true,
        'message' => "User details fetched successfully",
        'data' => [$user_details]
    ];
    echo json_encode($response);
} else {
    $response = ['success' => false, 'message' => "No Users Found"];
    echo json_encode($response);
}
?>
