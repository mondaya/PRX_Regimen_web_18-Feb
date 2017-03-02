<?php
	require_once("../includes-nct/config-nct.php");
	require_once("general-functions.php");
	$module = 'login-nct';
	$request_data=$_REQUEST;
	
	if(isset($request_data['action']) && strtolower($request_data['action'])=='login'){
		$json_content = login_api($request_data);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='socialLogin'){
		$json_content = social_login_api($request_data);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='forgotPassword'){
		$json_content = forgotPassword_api($request_data);
		echo $json_content;
	}else {
		$res_array['status']=false;
		$res_array['message']='Oops! Something wrong. Please try again.';    
   		echo json_encode($res_array);
	}
?>	