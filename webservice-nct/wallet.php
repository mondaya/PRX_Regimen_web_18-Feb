<?php
/*	developer: ntc
	code:manage wallet
	date:15102016
*/
	require_once("../includes-nct/config-nct.php");
	require_once("general-functions.php");
	$request_data=$_REQUEST;
	$userId = isset($request_data['userId'])?$request_data['userId']:0;
	$curCode =isset($request_data['currencyCode'])?$request_data['currencyCode']:$currencyCode;
	$page = isset($request_data['page'])?$request_data['page']:1;
	$limit = isset($request_data['limit'])?$request_data['limit']:8;
	$amount =isset($request_data['amount'])?$request_data['amount']:0;
	
	if(isset($request_data['action']) && strtolower($request_data['action'])=='balance'){
		$json_content = wallet_balance_api($userId,$curCode);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='redeemrequesthistory'){
		$json_content = wallet_redeemRequestList_api($userId,$curCode,$page,$limit);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='redeemrequest'){
		$json_content = wallet_redeemRequest_api($userId,$amount);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='deposit'){
		$json_content = wallet_deposit_api($request_data);
		echo $json_content;
	}else {
		$res_array['status']=false;
		$res_array['message']='Oops! Something wrong. Please try again.';
		echo json_encode($res_array);
	}
  	
?>

