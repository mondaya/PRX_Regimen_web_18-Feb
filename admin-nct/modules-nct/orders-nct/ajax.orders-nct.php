<?php
	$content = '';
	require_once("../../../includes-nct/config-nct.php");
	if($adminUserId == 0){die('Invalid request');}
	include("class.orders-nct.php");

	$module = 'orders-nct';
	chkPermission($module);
	$Permission = chkModulePermission($module);
	$table = 'tbl_orders';
	$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : 'datagrid');
	$id = isset($_GET["id"]) ? trim($_GET["id"]) : (isset($_POST["id"]) ? trim($_POST["id"]) : 0);
	$value = isset($_POST["value"]) ? trim($_POST["value"]) : isset($_GET["value"]) ? trim($_GET["value"]) : '';
	$page = isset($_POST['iDisplayStart']) ? intval($_POST['iDisplayStart']) : 0;
	$rows = isset($_POST['iDisplayLength']) ? intval($_POST['iDisplayLength']) : 25;
	$sort = isset($_POST["iSortTitle_0"]) ? $_POST["iSortTitle_0"] : NULL;
	$order = isset($_POST["sSortDir_0"]) ? $_POST["sSortDir_0"] : NULL;
	$chr = isset($_POST["sSearch"]) ? $_POST["sSearch"] : NULL;
	$sEcho = isset($_POST['sEcho']) ? $_POST['sEcho'] : 1;
	$date_range = isset($_POST["sSearch_0"]) ? $_POST["sSearch_0"] : NULL;
	$country = isset($_POST["country"]) ? $_POST["country"] : 0;
	$state = isset($_POST["state"]) ? $_POST["state"] : 0;
	$status = isset($_POST["status"]) ? $_POST["status"] : NULL;

	extract($_GET);
	$searchArray = array("page"=>$page, "rows"=>$rows, "sort"=>$sort, "order"=>$order, "offset"=>$page, "chr"=>$chr, 'sEcho' =>$sEcho, 'date_range'=>$date_range, 'country'=>$country, 'state'=>$state, 'status'=>$status);


	if($action == "delete") {
		$aWhere=array("id"=>$id);
		$db->delete($table,$aWhere);
	} else if($action == "view" && in_array('view',$Permission)){
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'view');
		add_admin_activity($activity_array);
	} else if(isset($action) && $action=='getState' && !empty($_POST['country_id'])) {
		$states = '';
		$states_option = '<option value="">Select State</option>';
		$states = $db->select('tbl_state', array('id', 'stateName'), array('countryId'=>$_POST['country_id']), 'ORDER BY stateName ASC')->results();
		foreach ($states as $key => $value) {
			$states_option .= '<option value="'.$value['id'].'">'.$value['stateName'].'</option>';
		}
		echo json_encode(array('state_option'=>$states_option));
		exit();
	}

	$mainObject = new Orders($module, $id, NULL, $searchArray, $action);
	extract($mainObject->data);
	echo ($content);
	exit;