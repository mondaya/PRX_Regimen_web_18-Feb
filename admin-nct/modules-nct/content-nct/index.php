<?php

	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	require_once("class.content-nct.php");
	$module = "content-nct";
	$table = "tbl_content";

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
	$breadcrumb = array("Content");

	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;

	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Content';
	$winTitle = $headTitle.' - '.SITE_NM;

	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->id = isset($id) ? $id : '';
		$objPost->pageName = isset($pageName) ? $pageName : '';
		$objPost->section = isset($section) ? $section : '';
		$objPost->pageTitle = isset($pageTitle) ? $pageTitle : '';
		$objPost->metaKeyword = isset($metaKeyword) ? $metaKeyword : '';
		$objPost->metaDesc = isset($metaDesc) ? $metaDesc : '';
		$objPost->pageDesc = isset($pageDesc) ? $pageDesc : '';
		$objPost->isActive = isset($isActive) ? $isActive : 'n';

		if($objPost->pageTitle != "" && preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\_\-]*$/',$objPost->pageName)){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){
					
						$db->update($table, array("pageName"=>$objPost->pageName,"section"=>$objPost->section,"pageTitle"=>$objPost->pageTitle,"metaKeyword"=>$objPost->metaKeyword,"metaDesc"=>$objPost->metaDesc,"pageDesc"=>$objPost->pageDesc,"isActive"=>$objPost->isActive), array("pId"=>$id));
						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
						add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
					
					
				}
				else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'NoPermission'));
				}
			} else {
				if(in_array('add',$Permission)){
					
						$objPost->createdDate = date('Y-m-d H:i:s');
						$valArray = array("pageName"=>$objPost->pageName,"section"=>$objPost->section,"pageTitle"=>$objPost->pageTitle,"metaKeyword"=>$objPost->metaKeyword,"metaDesc"=>$objPost->metaDesc,"pageDesc"=>$objPost->pageDesc,"isActive"=>$objPost->isActive,"createdDate"=>$objPost->createdDate);
						$id=$db->insert("tbl_content", $valArray)->getLastInsertId();
						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'add');
						add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
					
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
	$objContent = new Content($module);
	$pageContent = $objContent->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");
	//require_once(DIR_ADMIN_THEME."default.nct");