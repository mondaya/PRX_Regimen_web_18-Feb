<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.favoriteCate-nct.php");

$action = $_POST['action'];

if ($action == 'paging') {
	$page = $_POST['page'];
	
	$mainObj = new favoriteCate('favoriteCate-nct',$page);
	$cateList = $mainObj->getcateList();
 	$pagination = $mainObj->getPagination();

	echo $cateList.$pagination;
	
}
