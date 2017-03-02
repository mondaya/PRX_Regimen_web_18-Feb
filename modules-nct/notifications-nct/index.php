<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.notifications-nct.php");
	
	$page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;

	$action = isset($_GET['action']) && $_GET['action']!=''?$_GET['action']:"";

	$module = 'notifications-nct';
	$table = 'tbl_orders';
	
	$winTitle = 'My Notifications -' .SITE_NM;
    $headTitle = 'My Notifications';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));
		
	
	
	$mainObj = new notifications($module,$page);

	$pageContent = $mainObj->getPageContent();
 	$notificationList = $mainObj->getnotificationList();
 	$pagination = $mainObj->getPagination();
 	
 	$fields = array(
 		"%NOTIFICATION_LIST%","%PAGINATION%"
 	);

	$fields_replace = array(
		$notificationList,$pagination
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>