<?php
	$content = '';
	require_once("../../../includes-nct/config-nct.php");
	if($adminUserId == 0){die('Invalid request');}
	include("class.manage_subadmin-nct.php");
	
	$module = 'manage_subadmin-nct';
	$table = 'tbl_admin';
	$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : 'datagrid');
		
	chkPermission($module);
	$Permission=chkModulePermission($module);	
		
	
	$id = isset($_GET["id"]) ? trim($_GET["id"]) : (isset($_POST["id"]) ? trim($_POST["id"]) : 0);
	$value = isset($_POST["value"]) ? trim($_POST["value"]) : isset($_GET["value"]) ? trim($_GET["value"]) : '';
	$page = isset($_POST['iDisplayStart']) ? intval($_POST['iDisplayStart']) : 0;
	$rows = isset($_POST['iDisplayLength']) ? intval($_POST['iDisplayLength']) : 25;
	$sort = isset($_POST["iSortTitle_0"]) ? $_POST["iSortTitle_0"] : NULL;
	$order = isset($_POST["sSortDir_0"]) ? $_POST["sSortDir_0"] : NULL;	
	$chr = isset($_POST["sSearch"]) ? $_POST["sSearch"] : NULL;	
	$sEcho = isset($_POST['sEcho']) ? $_POST['sEcho'] : 1;

	extract($_GET);
	$searchArray = array("page"=>$page, "rows"=>$rows, "sort"=>$sort, "order"=>$order, "offset"=>$page, "chr"=>$chr, 'sEcho' =>$sEcho);
	

	if(isset($_POST["ajaxvalidate"]) && $_POST["ajaxvalidate"]==true) {
		if($_POST["action"]=='checkEmail'){
			$email = $_POST["txt_email"];
			$whr = '';
			if($id>0){
				//$whr = " AND id != $id";
				//$whr = array("id !="=> $id);
			}
			//$sqlCheck = $db->select($table,"page_name","page_name='".$page_name."' $whr");
			//echo mysql_num_rows($sqlCheck)>0 ? 'false' : 'true';
			$aWhere['uEmail'] = $email;
			if($id > 0){
				$aWhere["id !="] = (int)$id;
			}
			$sqlCheck = $db->count($table,$aWhere);
			echo ($sqlCheck)>0 ? 'false' : 'true';
			exit;
		}else if($_POST["action"]=='checkUname'){
			$user_name = $_POST["txt_uname"];
			$whr = '';
			if($id>0){
				//$whr = " AND id != $id";
				//$whr = array("id !="=> $id);
			}
			//$sqlCheck = $db->select($table,"page_name","page_name='".$page_name."' $whr");
			//echo mysql_num_rows($sqlCheck)>0 ? 'false' : 'true';
			$aWhere['uName'] = $user_name;
			if($id > 0){
				$aWhere["id !="] = (int)$id;
			}
			$sqlCheck = $db->pdoQuery('SELECT id FROM '.$table.' WHERE uName = ? AND id != ? ',array($user_name,$id))->affectedRows();
			echo ($sqlCheck)>0 ? 'false' : 'true';
			exit;
		}
		
	} else if($action == "updateStatus" && in_array('status',$Permission)) {
		$setVal = array('status'=>($value == 'y' ? 'y' : 'n'));
		$db->update($table,$setVal,array("id"=>$id));	
		echo json_encode(array('type'=>'success','Record '.($value == 'y' ? 'activated ' : 'deactivated ').'successfully'));
		
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'status',"action"=>$value);
		add_admin_activity($activity_array);
		exit;
	} else if($action == "delete" && in_array('delete',$Permission)) {
		//$aWhere=array("id"=>$id);
		//$db->delete($table,$aWhere);
		$db->update($table,array("status"=>'t'),array("id"=>$id));

		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'delete');
		add_admin_activity($activity_array);
		
	} else if($action == "delete_activity" && in_array('delete',$Permission)) {

		$aWhere=array("admin_id"=>$id);
		$db->delete('tbl_admin_activity',$aWhere);
		
	}
	
	$mainObject = new SubAdmin($id,$searchArray, $action);
	extract($mainObject->data);
	echo ($content);
	exit;