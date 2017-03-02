<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	require_once("class.returnPolicy-nct.php");

	$objPost = new stdClass();
	
	$winTitle = 'Return Policy - '.SITE_NM;
	$headTitle = 'Return Policy';
	$metaTag = getMetaTags(array("description"=>"Admin Panel",
		"keywords"=>'Admin Panel',
		'author'=>AUTHOR));

	$id = isset($_GET['id']) ? $_GET['id'] : '';
		
	$module = 'returnPolicy-nct';
	$breadcrumb = array($headTitle);
	chkPermission($module);
	
	$objUser = new returnPolicy();	
	
	if(isset($_POST["submitChange"])) {
		extract($_POST);
		$data = array();
		$data['returnPolicy'] = isset($returnPolicy) ? $returnPolicy : '';

		$db->update('tbl_home_contant',$data,array("id"=>1));
		$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));

	}
	
	//$pageContent = $objUser->getForm();
	$pageContent = $objUser->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");
	//require_once(DIR_ADMIN_THEME."default.nct");
?>