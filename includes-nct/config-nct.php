<?php 
	error_reporting(0);
	ob_start();
	session_start();
	set_time_limit(0);
	session_set_cookie_params (3600);
	session_name('wellness');

	define('DEFAULT_TIME_ZONE','Asia/Kolkata');
	date_default_timezone_set(DEFAULT_TIME_ZONE);
	if($_SERVER["SERVER_NAME"] == 'nct23' || $_SERVER["SERVER_NAME"] == 'nct75' || $_SERVER["SERVER_NAME"] == 'localhost') {
		//error_reporting(0);
	} else {
		//error_reporting(0);
	}
	define("DB_CHAR","utf8");
	define("DB_DEBUG",false);

	global $db,$helper,$fields,$module, $adminUserId, $sessUserId,$lId,$abuse_array,$abuse_array_value,$objHome,$page_name,$module,$breadcrumb,$Permission,$msgType,$currencyId,$currencyCode,$currencySign,$sessUserName;
	global $head,$header,$left,$right,$footer,$content,$title;

	$header_panel=true;
	$footer_panel=true;
	$left_panel=false;
	$right_panel=false;
	$slider_panel = false;
	$isPopup=false;
	$styles = array();
	$scripts = array();

	$reqAuth = isset($reqAuth) ? $reqAuth : false;
	$clsContainer = isset($clsContainer) ? $clsContainer : true;
	$adminUserId = (isset($_SESSION["adminUserId"]) && $_SESSION["adminUserId"] > 0 ? (int)$_SESSION["adminUserId"] : 0);
	$sessUserId = (isset($_SESSION["sessUserId"]) && $_SESSION["sessUserId"] > 0 ? (int)$_SESSION["sessUserId"] : 0);
	$sessUserType = (isset($_SESSION["sessUserType"]) && $_SESSION["sessUserType"] != '' ? $_SESSION["sessUserType"] : 'u');
	$msgType = isset($_SESSION["msgType"])?$_SESSION["msgType"]:NULL;
	$lId = (isset($_SESSION["lId"]) && $_SESSION["lId"] > 1 ? (int)$_SESSION["lId"] : $_SESSION["lId"] = 1);
	$currencyId = (isset($_SESSION["currencyId"]) && !empty($_SESSION["currencyId"]) ? (int)$_SESSION["currencyId"] : $_SESSION["currencyId"] = 1);
	$currencyCode = (isset($_SESSION["currencyCode"]) && !empty($_SESSION["currencyCode"]) ? $_SESSION["currencyCode"] : $_SESSION["currencyCode"] = 'USD');
	$currencySign = (isset($_SESSION["currencySign"]) && !empty($_SESSION["currencySign"]) ? $_SESSION["currencySign"] : $_SESSION["currencySign"] = '$');
	$sessUserName = ((isset($_SESSION['sessUserName']) && !empty($_SESSION['sessUserName']))?$_SESSION['sessUserName']:'');

	unset($_SESSION['msgType']);
	require_once('database-nct.php');
	require_once('functions-nct/class.pdohelper.php');
	require_once('functions-nct/class.pdowrapper.php');
	$dbConfig = array("host"=>DB_HOST, "dbname"=>DB_NAME, "username"=>DB_USER, "password"=>DB_PASS);
	$db = new PdoWrapper($dbConfig);
	$helper = new PDOHelper();
	$db->setErrorLog(true);

	require_once('constant-nct.php');
	require_once('help-constant-nct.php');
	require_once('language-nct/'.$lId.'.php');
	require_once('functions-nct/function-nct.php');
	Authentication($reqAuth);

	require("class.main_template.php");
	require_once('class.template.php');
	$main = new MainTemplater();
	if(domain_details('dir') =='admin-nct') {
		$left_panel=true;
		require_once(DIR_ADM_INC.'functions-nct/fields-nct.php');
		require_once(DIR_ADM_INC.'functions-nct/admin-function-nct.php');
		require_once(DIR_ADM_MOD.'home-nct/class.home-nct.php');
		$objHome = new Home();
	} else {
		require_once(DIR_INC.'functions-nct/fields-nct.php');
		require_once(DIR_MOD.'home-nct/class.home-nct.php');
		$objHome = new Home(0);
	}
	$fields = new fields();
	$objPost = new stdClass();

	//Get site currency id
	$siteCurrencyId = getTableValue('tbl_currency','id',array('currency'=>'USD'));
	define('SITE_CURR_ID',$siteCurrencyId);
	$usdCurrencyId = getTableValue('tbl_currency','id',array('currency'=>'USD'));
	define('USD_CURR_ID',$usdCurrencyId);
	$returnPolicy = getTableValue('tbl_home_contant','returnPolicy',array('id'=>'1'));

	define('RETURN_POLICY',$returnPolicy);
	//echo SITE_CURR_ID;

	//For default amounts
	$fees = $db->pdoQuery("select * from tbl_admin_amount where id = 1")->result();
	define('DEFAULT_SHIPPING',$fees['shipping']);
	define('DEFAULT_DUTIES',$fees['duties']);
	define('DEFAULT_ADMIN_CHARGE',$fees['adminCharge']);
	define("ANDROID_APP_KEY","AIzaSyD6diHzSi1CW6kdiNfH9mDT1CLryXSyWI8");
	require_once('class.pushnotification.php');
	$pushObj= new pushmessage();
	