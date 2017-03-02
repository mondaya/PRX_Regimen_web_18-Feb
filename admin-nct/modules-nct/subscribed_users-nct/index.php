<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	include("class.subscribed_users-nct.php");
	$module = "subscribed_users-nct";
	$table = "tbl_newsletter_subscriber";

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

	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;

	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Subscribed User';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);

	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->id = isset($id) ? $id : '';
		$objPost->fname = isset($firstName) ? $firstName : '';
		$objPost->status = isset($status) ? $status : '';

		if($objPost->fname != "" && strlen($objPost->fname) > 0 ){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){
					$db->update($table, array("firstName"=>$objPost->fname,"isActive"=>$objPost->status), array("id"=>$id));
					$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
					add_admin_activity($activity_array);
					$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				}else{
						$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
					}
			} else {
				if(in_array('add',$Permission)){
					if(getTotalRows($table,"firstName='".$objPost->fname."'",'userId')==0){
						$objPost->created_date = date('Y-m-d H:i:s');
						$valArray = array("firstName"=>$objPost->fname,"isActive"=>$objPost->status,"createdDate"=>$objPost->created_date);
						$id=$db->insert("tbl_users", $valArray)->getLastInsertId();
						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'add');
						add_admin_activity($activity_array);


						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
					}else{
						$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'recExist'));
					}
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

	$objUsers=new SubscribedUsers($module);
	$pageContent = $objUsers->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");