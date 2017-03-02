           <?php
/*
	developer: ntc
	code:manage cart
	date:18102016
*/ 

	require_once("../includes-nct/config-nct.php");
	require_once("general-functions.php");
	$request_data=$_REQUEST;
	
	$userId = isset($request_data['userId'])?$request_data['userId']:0;
	$curCode =isset($request_data['currencyCode'])?$request_data['currencyCode']:$currencyCode;
	$deliveryType=isset($request_data['deliveryType'])?$request_data['deliveryType']:'d';
	$countryId =isset($request_data['countryId'])?$request_data['countryId']:0;
	$stateId =isset($request_data['stateId'])?$request_data['stateId']:0;
	$pickupPointId =isset($request_data['pickupPointId'])?$request_data['pickupPointId']:0;
	$couponCode =isset($request_data['couponCode'])?$request_data['couponCode']:'';
	
	if(isset($request_data['action']) && strtolower($request_data['action'])=='list'){
		$json_content = cart_list_api($userId,$curCode);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='pickuppointslist'){
		$json_content = pickup_points_list_api($userId);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='statepickuppointslist'){
		$json_content = state_pickup_points_list_api($stateId);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='getdeliveryaddress'){
		$json_content = cart_delivery_address_api($userId);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='applycouponcode'){
		$json_content = cart_scratch_coupon_code_api($userId,$couponCode,$curCode);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='changedeliveryaddress'){
		$json_content = change_delivery_address_api($request_data);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='delete'){
		$json_content = change_cart_item_api($request_data);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='checkoutlist'){
		$json_content = cart_checkoutlist_api($request_data,$userId,$curCode);
		echo $json_content;	
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='gettotalpayamount'){
		$json_content = cart_totalpay_amount_api($userId,$curCode);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='checkout'){
		$json_content = cart_checkout_api($request_data,$curCode);
		echo $json_content;	
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='addcart'){
		$json_content = add_cart_api($request_data);
		echo $json_content;	
	}else {
		$res_array['status']=false;
		$res_array['message']='Oops! something wrong. Please try again.';    
   		echo json_encode($res_array);
	}
?>
