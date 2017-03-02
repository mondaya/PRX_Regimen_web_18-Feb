<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.favoriteStore-nct.php");
	
	$page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;

	$action = isset($_GET['action']) && $_GET['action']!=''?$_GET['action']:"";

	$module = 'favoriteStore-nct';
	$table = 'tbl_orders';
	
	$winTitle = 'My Favorite Brand -' .SITE_NM;
    $headTitle = 'My Favorite Brand';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));
		
	
	
	$mainObj = new favoriteStore($module,$page);

	if($action == 'delete'){
		$id = $_GET['id'];
		
		$db->delete('tbl_favourite_store',array("id"=>$id));
		$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>"Brand removed successfully"));
		redirectPage(SITE_URL.'myStore');
	}

	if(isset($_POST['submitCate']) && $_POST['submitCate'] == "Save"){
		//echo '<pre>';
		//print_r($_POST);exit;
		$storeIds = $_POST['store'];

		foreach ($storeIds as $value) {
			
			$data = array();
			$data['userId'] = $sessUserId;
			$data['storeId'] = $value;
			$db->insert("tbl_favourite_store",$data);

		}

		$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>"Brand added successfully"));
		redirectPage(SITE_URL.'myStore');

	}
	

	$pageContent = $mainObj->getPageContent();
 	$storeList = $mainObj->getstoreList();
 	$pagination = $mainObj->getPagination();
 	$cateOption = $mainObj->getCateOption();

 	$fields = array(
 			"%STORE_LIST%","%PAGINATION%","%OPTION%"
 	);

	$fields_replace = array(
		$storeList,$pagination,$cateOption
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>