<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.myOrder.php");
	
	$searchText = isset($_GET["searchText"]) && $_GET["searchText"]!='-' ? $_GET["searchText"] : (isset($_POST["searchText"]) && $_POST["searchText"]!='-'? $_POST["searchText"] : '');
	$searchText = str_replace("'",'',$searchText);
	$date = isset($_POST['date']) && $_POST['date']!=''?$_POST['date']:'';
	$status = isset($_POST['status']) && $_POST['status']!=''?$_POST['status']:'';
	$page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;

	$action = isset($_GET['action']) && $_GET['action']!=''?$_GET['action']:"";

	$module = 'myOrder-nct';
	$table = 'tbl_orders';
	
	$winTitle = 'My Order -' .SITE_NM;
    $headTitle = 'My Order';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));
		
	
	
	$mainObj = new myOrder($module,$searchText,$date,$status,$page);

	if($action == 'delete'){
		$id = $_GET['id'];
		
		$db->update('tbl_orders',array("id_delete"=>"y"),array("id"=>$id));
		$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>"Order deleted successfully"));
		redirectPage(SITE_URL.'myOrder');
	}
	

	$pageContent = $mainObj->getPageContent();
 	$orderList = $mainObj->getOrderList();
 	$pagination = $mainObj->getPagination();

 	$fields = array(
 			"%ORDER_LIST%","%SEARCH_TEXT%","%DATE%","%PAGINATION%"
 	);

	$fields_replace = array(
		$orderList,$searchText,$date,$pagination
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>