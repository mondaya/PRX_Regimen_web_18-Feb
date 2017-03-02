<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");

	include("class.duties-nct.php");
	$module = "duties-nct";
	$table = "tbl_duties_amount";
	
	$styles = array(array("data-tables/DT_bootstrap.css",SITE_ADM_PLUGIN),
					array("bootstrap-switch/css/bootstrap-switch.min.css",SITE_ADM_PLUGIN),
					array("image_crop_css/cropper.css",SITE_ADM_PLUGIN),
					array("image_crop_css/main.css",SITE_ADM_PLUGIN));
	
	$scripts= array("core/datatable.js",
					array("data-tables/jquery.dataTables.js",SITE_ADM_PLUGIN),
					array("data-tables/DT_bootstrap.js",SITE_ADM_PLUGIN),
					array("bootstrap-switch/js/bootstrap-switch.min.js",SITE_ADM_PLUGIN),
					array("image_crop/cropper.js",SITE_ADM_PLUGIN),
					array("image_crop/main.js",SITE_ADM_PLUGIN),
					array("image_crop/uploadimage.js",SITE_ADM_PLUGIN));
	
	chkPermission($module);
	$Permission=chkModulePermission($module);
	
	$metaTag = getMetaTags(array("description"=>"Admin Panel",
			"keywords"=>'Admin Panel',
			"author"=>SITE_NM));
	
	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;	
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';	
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;	
	
	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Duties and Handling Amount';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);		
	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->countryId = isset($country) ? $country : '';
		$objPost->amount = isset($amount) ? $amount : '';
		$objPost->minimumAmount = isset($minimumAmount) ? $minimumAmount : '';
		$objPost->isActive	= isset($isActive) && $isActive == 'y' ? 'y' : 'n';
		$objPost->createdDate = date("Y-m-d H:i:s");

		
		if($objPost->countryId != ""){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){

						
						$data['countryId'] = $objPost->countryId;
						$data['amount'] = $objPost->amount;
						$data['minimumAmount'] = $objPost->minimumAmount;
						$data['isActive'] = $objPost->isActive;

						$db->update($table, $data, array("id"=>$id));
						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
					add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
					
				}
				else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));		
				}
			} else {
				if(in_array('add',$Permission)){

						$exist = getTableValue('tbl_duties_amount','id',array('countryId'=>$objPost->countryId));

						if($exist > 0){
							$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'recExist'));
							redirectPage(SITE_ADM_MOD.$module);
						}
					
						$data['countryId'] = $objPost->countryId;
						$data['amount'] = $objPost->amount;
						$data['isActive'] = $objPost->isActive;
						$data['minimumAmount'] = $objPost->minimumAmount;
						$data['createdDate'] = $objPost->createdDate;

						$db->insert($table, $data);
						

						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'add');
						add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
					
					
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
	$objSubcategory = new duties($module, $id, NULL, $searchArray, $type);
	$pageContent = $objSubcategory->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");	
//	require_once(DIR_ADMIN_THEME."default.nct");