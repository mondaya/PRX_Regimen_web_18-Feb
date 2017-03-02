<?php
	//$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.categories-nct.php");

	$page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;

	$table = "tbl_categories";
	$module = 'categories-nct';

	$winTitle = 'Categories '.SITE_NM;
	$headTitle = 'Categories';
	$metaTag = getMetaTags(array("description"=>$winTitle,"keywords"=>$headTitle,"author"=>AUTHOR));

	$mainObj = new Categories($module,$page);
	

	$pageContent = $mainObj->getPageContent();
	$categoriesList = $mainObj->getCategoriesList();
	$pagination = $mainObj->getPagination();
 	
 	$fields = array(
		'%CATEGORIES_LIST%','%PAGINATION%'
	);

	$fields_replace = array(
		$categoriesList,$pagination
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");
	
	
?>