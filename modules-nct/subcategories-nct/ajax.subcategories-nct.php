<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.subcategories-nct.php");
$module = 'subcategories-nct';

$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');
if ($action == 'paging') {
	
	$page = $_POST['page'];
	$cateId = $_POST['cateId'];

	$mainObj = new SubCategories('subcategories-nct',$page,$cateId);
	$categoriesList = $mainObj->getSubCategoriesList();
	$pagination = $mainObj->getPagination();

	echo $categoriesList.$pagination;
}
?>