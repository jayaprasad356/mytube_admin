<?php 
session_start();
ini_set('display_errors', 1);
//importing required files
require_once 'includes/crud.php';
$db_con=new Database();
$db_con->connect();
date_default_timezone_set('Asia/Kolkata');
require_once 'includes/functions.php';
require_once('includes/firebase.php');
require_once ('includes/push.php');


$fnc = new functions;

include_once('includes/custom-functions.php');
    
$fn = new custom_functions;

$response = array(); 

if($_SERVER['REQUEST_METHOD']=='POST'){	
	//hecking the required params 
	if(isset($_POST['title']) and isset($_POST['description'])) {

		//creating a new push
		$title = $db_con->escapeString($fn->xss_clean($_POST['title']));
		$message = $db_con->escapeString($fn->xss_clean($_POST['description']));
		$link = $db_con->escapeString($fn->xss_clean($_POST['link']));
		$datetime = date('Y-m-d H:i:s');
		$id = "0";
		$type = "default";
		/*dynamically getting the domain of the app*/
		$url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$url .= $_SERVER['SERVER_NAME'];
		$url .= $_SERVER['REQUEST_URI'];
		$server_url = dirname($url).'/';
		
		$push = null;
		$sql = "INSERT INTO notifications (title,description,datetime,link)VALUES('$title','$message','$datetime','$link')";
		$db_con->sql($sql);
		$db_con->getResult();
		//first check if the push has an image with it
		$push = new Push(
			$db_con->escapeString($fn->xss_clean($_POST['title'])),
			$db_con->escapeString($fn->xss_clean($_POST['description'])),
			null,
			$type,
			$id
		);

		//getting the push from push object
		$mPushNotification = $push->getPush();
		
		//getting the token from database object 
        $devicetoken = $fnc->getAllTokens();
        //$devicetoken1 = $fnc->getAllTokens("devices");
        //$final_tokens = array_merge($devicetoken,$devicetoken1);
        $f_tokens = array_unique($devicetoken);
		$devicetoken_chunks = array_chunk($f_tokens,1000);
		foreach($devicetoken_chunks as $devicetokens){
			//creating firebase class object 
			$firebase = new Firebase(); 

			//sending push notification and displaying result 
			$firebase->send($devicetokens, $mPushNotification);
		}
		$response['error'] = false;
// 		$response['message'] = $firebase->send($devicetoken, $mPushNotification);
		$response["message"] = "<span class='label label-success'>Notification Sent Successfully!</span>";
	}else{
		$response['error']=true;
		$response['message']='Parameters missing';
	}
}else{
	$response['error']=true;
	$response['message']='Invalid request';
}
// echo str_replace("\\/","/",json_encode($response['message']));
echo(json_encode($response));

?>
