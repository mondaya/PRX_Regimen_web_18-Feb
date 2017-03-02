<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	require_once("class.accountContent-nct.php");

	$objPost = new stdClass();
	
	$winTitle = 'My Account Content - '.SITE_NM;
	$headTitle = 'My Account Content';
	$metaTag = getMetaTags(array("description"=>"Admin Panel",
		"keywords"=>'Admin Panel',
		'author'=>AUTHOR));

	$id = isset($_GET['id']) ? $_GET['id'] : '';
		
	$module = 'accountContent-nct';
	$breadcrumb = array($headTitle);
	chkPermission($module);
	
	$objUser = new accountContent();	
	
	if(isset($_POST["submitChange"])) {
		extract($_POST);
		$data = array();
		$data['profile'] = isset($profile) ? $profile : '';
		$data['paymentHistory'] = isset($paymentHistory) ? $paymentHistory : ''; 
		$data['wallet'] = isset($wallet) ? $wallet : '';
		$data['orders'] = isset($orders) ? $orders : '';
		$data['customOrder'] = isset($customOrder) ? $customOrder : '';
		$data['newCustomOrder'] = isset($newCustomOrder) ? $newCustomOrder : '';
		$data['settings'] = isset($settings) ? $settings : '';
		$data['notifications'] = isset($notifications) ? $notifications : '';
		$data['favoriteCate'] = isset($favoriteCate) ? $favoriteCate : '';
		$data['favoriteStore'] = isset($favoriteStore) ? $favoriteStore : '';
		$data['referral'] = isset($referral) ? $referral : '';
		$data['cart'] = isset($cart) ? $cart : '';

		$db->update('tbl_myaccount_contant',$data,array("id"=>1));
		$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));

	}
	
	//$pageContent = $objUser->getForm();
	$pageContent = $objUser->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");
	//require_once(DIR_ADMIN_THEME."default.nct");
?>