<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.favoriteStore-nct.php");

$action = $_POST['action'];

if ($action == 'paging') {
	$page = $_POST['page'];
	
	$mainObj = new favoriteCate('favoriteStore-nct',$page);
	$storeList = $mainObj->getstoreList();
 	$pagination = $mainObj->getPagination();

	echo $storeList.$pagination;
	
}
