<?php
	$content = '';
	require_once("../../../includes-nct/config-nct.php");
	if($adminUserId == 0){die('Invalid request');}
	include("class.users-nct.php");

	$module = 'users-nct';
	chkPermission($module);
	$Permission=chkModulePermission($module);
	$table = 'tbl_users';
	$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : 'datagrid');


	$id = isset($_GET["id"]) ? trim($_GET["id"]) : (isset($_POST["id"]) ? trim($_POST["id"]) : 0);
	$value = isset($_POST["value"]) ? trim($_POST["value"]) : isset($_GET["value"]) ? trim($_GET["value"]) : '';
	$page = isset($_POST['iDisplayStart']) ? intval($_POST['iDisplayStart']) : 0;
	$rows = isset($_POST['iDisplayLength']) ? intval($_POST['iDisplayLength']) : 25;
	$sort = isset($_POST["iSortTitle_0"]) ? $_POST["iSortTitle_0"] : NULL;
	$order = isset($_POST["sSortDir_0"]) ? $_POST["sSortDir_0"] : NULL;
	$chr = isset($_POST["sSearch"]) ? $_POST["sSearch"] : NULL;
	$sEcho = isset($_POST['sEcho']) ? $_POST['sEcho'] : 1;
	$country = isset($_POST['country']) ? $_POST['country'] : 0;
	$state = isset($_POST['state']) ? $_POST['state'] : 0;
	$city = isset($_POST['city']) ? $_POST['city'] : 0;

	extract($_GET);
	$searchArray = array("page"=>$page, "rows"=>$rows, "sort"=>$sort, "order"=>$order, "offset"=>$page, "chr"=>$chr, 'sEcho' =>$sEcho, 'country' =>$country, 'state' =>$state, 'city' =>$city);

	if($action == "updateBuyStatus") {
		$setVal = array('buyStatus'=>($value == 'y' ? 'y' : 'n'));
		$db->update($table,$setVal,array("id"=>$id));
		echo json_encode(array('type'=>'success','Record '.($value == 'y' ? 'Verified ' : 'Unverified ').'successfully'));
		
		//Send email to user
		$users = $db->select('tbl_users',array('email','firstName'),array('id'=>$id))->result();
		if($value == 'y'){
			$contArray = array(
				"USER_NM"=>$users['firstName']
			);
			sendMail(base64_decode($users['email']),"purchase_status",$contArray);
		}

		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'status',"action"=>$value);
		add_admin_activity($activity_array);
		exit;
	}
	else if($action == "updateStatus") {
		$setVal = array('isActive'=>($value == 'y' ? 'y' : 'n'));
		$db->update($table,$setVal,array("id"=>$id));
		echo json_encode(array('type'=>'success','Record '.($value == 'y' ? 'activated ' : 'deactivated ').'successfully'));
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'status',"action"=>$value);
		add_admin_activity($activity_array);
		exit;
	}
	else if($action == "delete") {
		$aWhere=array("id"=>$id);
		$db->delete($table,$aWhere);
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'delete');
		add_admin_activity($activity_array);
	}
	else if($action == "view" && in_array('view',$Permission)){
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'view');
		add_admin_activity($activity_array);
	}
	else if ($action == 'countryData' && $action !='') {
		$qrySel = $db->pdoQuery("SELECT id,countryName FROM tbl_country WHERE isActive = 'y'")->results();
		foreach ($qrySel as $fetchRes) {
			if($fetchRes['countryName'] !='')
			{
				$option .= "<option value=".$fetchRes['id'].">".$fetchRes['countryName']."</option>";
			}
		}
		echo json_encode($option);
	}
	else if ($action == 'stateData' && $action !='') {
		$qrySel = $db->pdoQuery("SELECT id,stateName FROM tbl_state WHERE isActive = 'y'")->results();
		foreach ($qrySel as $fetchRes) {
			if($fetchRes['stateName'] !='')
			{
				$option .= "<option value=".$fetchRes['id'].">".$fetchRes['stateName']."</option>";
			}
		}
		echo json_encode($option);
	}
	else if ($action == 'cityData' && $action !='') {
		$qrySel = $db->pdoQuery("SELECT id,cityName FROM tbl_city WHERE isActive = 'y'")->results();
		foreach ($qrySel as $fetchRes) {
			if($fetchRes['cityName'] !='')
			{
				$option .= "<option value=".$fetchRes['id'].">".$fetchRes['cityName']."</option>";
			}
		}
		echo json_encode($option);
	}
	else if($action == 'changeState'){

	$qrySel=$db->pdoQuery("SELECT id,stateName from tbl_state where isActive = 'y' and countryId = ".$country."");
	$fetchRes = $qrySel->results();
	$totalRow = $qrySel->affectedRows();

	$content = '<option value="">Select state</option>';
			        if($totalRow > 0){
						foreach($fetchRes as $fetchRes){
							$id = $fetchRes['id'];
							$stateName = $fetchRes['stateName'];

							$content .= '<option value="'.$id.'">'.$stateName.'</option>';
						}
					}



	}
	else if($action == 'changeCity'){

	$qrySel=$db->pdoQuery("SELECT id,cityName from tbl_city where isActive = 'y' and stateId = ".$state."");
	$fetchRes = $qrySel->results();
	$totalRow = $qrySel->affectedRows();

	$content = '<option value="">Select city</option>';

			        if($totalRow > 0){
						foreach($fetchRes as $fetchRes){
							$id = $fetchRes['id'];
							$cityName = $fetchRes['cityName'];

							$content .= '<option value="'.$id.'">'.$cityName.'</option>';
						}
					}

	}

	$mainObject = new Users($module, $id, NULL, $searchArray, $action);
	extract($mainObject->data);
	echo ($content);
	exit;