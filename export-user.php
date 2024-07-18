<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();


	$sql_query = "SELECT *  FROM users";
	$db->sql($sql_query);
	$developer_records = $db->getResult();
	
	$filename = "Allusers-data".date('Ymd') . ".xls";			
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
