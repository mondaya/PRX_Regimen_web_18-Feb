<?php
	
	$reqAuth=false;
	require_once("../../../includes-nct/config-nct.php");
	require_once("class.login-nct.php");
	if($adminUserId > 0) {
		redirectPage(SITE_ADM_MOD.'home-nct');
	}

	$header_panel = false;
	$left_panel = false;
	$footer_panel = false;
	
	$winTitle = 'Login - '.SITE_NM;
	$headTitle = 'Login';
	$styles = array("pages/login.css");
	$scripts = array("custom/login.js");
	$module = 'login-nct';
	$metaTag = getMetaTags(array("description"=>"Admin Panel",
		"keywords"=>'Admin Panel',
		'author'=>AUTHOR));
	
	$objUser = new Login();	
	
	if(isset($_POST["submitEmail"])) {
		
		extract($_POST);
		$objPost->uEmail = isset($uEmail) ? $uEmail : '';
		if($objPost->uEmail != "") {
			$loginReturn1 = $objUser->forgotProdedure();
			switch ($loginReturn1) {
				case 'succForgotPass' : {$msgType=$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'succForgotPass')); redirectPage(SITE_ADM_MOD.$module.'/'); break; }
				case 'wrongUsername' : { $msgType=$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'wrongemail')); redirectPage(SITE_ADM_MOD.$module.'/'); break;}
			}
		}else{
			//$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'fillAllvalues'));
		}
	}
	
	if(isset($_POST["submitLogin"])) {
	
		extract($_POST);
		$objPost->uName = isset($uName) ? $uName : '';
		$objPost->uPass = isset($uPass) ? $uPass : '';
		$objPost->isRemember = isset($remember) ? $remember : '';
	
		if($objPost->isRemember == 'y') {
			setcookie('remember', 'y', time()+3600);			
			setcookie('uName', $objPost->uName, time()+3600);
			setcookie('uPass', $objPost->uPass, time()+3600);
		}
		else {
			setcookie('remember', '');			
			setcookie('uName', '');
			setcookie('uPass', '');
		}
	
		if($objPost->uName != "" && $objPost->uPass != "") {
			$objUser = new Login();
			$loginReturn = $objUser->loginSubmit();
			
			switch($loginReturn) {
				case 'invaildUsers' : $msgType = disMessage(array('type'=>'err','var'=>'invaildUsers')); break;
				case 'inactivatedUser' : $msgType = disMessage(array('type'=>'err','var'=>'inactivatedUser')); break;
				case 'invaildUsersAd' : $msgType = disMessage(array('type'=>'err','var'=>'invaildUsersAd')); break;				
			}
		}
	}
	
	if($msgType == '' && isset($_SESSION['req_uri_adm']) && $_SESSION['req_uri_adm']!=''){
		if(!isset($_SESSION['loginDisplayed_adm'])){
			$msgType = array('type'=>'err','var'=>'loginToContinue');
			$_SESSION['loginDisplayed_adm'] = 1;
		}
	}
	
	if(isset($_COOKIE["remember"]) && $_COOKIE["remember"] == 'y') {
		$objPost->uName = isset($_COOKIE["uName"]) ? $_COOKIE["uName"] : '';
		$objPost->uPass = isset($_COOKIE["uPass"]) ? base64_decode($_COOKIE["uPass"]) : '';
		$objPost->isRemember = 'y';	
	}
	
	$pageContent = $objUser->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");	