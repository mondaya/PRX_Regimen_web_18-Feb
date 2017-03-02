<?php
	$content = '';
	require_once("../../../includes-nct/config-nct.php");
	if($adminUserId == 0){die('Invalid request');}
	include("class.return-nct.php");
	//print_r($_POST);exit;
	$module = 'return-nct';
	chkPermission($module);
	$Permission=chkModulePermission($module);
	$table = 'tbl_return_request';
	$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : 'datagrid');
	$id = isset($_GET["id"]) ? trim($_GET["id"]) : (isset($_POST["id"]) ? trim($_POST["id"]) : 0);
	$value = isset($_POST["value"]) ? trim($_POST["value"]) : isset($_GET["value"]) ? trim($_GET["value"]) : '';
	$page = isset($_POST['iDisplayStart']) ? intval($_POST['iDisplayStart']) : 0;
	$rows = isset($_POST['iDisplayLength']) ? intval($_POST['iDisplayLength']) : 25;
	$sort = isset($_POST["iSortTitle_0"]) ? $_POST["iSortTitle_0"] : NULL;
	$order = isset($_POST["sSortDir_0"]) ? $_POST["sSortDir_0"] : NULL;
	$chr = isset($_POST["sSearch"]) ? $_POST["sSearch"] : NULL;
	$date_range = isset($_POST["sSearch_0"]) ? $_POST["sSearch_0"] : NULL;
	$from_date = isset($_GET["from"]) ? $_GET["from"] : NULL;
	$to_date = isset($_GET["to"]) ? $_GET["to"] : NULL;

	$sEcho = isset($_POST['sEcho']) ? $_POST['sEcho'] : 1;

	extract($_GET);
	$searchArray = array("page"=>$page, "rows"=>$rows, "sort"=>$sort, "order"=>$order, "offset"=>$page, "chr"=>$chr, 'sEcho' =>$sEcho, 'date_range'=>$date_range);

	if($action == "delete") {
		$aWhere=array("id"=>$id);
		$db->delete($table,$aWhere);
	}
	else if($action == "view" && in_array('view',$Permission)){
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'view');
		add_admin_activity($activity_array);
	}

	$mainObject = new Return_request($module, $id, NULL, $searchArray, $action);
	extract($mainObject->data);
	echo ($content);
	exit;