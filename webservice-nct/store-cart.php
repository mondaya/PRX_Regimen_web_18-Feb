<?php
/*
	developer: ntc
	code:manage cart
	date:18102016
*/
	require_once("../includes-nct/config-nct.php");
	require_once("general-functions.php");
	require_once("scrap.php");
	$request_data=$_REQUEST;
	
	$userId = isset($request_data['userId'])?$request_data['userId']:0;
	$curCode =isset($request_data['currencyCode'])?$request_data['currencyCode']:$currencyCode;
	$deliveryType=isset($request_data['deliveryType'])?$request_data['deliveryType']:'d';
	$countryId =isset($request_data['countryId'])?$request_data['countryId']:0;
	$stateId =isset($request_data['stateId'])?$request_data['stateId']:0;
	$pickupPointId =isset($request_data['pickupPointId'])?$request_data['pickupPointId']:0;
	$couponCode =isset($request_data['couponCode'])?$request_data['couponCode']:'';
	$content =isset($request_data['content'])?$request_data['content']:'';
	$storeId =isset($request_data['storeId'])?$request_data['storeId']:0;
	$storeLink =isset($request_data['storeLink'])?$request_data['storeLink']:'';
	//$htmldata=file_get_contents("scraphtml.html");
	//echo $htmldata;
	if(isset($request_data['action']) && strtolower($request_data['action'])=='addcart'){

		$storeLink=	getTableValue('tbl_stores','storeLink',array('id'=>$storeId));
		$categoryId=getTableValue('tbl_stores','categoryId',array('id'=>$storeId));
		$subcategoryId=getTableValue('tbl_stores','subcategoryId',array('id'=>$storeId));
		$cid =explode(",", $categoryId)[0];
		$scid =explode(",", $subcategoryId)[0];
		$htmldata=$content;
		
		//$storeLink ='http://www.natrenpro.com';
		$storecart =scraphtml($storeLink,$htmldata);
		//print_r($storecart);
		if(count($storecart)>0)
		{
			$json_content = add_store_cart_api($userId,$storeId,$storecart,$cid,$scid);
			echo $json_content;
	   }else
	   {
	   		$status=false;
			$message="Scrap store not available.";
			$res_array['status']=$status;
		    $res_array['message']=$message;
		    echo json_encode($res_array);
	   }

	}else {
		$res_array['status']=false;
		$res_array['message']='Oops! something wrong. Please try again.';    
   		echo json_encode($res_array);spance in html
	}
	
?>
