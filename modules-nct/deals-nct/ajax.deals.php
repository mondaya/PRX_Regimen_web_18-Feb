<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.deals.php");

$action = $_POST['action'];

if ($action == 'paging') {
	$searchText = $_POST['searchText'];
	$page = $_POST['page'];
	$cateId = $_POST['cateId'];
	$subCateId = $_POST['subCateId'];
	
	$mainObj = new deals('deals-nct',$searchText,$cateId,$subCateId,$page);
	$dealList = $mainObj->getDealsList();
 	$pagination = $mainObj->getPagination();

	echo $dealList.$pagination;
}
else if($action == 'add-to-cart'){
	$data = array();
	$dealId = $_POST['dealId'];
	if($sessUserId > 0){

		$alreadyExist = getTotalRows('tbl_cart',array("productId"=>$dealId,"userId"=>$sessUserId),'id');
		if($alreadyExist == 0){

			$data = array();
			$data['userId'] = $sessUserId;
			$data['productId'] = $dealId;
			$data['quantity'] = '1';
			$data['createdDate'] = date('Y-m-d h:i:s');
			$db->insert('tbl_cart',$data);

			$cartProduct = getTotalRows('tbl_cart',array("userId"=>$sessUserId),'id');

			$data['cartProduct'] = $cartProduct;
			$data['status'] = 's';
		}else{
			$data['status'] = 'a';
		}

	}else{
		$data['status'] = 'n';
	}

	echo json_encode($data);
}

