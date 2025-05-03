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

$sql = "SELECT * FROM users WHERE id = $user_id";
$db->sql($sql);
$user = $db->getResult();

if (empty($user)) {
    $response['success'] = false;
    $response['message'] = "User not found";
    print_r(json_encode($response));
    return false;
}

// Get user plan_type
$plan_type = strtolower($user[0]['plan_type']);  // trial, basic, premium

// Map plan_type to expected plan id
$plan_map = array(
    'trial' => 1,
    'basic' => 2,
    'premium' => 3
);

$active_plan_id = isset($plan_map[$plan_type]) ? $plan_map[$plan_type] : 0;

$sql = "SELECT * FROM plan ORDER BY price ASC";
$db->sql($sql);
$res = $db->getResult();

if (!empty($res)) {
    $rows = array();
    foreach ($res as $row) {
        $temp['id'] = $row['id'];
        $temp['name'] = $row['name'];
        $temp['description'] = strip_tags_except($row['description'], array('br'));
        $temp['image'] = DOMAIN_URL . $row['image'];
        $temp['demo_video'] = $row['demo_video'];
        $temp['invite_bonus'] = $row['invite_bonus'];
        $temp['price'] = $row['price'];
        $temp['num_sync'] = $row['num_sync'];

        // Enable logic
        $temp['enable'] = ($row['id'] == $active_plan_id) ? 1 : 0;

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
    $string = str_replace(array("\r", "\n"), '', $string);
    $string = strip_tags($string);
    $string = html_entity_decode($string);
    foreach ($exceptions as $tag) {
        $string = str_replace("#{$tag}#", "<$tag>", $string);
        $string = str_replace("#/{$tag}#", "</$tag>", $string);
    }
    return $string;
}
?>
