<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	require_once("class.cPass-nct.php");

	$objPost = new stdClass();
	
	$winTitle = 'Change Password - '.SITE_NM;
	$headTitle = 'Change Password';
	$metaTag = getMetaTags(array("description"=>"Admin Panel",
		"keywords"=>'Admin Panel',
		'author'=>AUTHOR));
		
	$module = 'cPass-nct';
	$breadcrumb = array($headTitle);
	chkPermission($module);
	
	extract($_POST);
	$objPost->opasswd = isset($opasswd) ? $opasswd : '';
	$objPost->passwd = isset($passwd) ? $passwd : ''; 
	$objPost->cpasswd = isset($cpasswd) ? $cpasswd : '';
	$objPost->passvalue = isset($passvalue) ? $passvalue : '';
	
	$objUser = new cPass();	
	
	if(isset($_POST["submitChange"])) {
		if($objPost->opasswd != "" && $objPost->passwd != "" && $objPost->cpasswd != "") {
			$changeReturn = $objUser->submitProcedure();
			switch ($changeReturn) {
				case 'wrongPass' : $msgType = disMessage(array('from'=>'admin','type'=>'error','var'=>'wrongPass')); break;
				case 'passNotmatch' : $msgType = disMessage(array('from'=>'admin','type'=>'error','var'=>'passNotmatch')); break;
				case 'succChangePass' : { 
					$_SESSION["msgType"] = disMessage(array('from'=>'admin','type'=>'suc','var'=>'succChangePass'));
					redirectPage(SITE_ADM_MOD.$module);
					break; 
				}
			}
		}
	}
	
	//$pageContent = $objUser->getForm();
	$pageContent = $objUser->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");
	//require_once(DIR_ADMIN_THEME."default.nct");
?>