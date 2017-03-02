<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	require_once("class.homeContent-nct.php");

	$objPost = new stdClass();
	
	$winTitle = 'Home Content - '.SITE_NM;
	$headTitle = 'Home Content';
	$metaTag = getMetaTags(array("description"=>"Admin Panel",
		"keywords"=>'Admin Panel',
		'author'=>AUTHOR));

	$id = isset($_GET['id']) ? $_GET['id'] : '';
		
	$module = 'homeContent-nct';
	$breadcrumb = array($headTitle);
	chkPermission($module);
	
	$objUser = new homeContent();	
	
	if(isset($_POST["submitChange"])) {
		extract($_POST);
		$data = array();
		$data['step1Title'] = isset($step1Title) ? $step1Title : '';
		$data['step1Desc'] = isset($step1Desc) ? $step1Desc : '';
		$data['step2Title'] = isset($step2Title) ? $step2Title : '';
		$data['step2Desc'] = isset($step2Desc) ? $step2Desc : '';
		$data['step3Title'] = isset($step3Title) ? $step3Title : '';
		$data['step3Desc'] = isset($step3Desc) ? $step3Desc : '';
		$data['whyWellness'] = isset($whyWellness) ? $whyWellness : '';
		$data['whyTitle'] = isset($whyTitle) ? $whyTitle : '';

		$db->update('tbl_home_contant',$data,array("id"=>1));
		$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));

	}
	
	//$pageContent = $objUser->getForm();
	$pageContent = $objUser->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");
	//require_once(DIR_ADMIN_THEME."default.nct");
?>