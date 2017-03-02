<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.notifications-nct.php");

$action = $_POST['action'];

if ($action == 'paging') {
	$page = $_POST['page'];
	
	$mainObj = new notifications('notifications-nct',$page);
	$notificationList = $mainObj->getnotificationList();
 	$pagination = $mainObj->getPagination();

	echo $notificationList.$pagination;
	
}
