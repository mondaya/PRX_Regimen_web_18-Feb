<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.favoriteCate-nct.php");
	
	$page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;

	$action = isset($_GET['action']) && $_GET['action']!=''?$_GET['action']:"";

	$module = 'favoriteCate-nct';
	$table = 'tbl_orders';
	
	$winTitle = 'My Favorite Categories -' .SITE_NM;
    $headTitle = 'My Favorite Categories';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));
		
	
	
	$mainObj = new favoriteCate($module,$page);

	if($action == 'delete'){
		$id = $_GET['id'];
		
		$db->delete('tbl_favorite_categories',array("id"=>$id));
		$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>"Categories removed successfully"));
		redirectPage(SITE_URL.'myCate');
	}

	if(isset($_POST['submitCate']) && $_POST['submitCate'] == "Save"){
		//echo '<pre>';
		//print_r($_POST);exit;
		$cateIds = $_POST['category'];

		foreach($cateIds as $value) {
			
			$data = array();
			$data['userId'] = $sessUserId;
			$data['categoryId'] = $value;
			$db->insert("tbl_favorite_categories",$data);

		}
		
		$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>"Categories added successfully"));
		redirectPage(SITE_URL.'myCate');

	}
	

	$pageContent = $mainObj->getPageContent();
 	$cateList = $mainObj->getcateList();
 	$pagination = $mainObj->getPagination();
 	$cateOption = $mainObj->getCateOption();

 	$fields = array(
 			"%CATE_LIST%","%PAGINATION%","%OPTION%"
 	);

	$fields_replace = array(
		$cateList,$pagination,$cateOption
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>