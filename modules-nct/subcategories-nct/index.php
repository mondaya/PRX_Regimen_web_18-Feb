<?php
	//$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.subcategories-nct.php");

	$page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;
	$cateId = isset($_GET['cateId']) && $_GET['cateId']!=''?$_GET['cateId']:0;

	//For check category exist or not
	$axist = getTotalRows('tbl_categories',array("id"=>$cateId),'id');
	if($axist == 0){
		$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>"Invalid request."));
		redirectPage(SITE_URL);
	}

	$table = "tbl_subcategory";
	$module = 'subcategories-nct';

	$winTitle = 'Sub Categories '.SITE_NM;
	$headTitle = 'Sub Categories';
	$metaTag = getMetaTags(array("description"=>$winTitle,"keywords"=>$headTitle,"author"=>AUTHOR));

	$mainObj = new SubCategories($module,$page,$cateId);
	

	$pageContent = $mainObj->getPageContent();
	$categoriesList = $mainObj->getSubCategoriesList();
	$pagination = $mainObj->getPagination();
 	
 	$fields = array(
		'%SUBCATEGORY_LIST%','%PAGINATION%'
	);

	$fields_replace = array(
		$categoriesList,$pagination
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");
	
	
?>