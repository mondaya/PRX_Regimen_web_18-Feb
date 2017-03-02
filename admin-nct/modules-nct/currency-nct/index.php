<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	include("class.currency-nct.php");	
	$module = "currency-nct";
	$table = "tbl_currency";	
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
	
	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Currency';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);		
	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->currency = isset($currency) ? $currency : '';
		$objPost->code = isset($code) ? $code : '';
		$objPost->sign = isset($sign) ? $sign : '';
		$objPost->currencyValue = isset($currencyValue) ? $currencyValue : '';
		$objPost->isactive	= isset($isactive) && $isactive == 'y' ? 'y' : 'n';
	
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){ 	
					$db->update($table, array('currencyValue'=>$objPost->currencyValue,'isactive'=>$objPost->isactive), array("id"=>$id));
					$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
				add_admin_activity($activity_array);
					$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				}
				else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));	
				}
			} else {
				if(in_array('add',$Permission)){
					if(getTotalRows($table,array('currency'=>$objPost->currency))==0){
						$valArray = array("currency"=>$objPost->currency,"code"=>$objPost->code,"sign"=>$objPost->sign,'currencyValue'=>$objPost->currencyValue,"isactive"=>$objPost->isactive);
						$id=$db->insert("tbl_currency", $valArray)->getLastInsertId();
						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'add');
						add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
					}else{
						$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'recExist'));
					}
				}
				else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
				}
			}
			redirectPage(SITE_ADM_MOD.$module);
		
	}
	$objCountry = new currency($module, $id, NULL, $searchArray, $type);
	$pageContent = $objCountry->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");	
	//require_once(DIR_ADMIN_THEME."default.nct");