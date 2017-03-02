<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	require_once("class.email_templates-nct.php");
		
	$module = "email_templates-nct";
	$table = "tbl_email_templates";
	
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
			'author'=>AUTHOR));
	$breadcrumb = array("Email Templates");			
	
	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;	
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';	
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;	
	
	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Email Templates';
	$winTitle = $headTitle.' - '.SITE_NM;
	
	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->subject= isset($subject) ? $subject : '';
		$objPost->templates= isset($templates) ? $templates : '';
	
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){
					$db->update($table, array("subject"=>$objPost->subject,"templates"=>$objPost->templates), array("id"=>$id));
					
					$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
					add_admin_activity($activity_array);
	
					$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				}else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
				}
			} else {
				if(in_array('add',$Permission)){
					$objPost->updated_date = date('Y-m-d H:i:s');
					$valArray = array("subject"=>$objPost->subject,"templates"=>$objPost->templates,"updated_date"=>$objPost->updated_date);
					$db->insert("tbl_templates", $valArray);
					$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
				}else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
				}
			}
			redirectPage(SITE_ADM_MOD.$module);
	}	
	
	
	$objTemplates = new Templates();
	
	$pageContent = $objTemplates->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");