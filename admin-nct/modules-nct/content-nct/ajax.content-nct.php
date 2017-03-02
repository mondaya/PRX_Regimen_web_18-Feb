<?php
//echo $_REQUEST['pageName'];exit;
	$content = '';
	require_once("../../../includes-nct/config-nct.php");
	if($adminUserId == 0){die('Invalid request');}
	include("class.content-nct.php");
	
	$module = 'content-nct';
	chkPermission($module);
	$Permission=chkModulePermission($module);
	$table = 'tbl_content';
	$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : 'datagrid');
	$id = isset($_GET["id"]) ? trim($_GET["id"]) : (isset($_POST["id"]) ? trim($_POST["id"]) : 0);
	$value = isset($_POST["value"]) ? trim($_POST["value"]) : isset($_GET["value"]) ? trim($_GET["value"]) : '';
	$page_no = isset($_POST['iDisplayStart']) ? intval($_POST['iDisplayStart']) : 0;
	$rows = isset($_POST['iDisplayLength']) ? intval($_POST['iDisplayLength']) : 25;
	$sort = isset($_POST["iSortTitle_0"]) ? $_POST["iSortTitle_0"] : NULL;
	$order = isset($_POST["sSortDir_0"]) ? $_POST["sSortDir_0"] : NULL;	
	$chr = isset($_POST["sSearch"]) ? $_POST["sSearch"] : NULL;	
	$sEcho = isset($_POST['sEcho']) ? $_POST['sEcho'] : 1;

	extract($_GET);
	
	$searchArray = array("page"=>$page_no, "rows"=>$rows, "sort"=>$sort, "order"=>$order, "offset"=>$page_no, "chr"=>$chr, 'sEcho' =>$sEcho);
	if(isset($_POST["ajaxvalidate"]) && $_POST["ajaxvalidate"]==true) {
		$page_name = $_POST["page_name"];
		$id =$_POST['id'];
		$whr = '';
		if($id>0){
			$whr = " AND pId != $id";
		}
		if(getTotalRows($table,"pageName='".$page_name.$whr."'",'pId') > 0)
		{	
			echo 'false';
		}
		else
		{
			echo 'true';
		}
					
		exit;
		
	} else if($action == "updateStatus" && in_array('status',$Permission)) {
		$setVal = array('isActive'=>($value == 'y' ? 'y' : 'n'));
		$db->update($table,$setVal,array("pId"=>$id));	
		echo json_encode(array('type'=>'success','Record '.($value == 'y' ? 'activated ' : 'deactivated ').'successfully'));
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'status',"action"=>$value);
		add_admin_activity($activity_array);
		exit;
	} else if($action == "delete" && in_array('delete',$Permission)) {
		$aWhere=array("pId"=>$id);
		$db->delete($table,$aWhere);
		//$db->update($table,array("isActive"=>'t'),array("id"=>$id));
		
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'delete');
		add_admin_activity($activity_array);
	}
	else if($action == "view" && in_array('view',$Permission)){
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'view');
		add_admin_activity($activity_array);
	}

	$mainObject = new Content($module, $id, NULL, $searchArray, $action);
	extract($mainObject->data);
	echo ($content);
	exit;