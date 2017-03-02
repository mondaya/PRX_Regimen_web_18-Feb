<?php
	$content = '';
	require_once("../../../includes-nct/config-nct.php");
	if($adminUserId == 0){die('Invalid request');}
	include("class.products-nct.php");
	
	$module = 'products-nct';
	chkPermission($module);
	$Permission=chkModulePermission($module);
	$table = 'tbl_product_deals';
	$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : 'datagrid');
	$id = isset($_GET["id"]) ? trim($_GET["id"]) : (isset($_POST["id"]) ? trim($_POST["id"]) : 0);
	$value = isset($_POST["value"]) ? trim($_POST["value"]) : isset($_GET["value"]) ? trim($_GET["value"]) : '';
	$page = isset($_POST['iDisplayStart']) ? intval($_POST['iDisplayStart']) : 0;
	$rows = isset($_POST['iDisplayLength']) ? intval($_POST['iDisplayLength']) : 25;
	$sort = isset($_POST["iSortTitle_0"]) ? $_POST["iSortTitle_0"] : NULL;
	$order = isset($_POST["sSortDir_0"]) ? $_POST["sSortDir_0"] : NULL;	
	$chr = isset($_POST["sSearch"]) ? $_POST["sSearch"] : NULL;	
	$sEcho = isset($_POST['sEcho']) ? $_POST['sEcho'] : 1;
	$category = isset($_POST['category']) ? $_POST['category'] : 0;
	$subcategory = isset($_POST['subcategory']) ? $_POST['subcategory'] : 0;

	extract($_GET);
	$searchArray = array("page"=>$page, "rows"=>$rows, "sort"=>$sort, "order"=>$order, "offset"=>$page, "chr"=>$chr, 'sEcho' =>$sEcho, 'category' =>$category, 'subcategory' =>$subcategory);
	 
	if($action == "updateStatus") {
		$setVal = array('isActive'=>($value == 'y' ? 'y' : 'n'));
		$db->update($table,$setVal,array("id"=>$id));
		echo json_encode(array('type'=>'success','Record '.($value == 'y' ? 'activated ' : 'deactivated ').'successfully'));
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'status',"action"=>$value);
		add_admin_activity($activity_array);
		exit;
	} else if($action == "delete") {
		$aWhere=array("id"=>$id);
		$db->delete($table,$aWhere);
	}
	else if($action == "view" && in_array('view',$Permission)){
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'view');
		add_admin_activity($activity_array);
	}else if ($action == 'changeSubcate' && $action !='') {
		$content = '';
		$categoryId = $_REQUEST['category'];

		$qrySel=$db->pdoQuery("SELECT id,subcategoryName from tbl_subcategory where isActive = 'y' and categoryId = ".$categoryId."");
		$fetchRes = $qrySel->results();
		$totalRow = $qrySel->affectedRows();	

		$content = '<option name="subcategory" id="subcategory" value="">Select Sub Category</option>';	
				        if($totalRow > 0){ 
							foreach($fetchRes as $fetchRes){
								$subcateId = $fetchRes['id'];
								$subcategoryName = $fetchRes['subcategoryName'];
									
								$content .= '<option value="'.$subcateId.'">'.$subcategoryName.'</option>';
							}
						}
	}else if ($action == 'categoryData' && $action !='') {
		$qrySel = $db->pdoQuery("SELECT id,categoryName FROM tbl_categories WHERE isActive = 'y'")->results();
		foreach ($qrySel as $fetchRes) {
			if($fetchRes['categoryName'] !='')
			{
				$option .= "<option value=".$fetchRes['id'].">".$fetchRes['categoryName']."</option>";
			}
		}
		echo json_encode($option);
	}
	else if ($action == 'subcateData' && $action !='') {
		$qrySel = $db->pdoQuery("SELECT id,subcategoryName FROM tbl_subcategory WHERE isActive = 'y'")->results();
		foreach ($qrySel as $fetchRes) {
			if($fetchRes['subcategoryName'] !='')
			{
				$option .= "<option value=".$fetchRes['id'].">".$fetchRes['subcategoryName']."</option>";
			}
		}
		echo json_encode($option);
	}
	else if ($action == 'deleteImg' && $action !='') {
		$imageId = $_REQUEST['imageId'];
		$qrySel = $db->pdoQuery("delete from tbl_product_image where id = ".$imageId."");
			
	}


	$mainObject = new products($module, $id, NULL, $searchArray, $action);
	extract($mainObject->data);
	echo ($content);
	exit;