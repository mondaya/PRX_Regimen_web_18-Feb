<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");

	include("class.orders-nct.php");
	$module = "orders-nct";
	$table = "tbl_orders";

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

	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Orders';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);


	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->id = isset($id) ? $id : '';
		$objPost->deliveryStatus = isset($deliveryStatus) ? $deliveryStatus : '';
		
		if($objPost->deliveryStatus != ""){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){ 	
					$db->update($table, array("deliveryStatus"=>$objPost->deliveryStatus), array("id"=>$id));

					//Send mail to user
					if($objPost->deliveryStatus == 'd'){
						$orders = $db->pdoQuery('Select u.firstName,u.email,p.productName FROM tbl_orders as o LEFT JOIN tbl_product_deals as p ON(o.productId = p.id) LEFT JOIN tbl_users as u ON(o.userId = u.id) where o.id = '.$id.'')->result();

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

	$objSubcategory = new Orders($module, $id, NULL, array(), $type);
	$pageContent = $objSubcategory->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");
//	require_once(DIR_ADMIN_THEME."default.nct");