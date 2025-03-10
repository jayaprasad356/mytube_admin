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

$api_name = 'my_plan_list';
$sql_log_api_call = "INSERT INTO api_calls (api_name, datetime) VALUES ('$api_name', '$datetime')";
$db->sql($sql_log_api_call);

// Check if user_id and plan_id are provided
if (empty($_POST['user_id'])) {
    $response['success'] = false;
    $response['message'] = "User ID is Empty";
    print_r(json_encode($response));
    return false;
}

if (empty($_POST['plan_id'])) {
    $response['success'] = false;
    $response['message'] = "Plan ID is Empty";
    print_r(json_encode($response));
    return false;
}

$user_id = $db->escapeString($_POST['user_id']);
$plan_id = $db->escapeString($_POST['plan_id']);

// Fetch user plan details
$sql = "SELECT * FROM user_plan WHERE user_id ='$user_id' AND plan_id ='$plan_id'";
$db->sql($sql);
$res = $db->getResult();
$num = $db->numRows($res);

if ($num >= 1) {
    foreach ($res as $row) {
        $temp['id'] = $row['id'];
        $temp['user_id'] = $row['user_id'];
        $temp['plan_id'] = $row['plan_id'];
        $temp['income'] = $row['income'];
        $temp['joined_date'] = $row['joined_date'];
        $temp['claim'] = $row['claim'];
        $temp['inactive'] = $row['inactive'];

        // Calculate total worked days
        $joined_date = new DateTime($row['joined_date']);
        $current_date = new DateTime();

        // Get the difference in days
        $interval = $joined_date->diff($current_date);
        $worked_days = $interval->days + 1; // Add 1 to include current date

        // Fetch leave days between joined_date and current_date
        $sql_leaves = "SELECT date FROM leaves WHERE date >= '{$joined_date->format('Y-m-d')}' AND date <= '{$current_date->format('Y-m-d')}'";
        $db->sql($sql_leaves);
        $leaves = $db->getResult();

        // Subtract leave days from the total worked days
        $leave_dates = array_map(function($leave) {
            return $leave['date'];
        }, $leaves);

        // Loop through each day between joined_date and current_date to subtract leave days
        $date_period = new DatePeriod($joined_date, new DateInterval('P1D'), $current_date);
        foreach ($date_period as $date) {
            if (in_array($date->format('Y-m-d'), $leave_dates)) {
                $worked_days--; // Reduce worked_days for each leave day
            }
        }

        // Return the worked days value
        $temp['worked_days'] = $worked_days;
        $rows[] = $temp;
    }

    $response['success'] = true;
    $response['message'] = "User Plan Details Retrieved Successfully";
    $response['data'] = $rows;
    print_r(json_encode($response));
} else {
    $response['success'] = false;
    $response['message'] = "Plan Not found";
    print_r(json_encode($response));
}
?>
