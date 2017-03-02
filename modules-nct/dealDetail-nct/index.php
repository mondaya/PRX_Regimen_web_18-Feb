<?php
	//$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.dealDetail-nct.php");
	
	$dealId = isset($_REQUEST['dealId']) && $_REQUEST['dealId']!=''?$_REQUEST['dealId']:'';
	$quantity = isset($_REQUEST['quantity']) && $_REQUEST['quantity']!=''?$_REQUEST['quantity']:'';
	$action = isset($_REQUEST['action']) && $_REQUEST['action']!=''?$_REQUEST['action']:'';

	//Check for product exist
	$id = getTotalRows('tbl_product_deals',array("id"=>$dealId),'id');
	$quantity = getTableValue('tbl_product_deals','quantity',array("id"=>$dealId));

	if($id == 0 || $quantity == 0){
		$_SESSION['msgType'] = disMessage(array('type'=>'err','var'=>"Invalid request"));
		redirectPage(SITE_URL);
	}
	
	$table = "tbl_product_deals";
	$module = 'dealDetail-nct';

	$winTitle = 'Product Detail '.SITE_NM;
	$headTitle = 'Product Detail';
	$metaTag = getMetaTags(array("description"=>$winTitle,"keywords"=>$headTitle,"author"=>AUTHOR));

	$mainObj = new dealDetail($module,$dealId);

	if($action == 'add-to-cart'){
		//echo '<pre>';
		//print_r($_REQUEST);exit;
		extract($_REQUEST);

		if($sessUserId > 0){

			$alreadyExist = getTotalRows('tbl_cart',array("productId"=>$dealId,"userId"=>$sessUserId),'id');
			if($alreadyExist == 0){

				$data = array();
				$data['userId'] = $sessUserId;
				$data['productId'] = $dealId;
				$data['quantity'] = $dealquantity>0?$dealquantity:1;
				$data['createdDate'] = date('Y-m-d h:i:s');
				$db->insert('tbl_cart',$data);

				$cartProduct = getTotalRows('tbl_cart',array("userId"=>$sessUserId),'id');

				$_SESSION['msgType'] = disMessage(array('type'=>'suc','var'=>"Added successfully"));
				redirectPage(SITE_URL.'orderCart');

			}else{
				$_SESSION['msgType'] = disMessage(array('type'=>'err','var'=>"Already in cart"));
				redirectPage(SITE_URL.'product/'.$dealId);
			}

		}else{
			$_SESSION['msgType'] = disMessage(array('type'=>'err','var'=>"Please login to continue"));
			redirectPage(SITE_URL.'product/'.$dealId);
			
		}
	
	}
	

	$pageContent = $mainObj->getPageContent();
	$dealList = $mainObj->getSimilarDealsList();
	
	
 	$fields = array(
 		'%SIMILAR_DEALS%'
	);

	$fields_replace = array(
		$dealList
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");
	
	
?>