<?php
	require_once("../includes-nct/config-nct.php");
	require_once("general-functions.php");
	$module = 'product_deals-nct';
	$request_data=$_REQUEST;
	$filedata =$_FILES;

	$id = isset($request_data['id'])?$request_data['id']:0;
	$userId = isset($request_data['userId'])?$request_data['userId']:0;
	$page = isset($request_data['page'])?$request_data['page']:1;
	$limit = isset($request_data['limit'])?$request_data['limit']:8;
	$keyword = isset($request_data['keyword'])?$request_data['keyword']:'';
	$categoryId = isset($request_data['categoryId'])?$request_data['categoryId']:'';
	$status =isset($request_data['status'])?$request_data['status']:'';
	$fromDate =isset($request_data['fromDate'])?$request_data['fromDate']:'';
	$toDate =isset($request_data['toDate'])?$request_data['toDate']:'';
	$subCategoryId = isset($request_data['subCategoryId'])?$request_data['subCategoryId']:'';
	$curCode =isset($request_data['currencyCode'])?$request_data['currencyCode']:$currencyCode;
	$orderId =isset($request_data['orderId'])?$request_data['orderId']:'';
	$txnId =isset($request_data['txnId'])?$request_data['txnId']:'';
	
	if(isset($request_data['action']) && $request_data['action']=='productDeals'){
		$json_content = getProductDeals_api($request_data,$curCode);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='productDetail' && $id>0){
		$json_content = getProductDetail_api($request_data);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='similarProductDeals' && $id>0){
		$json_content = similarDeals($request_data,$id);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='orderlist'){
		$json_content = get_order_list($keyword,$page,$limit,$userId,$curCode,$status,$fromDate,$toDate);
		echo $json_content;	
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='deleteorder'){
		$json_content = delete_order($userId,$orderId);
		echo $json_content;		
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='orderdetail'){
		$json_content = order_detail_api($userId,$orderId,$curCode);
		echo $json_content;		
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='returnorder'){
		$json_content = return_order_api($request_data,$filedata);
		echo $json_content;	
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='returnorderimage'){
		$json_content = return_order_image_api($request_data,$filedata);
		echo $json_content;		
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='paymenthistory'){
		$json_content = payment_history_api($userId,$page,$limit,$curCode);
		echo $json_content;	
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='transactiondetails'){
		$json_content = transaction_detail_api($userId,$txnId,$curCode);
		echo $json_content;	
	}else if(isset($request_data['action']) && $request_data['action']=='buy'){
		$json_content = product_deal_buy_api($request_data,$curCode);
		echo $json_content;
	}else {
		$res_array['status']=false;
		$res_array['message']='Oops! Something wrong. please try again.';    
   		echo json_encode($res_array);
	}
?>
	
