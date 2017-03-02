<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.paymentHistory-nct.php");

$action = $_POST['action'];

if ($action == 'paging') {
	$page = $_POST['page'];
	
	$mainObj = new paymentHistory('paymentHistory-nct',$page);
	$paymentList = $mainObj->getpaymentList();
 	$pagination = $mainObj->getPagination();

	//echo $storeList.$pagination;
	$data = array();
	$data['paymentList'] = $paymentList;
	$data['paging'] = $pagination;

	echo json_encode($data);
	
}
