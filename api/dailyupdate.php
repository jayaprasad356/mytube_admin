<?php 
header('Access-Control-Allow-Origin: *'); 
header("Content-Type: application/json"); 
header("Expires: 0"); 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache"); 
date_default_timezone_set('Asia/Kolkata');  

include_once('../includes/crud.php');  
$db = new Database(); 
$db->connect();  

include_once('../includes/functions.php'); 
$fn = new functions;  

$sql = "SELECT id, joined_date, plan_id FROM user_plan";  
$db->sql($sql); 
$users = $db->getResult();  

$currentDate = new DateTime();  

foreach ($users as $user) {     
    $userId = $user['id'];     
    $planId = $user['plan_id'];      

    // Check only for plan_id 5
    if ($planId != 5) {
        continue;
    }

    $joinedDate = new DateTime($user['joined_date']);      
    $interval = $currentDate->diff($joinedDate);     
    $totalDays = $interval->days;      

    // Fetch leave days     
    $leaveSql = "SELECT COUNT(*) AS leave_days FROM leaves WHERE date BETWEEN '{$user['joined_date']}' AND '{$currentDate->format('Y-m-d')}'";     
    $db->sql($leaveSql);     
    $leaveResult = $db->getResult();     
    $leaveDays = $leaveResult[0]['leave_days'];      

    $workedDays = $totalDays - $leaveDays;      

    $threshold = 2;      

    if ($workedDays >= $threshold) {         
        $updateSql = "UPDATE user_plan SET claim = 0 WHERE id = $userId";         
        $db->sql($updateSql);     
    } 
}  

$sql = "UPDATE outsource_user_plan SET claim = 1";
$db->sql($sql);

// Reset today's codes
$sql = "UPDATE users SET today_codes = 0"; 
$db->sql($sql); 
?>
