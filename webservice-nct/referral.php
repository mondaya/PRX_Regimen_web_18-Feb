<?php
/*
	developer: ntc
	code:manage referral 
	date:17102016
*/
	require_once("../includes-nct/config-nct.php");
	require_once("general-functions.php");
	$request_data=$_REQUEST;
	$userId = isset($request_data['userId'])?$request_data['userId']:0;
	$curCode =isset($request_data['currencyCode'])?$request_data['currencyCode']:$currencyCode;
	$page = isset($request_data['page'])?$request_data['page']:1;
	$limit = isset($request_data['limit'])?$request_data['limit']:8;
	$email =isset($request_data['email'])?$request_data['email']:'';
	
	if(isset($request_data['action']) && strtolower($request_data['action'])=='statistics'){
		$json_content = statistics_api($userId,$curCode);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='sendrefrequest'){
		$json_content = referral_request_api($userId,$email);
		echo $json_content;
	}else{
		$res_array['status']=false;
		$res_array['message']='Oops! Something wrong. Please try again.';    
   		echo json_encode($res_array);
	}

?>
