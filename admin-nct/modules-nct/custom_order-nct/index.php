<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");

	include("class.custom_order-nct.php");
	$module = "custom_order-nct";
	$table = "tbl_custom_orders";

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

	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Custom Orders';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);

	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->id = isset($id) ? $id : '';
		$objPost->order_status = isset($order_status) ? $order_status : '';
		$objPost->deliveryStatus = isset($deliveryStatus) ? $deliveryStatus : '';
		
		if($objPost->order_status != ""){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){ 	
					$db->update($table, array("order_status"=>$objPost->order_status,"deliveryStatus"=>$objPost->deliveryStatus), array("id"=>$id));

					//For notification
					$userId = getTableValue("tbl_custom_orders","userId",array("id"=>$objPost->id));
    				$value = $db->pdoQuery("Select id,firstName,email from tbl_users where id=".$userId."")->result();

    				$orderStatusByAdmin = getTableValue("tbl_notifications","orderStatusByAdmin",array("userId"=>$value['id']));

    				//For dashboard notifications
    				$db->insert("tbl_user_notifications",array("notificationType"=>6,"fromId"=>'',"toId"=>$value['id'],"refId"=>$id,"createdDate"=>date("Y-m-d H:i:s")));

    				//For email notification
    				$productName = getTableValue("tbl_custom_orders","productName",array("id"=>$id));
    				$status = $objPost->order_status == 'a'?'accepted':'rejected';

    				if($orderStatusByAdmin == 'y' && $objPost->deliveryStatus == 'p'){
						$contArray = array(
							"USER_NM"=>$value['firstName'],
							"PRODUCT_NM"=>$productName,
							"STATUS"=>$status
						);
						sendMail(base64_decode($value['email']),"custom_order_status",$contArray);
					}

					if($objPost->deliveryStatus == 'd'){
						$orders = $db->pdoQuery('Select u.firstName,u.email,o.productName FROM tbl_custom_orders as o LEFT JOIN tbl_users as u ON(o.userId = u.id) where o.id = '.$id.'')->result();
						//print_r($orders);exit;
						$contArray = array(
							"USER_NM"=>$orders['firstName'],
							"PRODUCT_NM"=>$orders['productName']
						);
						sendMail(base64_decode($orders['email']),"product_delivered",$contArray);
					}
    				


					$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
					add_admin_activity($activity_array);
					$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				}else{
						$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
					}	
			}
			redirectPage(SITE_ADM_MOD.$module);
		}
		else {
			$msgType = array('type'=>'err','var'=>'fillAllvalues');
		}
	}

	$objSubcategory = new CustomOrders($module, $id, NULL, array(), $type);
	$pageContent = $objSubcategory->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");