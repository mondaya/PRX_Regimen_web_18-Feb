<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	include("class.city-nct.php");	
	$module = "city-nct";
	$table = "tbl_city";
	
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
	
	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Cities';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);		
	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->cityName = isset($cityName) ? $cityName : '';
		$objPost->StateID = isset($state) ? $state : '';
		$objPost->CountryID = isset($country) ? $country : '';
		$objPost->isActive	= isset($isActive) && $isActive == 'y' ? 'y' : 'n';
	
		if($objPost->cityName != ""){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){ 	
					$db->update($table, array('cityName'=>$objPost->cityName,'StateID'=>$objPost->StateID,'CountryID'=>$objPost->CountryID,'isActive'=>$objPost->isActive), array("id"=>$id));
					$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
				add_admin_activity($activity_array);
					$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				}else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));	
				}
			} else {
				if(in_array('add',$Permission)){
					
					if(getTotalRows($table,array('cityName'=>$objPost->cityName))==0){

						$valArray = array("cityName"=>$objPost->cityName,"StateID"=>$objPost->StateID,"CountryID"=>$objPost->CountryID,"isActive"=>$objPost->isActive);
						$id=$db->insert("tbl_city", $valArray)->getLastInsertId();
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
		else {
			$msgType = array('type'=>'err','var'=>'fillAllvalues');
		}
	}	
	$objCity = new City($module, $id, NULL, NULL, $type);
	$pageContent = $objCity->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");	
	//require_once(DIR_ADMIN_THEME."default.nct");