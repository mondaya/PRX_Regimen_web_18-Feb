<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	include("class.defaultAmount-nct.php");	
	$module = "defaultAmount-nct";
	$table = "tbl_admin_amount";	
	$styles = array(array("data-tables/DT_bootstrap.css",SITE_ADM_PLUGIN),
					array("bootstrap-switch/css/bootstrap-switch.min.css",SITE_ADM_PLUGIN));
	
	$scripts= array("core/datatable.js",
					array("data-tables/jquery.dataTables.js",SITE_ADM_PLUGIN),
					array("data-tables/DT_bootstrap.js",SITE_ADM_PLUGIN),
					array("bootstrap-switch/js/bootstrap-switch.min.js",SITE_ADM_PLUGIN));
	
	chkPermission($module);
	$Permission=chkModulePermission($module);
	$metaTag = getMetaTags(array("description"=>"Admin Panel",
			"keywords"=>'Admin Panel',
			"author"=>SITE_NM));
	
	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;	
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';	
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;	
	
	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Default Amount';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);		
	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		
		$data['shipping'] = isset($shipping) ? $shipping : '';
		$data['duties'] = isset($duties) ? $duties : '';
		$data['adminCharge'] = isset($adminCharge) ? $adminCharge : '';
	
			
			if($type== 'edit' && $id > 0){
					if(in_array('edit',$Permission)){ 	
					$db->update($table, $data, array("id"=>$id));
					$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
				add_admin_activity($activity_array);
					$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				}
				else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));	
				}
			}
			redirectPage(SITE_ADM_MOD.$module);
	}
	
	$objCommission = new defaultAmount($module, $id, NULL, $searchArray, $type);
	$pageContent = $objCommission->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");	
	//require_once(DIR_ADMIN_THEME."default.nct");