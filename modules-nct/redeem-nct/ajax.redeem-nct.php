
<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.redeem-nct.php");

$action = $_POST['action'];

if ($action == 'paging') {
	$page = $_POST['page'];
	
	$mainObj = new redeem('redeem-nct',$page);
	$redeemRequest = $mainObj->getRedeemRequest();
	$pagination = $mainObj->getPagination();

	$data = array();
	$data['requetList'] = $redeemRequest;
	$data['paging'] = $pagination;
	echo json_encode($data);
	
}

