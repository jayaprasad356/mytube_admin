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
date_default_timezone_set('Asia/Kolkata');

if (empty($_POST['user_id'])) {
    $response = [
        'success' => false,
        'message' => "User ID is required"
    ];
    echo json_encode($response);
    exit;
}

$user_id = $db->escapeString($_POST['user_id']);

// ✅ Check global code_generate setting
$sql = "SELECT code_generate FROM settings LIMIT 1";
$db->sql($sql);
$settings = $db->getResult();

if (empty($settings) || (int)$settings[0]['code_generate'] === 0) {
    $response = [
        'success' => false,
        'message' => "Code generation is currently turned off. Please try again later."
    ];
    echo json_encode($response);
    exit;
}

// ✅ Check user-specific code_generate and plan_type
$sql = "SELECT code_generate, plan_type FROM users WHERE id = '$user_id'";
$db->sql($sql);
$res = $db->getResult();

if (empty($res)) {
    $response = [
        'success' => false,
        'message' => "User not found"
    ];
    echo json_encode($response);
    exit;
}

$user_code_generate = (int)$res[0]['code_generate'];
$plan_type = $res[0]['plan_type'];

if ($user_code_generate === 0) {
    $response = [
        'success' => false,
        'message' => "Your code generation access has been disabled. Please contact support."
    ];
    echo json_encode($response);
    exit;
}

$max_daily_limit = 1500;
$codes_per_call = 50;
$per_code_value = 0.25;



// ✅ Get today's date in PHP (not SQL)
$today_date = date('Y-m-d');

// ✅ Fetch total codes GENERATED today (from transactions)
$sql = "SELECT IFNULL(SUM(codes), 0) AS total_generated 
        FROM transactions 
        WHERE user_id = '$user_id' AND type = 'generate' AND DATE(datetime) = '$today_date'";
$db->sql($sql);
$result = $db->getResult();
$total_generated = (int)$result[0]['total_generated'];

// Check if user already reached the daily generate limit
if ($total_generated >= $max_daily_limit) {
    $response = [
        'success' => false,
        'message' => "You have reached your daily generate limit of 1500 codes."
    ];
    echo json_encode($response);
    exit;
}

// Calculate how many codes can be added
$remaining_codes = $max_daily_limit - $total_generated;
$codes_to_add = min($codes_per_call, $remaining_codes);
$amount = $codes_to_add * $per_code_value;

$datetime = date('Y-m-d H:i:s');
$type = 'generate';

// Insert transaction only if at least 1 code can be added
if ($codes_to_add > 0) {
    $db->sql("INSERT INTO transactions (user_id, codes, amount, datetime, type) 
        VALUES ('$user_id', '$codes_to_add', '$amount', '$datetime', '$type')");

    $db->sql("UPDATE users 
        SET today_codes = today_codes + $codes_to_add,
            total_codes = total_codes + $codes_to_add,
            earn = earn + $amount,
            earning_wallet = earning_wallet + $amount,
            last_updated = '$datetime'
        WHERE id = '$user_id'");
}

// Fetch updated user data
$sql = "SELECT today_codes, total_codes, balance FROM users WHERE id = '$user_id'";
$db->sql($sql);
$user_data = $db->getResult();

// Prepare the response message
if ($codes_to_add == $codes_per_call) {
    $message = "50 codes added successfully.";
} elseif ($codes_to_add > 0) {
    $message = "$codes_to_add codes added successfully. You have reached your daily generate limit of 1500 codes.";
} else {
    $message = "You have reached your daily generate limit of 1500 codes.";
}

$response = [
    'success' => true,
    'message' => $message,
    'today_codes' => $user_data[0]['today_codes'],
    'total_codes' => $user_data[0]['total_codes'],
    'balance' => $user_data[0]['balance']
];

echo json_encode($response);

?>
