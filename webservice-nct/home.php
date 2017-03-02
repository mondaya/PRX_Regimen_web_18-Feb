<?php
	require_once("../includes-nct/config-nct.php");
	require_once("../modules-nct/home-nct/class.home-nct.php");
	require_once("general-functions.php");
	$module = 'home-nct';
	$request_data=$_REQUEST;
	$objHome=new Home($module);
	$userId = isset($request_data['userId'])?$request_data['userId']:0;
	$page = isset($request_data['page'])?$request_data['page']:1;
	$limit = isset($request_data['limit'])?$request_data['limit']:8;
	$keyword = isset($request_data['keyword'])?$request_data['keyword']:'';
	$categoryId = isset($request_data['categoryId'])?$request_data['categoryId']:'';
	$subCategoryId = isset($request_data['subCategoryId'])?$request_data['subCategoryId']:'';
	$curCode =isset($request_data['currencyCode'])?$request_data['currencyCode']:$currencyCode;
	if(isset($request_data['action']) && $request_data['action']=='categoryList'){
		$page = isset($request_data['page'])?$request_data['page']:0;
		$json_content = category_list_api($keyword,$page,$limit,$userId);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='favCategoryUpdate'){
		$json_content = favCategoryUpdate_api($request_data);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='homecategory'){
		$json_content = category_home_api();
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='favCategoryList'){
		$json_content = fav_categoryList_api($userId);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='subCategoryList'){
		$page = isset($request_data['page'])?$request_data['page']:0;
		$json_content = subcategory_list_api($categoryId,$page,$limit);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='storeList'){
		$json_content = getStoreList_api($userId,$categoryId,$subCategoryId,$page,$limit);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='allStoreList'){
		$json_content = getAllStoreList_api($userId,$page,$limit);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='fevStoreList'){
		$json_content = fav_storeList_api($userId);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='favStoreUpdate')
	{	
		$json_content =favStoreUpdate_api($request_data);
		echo $json_content;

	}else if(isset($request_data['action']) && $request_data['action']=='sbscrb_newsletter'){
		$json_content = sbscrb_newslleter_api($request_data);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='info_pages'){
		$json_content = staticPages_api();
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='notificationList')
	{
		$json_content = notification_list_api($page,$limit,$userId,$curCode);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='contactus')
	{
		$json_content = contact_us_api($request_data);
		echo $json_content;
	}else if(isset($request_data['action']) && $request_data['action']=='returnpolicy')
	{
		$returnPolicy = getTableValue('tbl_home_contant','returnPolicy',array('id'=>'1'));
		$returnPolicy=strip_tags($returnPolicy);
		$res_array['returnpolicy']= $returnPolicy;
		$res_array['status']=true;
		$res_array['message']='success';
   	   	echo json_encode($res_array);
   	}else if(isset($request_data['action']) && $request_data['action']=='currencylist')
   	{	
   		 echo currencyList_API();
   	}else if(isset($request_data['action']) && $request_data['action']=='adminpaypal')
   	{	
   		 $jsoncontent =adminpaypal_API();
   		 echo $jsoncontent;
   	}else if(isset($request_data['action']) && $request_data['action']== 'video')
   	{
   		global $db;
		
		$row=$db->pdoQuery("select `value` from tbl_site_settings where constant='VIDEO_EMBED'")->result();
		$video=$row['value'];
		$spos =strpos( $video,'http');
		$hlink=substr($video, $spos);
		$endpos =strpos($hlink,'"' );
		if($spos)
		{	
			$link =substr($hlink, 0,$endpos);
		}else
		{
			$link ="";
		}
		$res_array['videohtml'] =base64_encode($video);
		$res_array['videolink'] =$link;
		$res_array['status']=true;	
		$res_array['message']='success';
		echo json_encode($res_array);
   	
	}else {
		$res_array['status']=false;
		$res_array['message']='Oops! Somethingnt wrong. Please try again.';    
   	   	echo json_encode($res_array);
	}

?>	
