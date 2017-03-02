<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	include("class.contact_us-nct.php");
	$module = "contact_us-nct";
	$table = "tbl_contactus";

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

	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Contact us';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);
	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->reply_user = (!empty($reply_user)?$reply_user:"");
		if(!empty($objPost->reply_user)) {
			$details = $db->select($table, array('*'), array('id'=>$id))->result();
			$karray = array("greetings"=>$details['first_name'], "MESSAGE"=>$details['message'], "REPLY"=>$reply_user);
			sendMail(base64_decode($details['email']), 'contact_us', $karray);
			$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Reply has been sent successfully'));
		} else {
			$msgType = disMessage(array('type'=>'err','var'=>'fillAllvalues'));
		}
		redirectPage(SITE_ADM_MOD.$module);
	}

	$objUsers=new ContactUs($module);
	$pageContent = $objUsers->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");