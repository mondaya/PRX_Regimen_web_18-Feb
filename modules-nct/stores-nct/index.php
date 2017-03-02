<?php
	//$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.stores-nct.php");

	$page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;
	$cateId = isset($_GET["cateId"]) && $_GET["cateId"]!='-' ? $_GET["cateId"] : (isset($_POST["cateId"]) && $_POST["cateId"]!='-'? $_POST["cateId"] : 0);
	$subCateId = isset($_GET["subCateId"]) && $_GET["subCateId"]!='-' ? $_GET["subCateId"] : (isset($_POST["subCateId"]) && $_POST["subCateId"]!='-'? $_POST["subCateId"] : 0);

	$table = "tbl_categories";
	$module = 'stores-nct';

	$winTitle = 'Stores '.SITE_NM;
	$headTitle = 'Stores';
	$metaTag = getMetaTags(array("description"=>$winTitle,"keywords"=>$headTitle,"author"=>AUTHOR));

	$mainObj = new Stores($module,$page,$cateId,$subCateId);
	

	$pageContent = $mainObj->getPageContent();
	$storeList = $mainObj->getStoreList();
	$pagination = $mainObj->getPagination();
 	
 	$fields = array(
 		'%STORE_LIST%','%PAGINATION%'
	);

	$fields_replace = array(
		$storeList,$pagination
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");
	
	
?>