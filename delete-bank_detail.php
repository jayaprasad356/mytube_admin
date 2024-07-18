<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();
	
	if (isset($_GET['id'])) {
		$ID = $db->escapeString($_GET['id']);
	} else {
		// $ID = "";
		return false;
		exit(0);
	}
	$data = array();

	$sql_query = "DELETE  FROM bank_details WHERE id =" . $ID;
	$db->sql($sql_query);
	$res = $db->getResult();
	header("location:bank_details.php");
?>
