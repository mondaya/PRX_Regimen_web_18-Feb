<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.myCustomOrder.php");
	
	$searchText = isset($_GET["searchText"]) && $_GET["searchText"]!='-' ? $_GET["searchText"] : (isset($_POST["searchText"]) && $_POST["searchText"]!='-'? $_POST["searchText"] : '');
	$searchText = str_replace("'",'',$searchText);
	$date = isset($_POST['date']) && $_POST['date']!=''?$_POST['date']:'';
	$status = isset($_POST['status']) && $_POST['status']!=''?$_POST['status']:'';
	$page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;

	$action = isset($_GET['action']) && $_GET['action']!=''?$_GET['action']:"";

	$module = 'myCustomOrder-nct';
	$table = 'tbl_custom_order';
	
	$winTitle = 'My Custom Order -' .SITE_NM;
    $headTitle = 'My Custom Order';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));
		
	
	
	$mainObj = new myCustomOrder($module,$searchText,$date,$status,$page);

	if($action == 'delete'){
		$id = $_GET['id'];
		
		$db->update('tbl_custom_orders',array("id_delete"=>"y"),array("id"=>$id));
		$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>"Order deleted successfully"));
		redirectPage(SITE_URL.'myCustomOrder');
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