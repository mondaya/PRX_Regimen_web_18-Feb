<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.myOrder.php");

$action = $_POST['action'];

if ($action == 'paging') {
	$searchText = $_POST['searchText'];
	$page = $_POST['page'];
	$date = $_POST['date'];
	$status = $_POST['status'];
	
	$mainObj = new myOrder('myOrder-nct',$searchText,$date,$status,$page);
	$orderList = $mainObj->getOrderList();
 	$pagination = $mainObj->getPagination();

 	$data = array();
	$data['orderList'] = $orderList;
	$data['paging'] = $pagination;

	echo json_encode($data);
}
