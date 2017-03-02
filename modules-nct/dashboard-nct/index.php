<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.dashboard-nct.php");
	
	$module = 'dashboard-nct';
	$table = 'tbl_orders';
	
	$winTitle = 'My Account -' .SITE_NM;
    $headTitle = 'My Account';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));
		
	
	
	$mainObj = new dashboard($module);

	$pageContent = $mainObj->getPageContent();
 	
 	$fields = array(
 	);

	$fields_replace = array(
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>