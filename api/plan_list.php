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
$type = isset($_POST['type']) ? $db->escapeString($_POST['type']) : 'jobs';

$sql = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql);
$user = $db->getResult();

if (empty($user)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    print_r(json_encode($response));
    return false;
}

$sql = "SELECT * FROM plan WHERE type = '$type' ORDER BY price ASC";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    foreach ($res as $row) {
        $temp['id'] = $row['id'];
        $temp['name'] = $row['name'];
        // Remove all HTML tags except for <br>
        $temp['description'] = strip_tags_except($row['description'], array('br'));
        $temp['image'] = DOMAIN_URL . $row['image'];
        $temp['demo_video'] = $row['demo_video'];
        $temp['monthly_codes'] = $row['monthly_codes'];
        $temp['monthly_earnings'] = $row['monthly_earnings'];
        $temp['per_code_cost'] = $row['per_code_cost'];
        $temp['price'] = $row['price'];
        $temp['type'] = $row['type'];
        $temp['min_refers'] = $row['min_refers'];
        $temp['num_sync'] = $row['num_sync'];
        $temp['sub_description'] = $row['sub_description'];
        $temp['active_link'] = $row['active_link'];
        $temp['refund'] = $row['refund'];
        $temp['refer_refund_amount'] = $row['refer_refund_amount'];

        $plan_id = $row['id'];
        $sql_check_plan_1_2_4_6 = "SELECT * FROM user_plan WHERE user_id = $user_id AND (plan_id = 1 OR plan_id = 2 OR plan_id = 4 OR plan_id = 6)";
        $db->sql($sql_check_plan_1_2_4_6);
        $has_plan_1_or_2_or_4_or_6 = $db->numRows() > 0;
    
        $sql_check_plan = "SELECT * FROM user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
        $db->sql($sql_check_plan);
        $plan_exists = $db->numRows() > 0;
    
        if ($plan_id == 5 && $has_plan_1_or_2_or_4_or_6){
            $temp['status'] = 2; 
        } else {
            $temp['status'] = $plan_exists ? 1 : 0;
        }

        // Fetch joined_date only if the user has an active plan
        $sql_joined_date = "SELECT joined_date FROM user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
        $db->sql($sql_joined_date);
        $user_plan = $db->getResult();

        if (!empty($user_plan) && $plan_exists) { // Ensure the user has this plan activated
            $joined_date = new DateTime($user_plan[0]['joined_date']);
            $current_date = new DateTime();

            // Calculate difference in days
            $interval = $joined_date->diff($current_date);
            $worked_days = $interval->days + 1; // Include current date

            // Fetch leave days between joined_date and current_date
            $sql_leaves = "SELECT date FROM leaves WHERE date >= '{$joined_date->format('Y-m-d')}' AND date <= '{$current_date->format('Y-m-d')}'";
            $db->sql($sql_leaves);
            $leaves = $db->getResult();

            // Convert leave records to an array of leave dates
            $leave_dates = array_map(function ($leave) {
                return $leave['date'];
            }, $leaves);

            // Loop through each day and subtract leave days
            $date_period = new DatePeriod($joined_date, new DateInterval('P1D'), $current_date);
            foreach ($date_period as $date) {
                if (in_array($date->format('Y-m-d'), $leave_dates)) {
                    $worked_days--; // Reduce worked_days for each leave day
                }
            }

            // Assign worked_days to response
            $temp['worked_days'] = $worked_days;
        } else {
            // If the user has not activated this plan, set worked_days as 0
            $temp['worked_days'] = 0;
        }

        
        $rows[] = $temp;
    }
    $response['success'] = true;
    $response['message'] = "Plan Details Listed Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "Plan not found";
    print_r(json_encode($response));
}

function strip_tags_except($string, $exceptions = array()) {
    foreach ($exceptions as $tag) {
        $string = str_replace("<$tag>", "#{$tag}#", $string);
        $string = str_replace("</$tag>", "#/{$tag}#", $string);
    }
    // Remove HTML tags and their attributes
    // Remove \r\n characters
    $string = str_replace(array("\r", "\n"), '', $string);
    $string = strip_tags($string);
    // Decode HTML entities to symbols
    $string = html_entity_decode($string);
    foreach ($exceptions as $tag) {
        $string = str_replace("#{$tag}#", "<$tag>", $string);
        $string = str_replace("#/{$tag}#", "</$tag>", $string);
    }
    return $string;
}
?>
