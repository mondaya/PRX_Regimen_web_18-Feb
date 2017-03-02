<?php
	require_once("../../includes-nct/config-nct.php");
	require_once("class.deals.php");
	
	$searchText = isset($_GET["searchText"]) && $_GET["searchText"]!='-' ? $_GET["searchText"] : (isset($_POST["searchText"]) && $_POST["searchText"]!='-'? $_POST["searchText"] : '');
	$searchText = str_replace("'",'',$searchText);
	
	//$cateId = isset($_POST["cateId"]) && $_POST["cateId"]!='' ? $_POST["cateId"] : (isset($_GET["cateId"]) && $_GET["cateId"]!=''? $_GET["cateId"] : 0);
	//$subCateId = isset($_POST["subCateId"]) && $_POST["subCateId"]!='' ? $_POST["subCateId"] : (isset($_GET["subCateId"]) && $_GET["subCateId"]!=''? $_GET["subCateId"] : 0);
	
	$cateId = isset($_GET["cateId"]) && $_GET["cateId"]!='-' ? $_GET["cateId"] : (isset($_POST["cateId"]) && $_POST["cateId"]!='-'? $_POST["cateId"] : 0);
	$subCateId = isset($_GET["subCateId"]) && $_GET["subCateId"]!='-' ? $_GET["subCateId"] : (isset($_POST["subCateId"]) && $_POST["subCateId"]!='-'? $_POST["subCateId"] : 0);

	$page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;

	$module = 'deals-nct';
	$table = 'tbl_product_deals';
	
	$winTitle = 'Search deals -' .SITE_NM;
    $headTitle = 'Search deals';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));
		
	
	
	$mainObj = new deals($module,$searchText,$cateId,$subCateId,$page);
	

	$pageContent = $mainObj->getPageContent();
 	$dealList = $mainObj->getDealsList();
 	$cateOption = $mainObj->getCateOption();

 	if($cateId > 0){
 		$subCateOption = $mainObj->getSubCateOption();
 	}
 	
 	$pagination = $mainObj->getPagination();

 	$fields = array(
 		'%DEAL_LIST%','%SEARCH_TEXT%','%CATE_OPTION%','%SUBCATE_OPTION%','%PAGINATION%'
	);

	$fields_replace = array(
		$dealList,$searchText,$cateOption,$subCateOption,$pagination
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>