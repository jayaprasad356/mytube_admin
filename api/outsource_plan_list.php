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

$sql = "SELECT * FROM outsource_plan WHERE type = '$type' ORDER BY price ASC";
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
        $temp['yearly_earnings'] = $row['yearly_earnings'];
        $temp['per_code_cost'] = $row['per_code_cost'];
        $temp['price'] = $row['price'];
        $temp['type'] = $row['type'];
        $temp['min_refers'] = $row['min_refers'];
        $temp['num_sync'] = $row['num_sync'];
        $temp['sub_description'] = $row['sub_description'];
        $temp['active_link'] = $row['active_link'];
        $temp['sync_cost'] = $row['sync_cost'];
        $temp['refund'] = $row['refund'];
        $temp['refer_refund_amount'] = $row['refer_refund_amount'];
        
        $plan_id = $row['id'];
        $sql_check_plan_1_2_3_6 = "SELECT * FROM outsource_user_plan WHERE user_id = $user_id AND (plan_id = 1 OR plan_id = 2 OR plan_id = 4 OR plan_id = 6)";
        $db->sql($sql_check_plan_1_2_3_6);
        $has_plan_1_or_2_or_3_or_6 = $db->numRows() > 0;
    
        $sql_check_plan = "SELECT * FROM outsource_user_plan WHERE user_id = $user_id AND plan_id = $plan_id";
        $db->sql($sql_check_plan);
        $plan_exists = $db->numRows() > 0;
    
        if ($plan_id == 5 && $has_plan_1_or_2_or_3_or_6){
            $temp['status'] = 2; 
        } else {
            $temp['status'] = $plan_exists ? 1 : 0;
        }
        
        $rows[] = $temp;
    }
    $response['success'] = true;
    $response['message'] = "Outsource Plan Details Listed Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "Outsource Plan not found";
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
