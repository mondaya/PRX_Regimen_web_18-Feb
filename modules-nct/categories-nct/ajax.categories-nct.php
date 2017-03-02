<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.categories-nct.php");
$module = 'categories-nct';

$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');
if ($action == 'paging') {
	
	$page = $_POST['page'];

	$mainObj = new Categories('categories-nct',$page);
	$categoriesList = $mainObj->getCategoriesList();
	$pagination = $mainObj->getPagination();

	echo $categoriesList.$pagination;
}
else if($action == 'favourite'){

	if($sessUserId > 0){

		$favourite = array();
		$favourite['userId'] = $sessUserId;
		$favourite['categoryId'] = (int) isset($_POST['id']) ? $_POST['id'] : 0;
		$value = $_POST['val'];
		
		if($value == 'on'){
		    $db->insert('tbl_favorite_categories', $favourite);	
		}else{
			
			$db->delete('tbl_favorite_categories',array("categoryId"=>$_POST['id'],"userId"=>$sessUserId));
			
		}
		echo 'login';

	}else{
		echo 'notLogin';
	}
	
}
?>