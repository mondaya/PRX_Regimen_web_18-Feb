<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.wallet-nct.php");
	
	$module = 'wallet-nct';
	$table = 'tbl_orders';
	
	$winTitle = 'My Wallet -' .SITE_NM;
    $headTitle = 'My Wallet';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));
		
	
	
	$mainObj = new wallet($module);

	$pageContent = $mainObj->getPageContent();
 	
 	$fields = array(
 	);

	$fields_replace = array(
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>