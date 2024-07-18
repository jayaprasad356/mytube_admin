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
    $sql_query = "SELECT *  FROM device_requests WHERE id =" . $ID;
	$db->sql($sql_query);
    $res = $db->getResult();
    $user_id = $res[0]['user_id'];
    $device_id = $res[0]['device_id'];
    $sql_query = "UPDATE users SET `device_id` = '$device_id' WHERE id =" . $user_id;
    $db->sql($sql_query);
	$sql_query = "UPDATE device_requests SET status=1 WHERE id =" . $ID;
	$db->sql($sql_query);
	$res = $db->getResult();
	header("location:manage-devices.php");
?>
