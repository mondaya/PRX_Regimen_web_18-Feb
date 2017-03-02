<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.paymentHistory-nct.php");
	
	$page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;

	$action = isset($_GET['action']) && $_GET['action']!=''?$_GET['action']:"";

	$module = 'paymentHistory-nct';
	$table = 'tbl_orders';
	
	$winTitle = 'Payment History -' .SITE_NM;
    $headTitle = 'Payment History';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));
		
	
	
	$mainObj = new paymentHistory($module,$page);

	$pageContent = $mainObj->getPageContent();
 	$paymentList = $mainObj->getpaymentList();
 	$pagination = $mainObj->getPagination();
 

 	$fields = array(
 		'%PAYMENT_LIST%','%PAGINATION%'
 	);

	$fields_replace = array(
		$paymentList,$pagination
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>