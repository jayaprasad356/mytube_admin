<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();
date_default_timezone_set('Asia/Kolkata');
$currentdate = date('Y-m-d');

	$join = "WHERE w.user_id = u.id AND w.user_id = b.user_id AND w.status= 0";
	$sql = "SELECT 
            w.id AS id,
            w.*, 
            u.name,
            u.total_codes,
            u.total_referrals,
            u.balance,
            up.plan_id,     
            p.name AS plan_name,
            u.mobile,
            u.referred_by,
            u.refer_code,
            DATEDIFF('$currentdate', u.joined_date) AS history,
            b.branch,
            b.bank,
            CONCAT(',', b.account_num, ',') AS account_num,
            b.ifsc,
            b.holder_name      
        FROM 
            `withdrawals` w
        JOIN 
            `users` u ON w.user_id = u.id
        JOIN 
            `bank_details` b ON w.user_id = b.user_id
        JOIN 
            `user_plan` up ON w.user_id = up.user_id   -- Join user_plan to get plan_id
        JOIN 
            `plan` p ON up.plan_id = p.id
        WHERE 
            w.status = 0 AND up.claim != 0"; 

$db->sql($sql);
$developer_records = $db->getResult();

	
	$filename = "unpaid-withdrawals-data".date('Ymd') . ".xls";			
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"$filename\"");	
	$show_coloumn = false;
	if(!empty($developer_records)) {
	  foreach($developer_records as $record) {
		if(!$show_coloumn) {
		  // display field/column names in first row
		  echo implode("\t", array_keys($record)) . "\n";
		  $show_coloumn = true;
		}
		echo implode("\t", array_values($record)) . "\n";
	  }
	}
	exit;  
?>
