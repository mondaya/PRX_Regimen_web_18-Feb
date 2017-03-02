<?php
	$content = '';
	require_once("../../../includes-nct/config-nct.php");
	if($adminUserId == 0){die('Invalid request');}
	include("class.stores-nct.php");

	$module = 'stores-nct';
	chkPermission($module);
	$Permission=chkModulePermission($module);
	$table = 'tbl_stores';
	$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : 'datagrid');
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

	if($action == "updateStatus") {
		$setVal = array('isActive'=>($value == 'y' ? 'y' : 'n'));
		$db->update($table, $setVal, array("id"=>$id));
		echo json_encode(array('type'=>'success','Record '.($value == 'y' ? 'activated ' : 'deactivated ').'successfully'));
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'status',"action"=>$value);
		add_admin_activity($activity_array);
		exit;
	} else if($action == "delete") {
		$aWhere=array("id"=>$id);
		$db->delete($table, $aWhere);
		remove_directory(DIR_BANNER.$id);
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'delete');
		add_admin_activity($activity_array);
	} else if($action == "view" && in_array('view',$Permission)){
		$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'view');
		add_admin_activity($activity_array);
	} else if ($action == 'changeSubcate' && $action !='') {
		$content = '';
		$categoryId = implode(',', $_REQUEST['category']);
		//echo $categoryId;exit;
		$qrySel=$db->pdoQuery("SELECT id,subcategoryName from tbl_subcategory where isActive = 'y' and categoryId IN (".$categoryId.")");
		$fetchRes = $qrySel->results();
		$totalRow = $qrySel->affectedRows();	

		$content .= '<select name="subcategoryId[]" id="subcategoryId" class="form-control selectBox-bg required" multiple="multiple">';	
				        if($totalRow > 0){ 
							foreach($fetchRes as $fetchRes){
								$subcateId = $fetchRes['id'];
								$subcategoryName = $fetchRes['subcategoryName'];
									
								$content .= '<option value="'.$subcateId.'">'.$subcategoryName.'</option>';
							}
						}
		$content .= '</select>';
		?>
		<script type="text/javascript">
		    $(document).ready(function() {
		        //$('#categoryId').multiselect();
		        $('#subcategoryId').multiselect();
		    });
		</script>
	<?php }

	$mainObject = new Stores($module, $id, NULL, $searchArray, $action);
	extract($mainObject->data);
	echo ($content);
	exit;