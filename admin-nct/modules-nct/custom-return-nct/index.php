<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");

	include("class.custom-return-nct.php");
	$module = "custom-return-nct";
	$table = "tbl_custom_return";

	$styles = array(array("data-tables/DT_bootstrap.css",SITE_ADM_PLUGIN),
					array("bootstrap-switch/css/bootstrap-switch.min.css",SITE_ADM_PLUGIN));

	$scripts= array("core/datatable.js",
					array("data-tables/jquery.dataTables.js",SITE_ADM_PLUGIN),
					array("data-tables/DT_bootstrap.js",SITE_ADM_PLUGIN),
					array("bootstrap-switch/js/bootstrap-switch.min.js",SITE_ADM_PLUGIN),
					array("data-table-columnFilter.js",SITE_ADM_PLUGIN));

	chkPermission($module);
	$Permission=chkModulePermission($module);

	$metaTag = getMetaTags(array("description"=>"Admin Panel",
			"keywords"=>'Admin Panel',
			"author"=>SITE_NM));

	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;
	$action = isset($_GET["action"]) ? (int)trim($_GET["action"]) : '';

	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Custom Order Return Requests';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);

	//print_r($_GET);exit;
	if($action == 'pay' && $id > 0){

		$qrySel = "SELECT u.id as userId,u.firstName,u.creditAmount,u.pendingAmount,u.email,o.id as productId,o.productName,o.paidAmount FROM tbl_custom_return as r
		 LEFT JOIN tbl_users as u ON(r.userId = u.id)
		 LEFT JOIN tbl_custom_orders as o ON(r.orderId = o.id) where r.adminPaid = 'n' and r.id = ".$id."";

		$qry_fetch = $db->pdoQuery($qrySel);
	 	$fetchRes = $qry_fetch->result();
		$totalRows = $qry_fetch->affectedRows();
		//echo '<pre>';
		//print_r($fetchRes);

		if($totalRows > 0){

			//Update paid status in return table
			$db->update("tbl_custom_return",array("adminPaid"=>"y"),array("id"=>$id));

			//Update user credit and pending amount
			$oldCreditAmount = $fetchRes['creditAmount'];
			$oldPendingAmount = $fetchRes['pendingAmount'];
			$productPrice = $fetchRes['paidAmount'];
			$newCreditAmount = strval($oldCreditAmount + $productPrice);
			$newPendingAmount = strval($oldPendingAmount - $productPrice);
			$db->update("tbl_users",array("creditAmount"=>$newCreditAmount,"pendingAmount"=>$newPendingAmount),array("id"=>$fetchRes['userId']));

			//for dashboard notifications
			//$db->insert("tbl_user_notifications",array("notificationType"=>1,"fromId"=>'Admin',"toId"=>$fetchRes['userId'],"refId"=>$fetchRes['productId'],"amount"=>strval($productPrice),"createdDate"=>date('Y-m-d H:i:s')),array("id"=>$fetchRes['userId']));

			//For email to user
			$contArray = array(
				"USER_NM"=>$fetchRes['firstName'],
				"PRODUCT_NM"=>$fetchRes['productName'],
				"AMOUNT"=>SITE_CURR.$productPrice
			);
			sendMail(base64_decode($fetchRes['email']),"return_refund",$contArray);

			$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Amount credited in user wallet successfully.'));
			redirectPage(SITE_ADM_MOD.$module);
		}else{
			$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Invalid request.'));
			redirectPage(SITE_ADM_MOD.$module);
		}
	}

	$objReturn = new custom_return_request($module);
	$pageContent = $objReturn->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");
//	require_once(DIR_ADMIN_THEME."default.nct");