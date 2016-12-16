<?php
require './firebase/firebaseLib.php';

use firebase\auth\tokenGenerator;

const DEFAULT_URL = 'https://peardevmtn.firebaseio.com/';
const DEFAULT_TOKEN = 'AFJzWwP7W7YYDNjRGp0NywKVQTG8eBFU7WSNnQ6V';					   

//print_r($_REQUEST);exit;

send_push();

function get_token($user_id){
	$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
	
	try{
		$token = $firebase->get('/tokens/'.$user_id.'/token');
	}catch(Exception $ex){
		echo $ex.getMessage();
	}
	return $token;
}

function send_push(){
	
	if(!array_key_exists("user_id",$_REQUEST)){
		echo"ERROR";exit;
	}
	if($_REQUEST['user_id'] == ''){
		echo"User Id is null.";exit;
	}
	$token = get_token($_REQUEST['user_id']);
	$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
	
	$tokenarray = array($token);

	// API access key from Google API's Console
	define( 'API_ACCESS_KEY', 'AAAAqV_xdcc:APA91bHbPviaFD87LF98ylVJQirUpd1Pr7Muuk5doiliPAp0dOTpZ0ZYjrP4YphzgyfTxEhjDdsG1VSlhcm32zHZfG9SsFYE8jTaFVWq0-BVcFZ4gd0dNFZ8wECTWVGF6-1CMNoffrV5YVYi_p5j8mQo9c508yYdCA' );
	
	// prep the bundle
	$msg = array
	(
		'body' 	=> $_REQUEST['body'],
		'title'		=> $_REQUEST['title'],
		'vibrate'	=> 1,
		'sound'		=> 1,
	);
	
	$fields = array
	(
		'registration_ids' => $tokenarray,
		'notification' => $msg
	);
	 
	$headers = array
	(
		'Authorization: key='.API_ACCESS_KEY,
		'Content-Type: application/json'
	);
	 
	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
	curl_setopt( $ch,CURLOPT_POST, true );
	curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	$result = curl_exec($ch );
	curl_close( $ch );
	echo $result;
}