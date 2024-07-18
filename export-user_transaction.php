<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();

$user_id = $_GET['user_id'];
$sql_query = "SELECT t.codes,t.amount,t.datetime,u.mobile,u.name,u.joined_date  FROM transactions t,users u WHERE t.user_id = u.id AND t.user_id='$user_id'";
	$db->sql($sql_query);
	$developer_records = $db->getResult();
	
	$filename = "All-transactions-data".date('Ymd') . ".xls";			
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
