<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.stores-nct.php");
$module = 'stores-nct';

$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');
if ($action == 'paging') {
	
	$page = $_POST['page'];
	$cateId = $_POST['cateId'];
	$subCateId = $_POST['subCateId'];

	$mainObj = new Stores('stores-nct',$page,$cateId,$subCateId);
	$storeList = $mainObj->getStoreList();
	$pagination = $mainObj->getPagination();

	echo $storeList.$pagination;
}
else if($action == 'favourite'){

	if($sessUserId > 0){
		
		$favourite = array();
		$favourite['userId'] = $sessUserId;
		$favourite['storeId'] = (int) isset($_POST['id']) ? $_POST['id'] : 0;
		$value = $_POST['val'];
		
		if($value == 'on'){
		    $db->insert('tbl_favourite_store', $favourite);	
		}else{
			
			$db->delete('tbl_favourite_store',array("storeId"=>$_POST['id'],"userId"=>$sessUserId));
			
		}
		echo 'login';

	}else{
		echo 'notLogin';
	}
	
}
?>