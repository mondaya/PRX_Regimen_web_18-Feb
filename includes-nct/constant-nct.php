<?php
	$sqlSettings=$db->select("tbl_site_settings",array("constant","value"))->results();
	foreach($sqlSettings as $conskey=>$consval){
		define($consval["constant"],$consval["value"]);
	}

	define('FROM_NM', SITE_NM);
	define('AUTHOR', SITE_NM);
	define('REGARDS',SITE_NM);
	define('SITE_CURR','$');
	
	//Paypal data
	define("LIVE_PAYPAL_MODE","OFF");
	//define('PAYPAL_URL','http://www.sandbox.paypal.com/cgi-bin/webscr/');
	define('PAYPAL_CURRENCY','USD');
	
	//define("PAYPAL_API_APP_ID",'APP-80W284485P519543T');

	define("API_VERSION","95.0");

	//for android  and iphone payment varify credential for webservice only
	
	define('PAYPAL_APP_URL_OAUTH2','https://api.sandbox.paypal.com/v1/oauth2/token');
	define('PAYPAL_APP_URL_PAYMENT','https://api.sandbox.paypal.com/v1/payments/payment/');	
	//end for android and iphone payment varify credential

	/* for client */
	define("SITE_INC", SITE_URL."includes-nct/");
	define('CROP_PATH', SITE_INC.'crop.php');
	define("SITE_UPD", SITE_URL."upload-nct/");
	define("SITE_MOD", SITE_URL."modules-nct/");
	define("SITE_JS", SITE_INC."javascript-nct/");
	define("SITE_IMG", SITE_URL."themes-nct/images/");
	define("SITE_CSS", SITE_URL."themes-nct/css/");
	define("SITE_FONT", SITE_URL."themes-nct/fonts/");
	define("SITE_BANNER", SITE_UPD."banner-nct/");
	define("SITE_STORE", SITE_UPD."store-nct/");
	define("SITE_SLIDER", SITE_UPD."sliderImage/");
	define("SITE_UPD_USER", SITE_UPD."profile/");
	define("SITE_UPD_CATEGORY", SITE_UPD."category/");
	define("SITE_UPD_SUBCATEGORY", SITE_UPD."subcategory/");
	define("SITE_UPD_PRODUCT", SITE_UPD."product/");
	define("SITE_SOCIAL", SITE_INC."social-nct/");


	define("DIR_INC", DIR_URL."includes-nct/");
	define("DIR_FUN", DIR_URL."includes-nct/functions-nct/");
	define("DIR_MOD", DIR_URL."modules-nct/");
	define("DIR_THEME", DIR_URL."themes-nct/");
	define("DIR_TMPL", DIR_URL."templates-nct/");
	define("DIR_IMG", DIR_URL."themes-nct/images/");
	define("DIR_UPD", DIR_URL."upload-nct/");
	define("DIR_UPD_USER", DIR_UPD."profile/");
	define("DIR_FONT", DIR_URL."themes-nct/fonts/");
	define("DIR_BANNER", DIR_URL."upload-nct/banner-nct/");
	define("DIR_STORE", DIR_URL."upload-nct/store-nct/");
	define("DIR_SLIDER", DIR_URL."upload-nct/sliderImage/");
	define("DIR_UPD_CATEGORY", DIR_UPD."category/");
	define("DIR_UPD_SUBCATEGORY", DIR_UPD."subcategory/");
	define("DIR_UPD_PRODUCT", DIR_UPD."product/");

	/* for admin */
	define("SITE_ADM_CSS", SITE_ADMIN_URL."themes-nct/css/");
	define("SITE_ADM_IMG", SITE_ADMIN_URL."themes-nct/img/");
	define("SITE_ADM_INC", SITE_ADMIN_URL."includes-nct/");
	define("SITE_ADM_MOD", SITE_ADMIN_URL."modules-nct/");
	define("SITE_ADM_JS", SITE_ADMIN_URL."includes-nct/scripts-nct/");
	define("SITE_ADM_PLUGIN", SITE_ADMIN_URL."includes-nct/plugins-nct/");
	define("SITE_ADM_UPD", SITE_ADMIN_URL."upload-nct/");

	define("DIR_ADMIN_THEME", DIR_ADMIN_URL."themes-nct/");
	define("DIR_ADMIN_TMPL", DIR_ADMIN_URL."templates-nct/");
	define("DIR_ADM_INC", DIR_ADMIN_URL."includes-nct/");
	define("DIR_ADM_MOD", DIR_ADMIN_URL."modules-nct/");
	define("DIR_ADMIN_FIELDS_HTML", DIR_ADMIN_TMPL."fields_html-nct/");
	define("DIR_FIELDS_HTML", DIR_TMPL."fields_html-nct/");
	define('DIR_IMGLOGO',DIR_URL."themes-nct/images-nct/Sitelogo/");

	define('LOADER',SITE_IMG.'loader.gif');

	define('LIMIT',40);
	define('VISIBLE_PAGES', 7);
	define('CONTENT_LIMIT', 200);
	define('ADMIN_LIMIT', 5);
	define('SLEEP', 1);
	define('STRING_LIMIT', 200); //characters
	define('MEND_SIGN', '<font color="#FF0000">*</font>');
	define('CURRENCY_SIGN', SITE_CURR);

	define('RNF', 'No results found.');
	define('LI_NMRF','<li class="txtCenter"><span>No result found</span></li>');
	define('NMRF', '<div class="NMRF">No more results found.</div>');
	define('NBAY', '<div class="NMRF">You have not bought anything yet.</div>');

	define('DB_TABLE', 'tbl_temp_uploads'); //Table name to store upload data
	define('UPLOAD_DIR', '../files/'); //Files path
	define('IMG_MAX_WIDTH', 6000); //Image max width
	define('IMG_MAX_HEIGHT', 4000); //Image max height
	define('THUMBNAIL_WIDTH', 528); //Thumbnail width
	define('THUMBNAIL_HEIGHT', 528); //Thumnail height
	define('UPLOAD_LIMIT', 3); //Max number of images that can be uploaded 0,1,2,3, .....
	define('ACCEPT_FILE_TYPES', 'jpeg|jpg|png|gif'); //Filetypes allowed for upload

	// for socail login
	define('BASE_URL', filter_var(SITE_SOCIAL, FILTER_SANITIZE_URL));
	define('REDIRECT_URI',SITE_SOCIAL.'login.php?google');
	define('APPROVAL_PROMPT','auto');
	define('ACCESS_TYPE','offline');


	//For notification constant
	$notification = $db->select("tbl_notifications","*",array("userId"=>$sessUserId))->result();
	define('NEWPRODUCT',$notification['newProductPosted']);
	define('AMOUNTWALLET',$notification['amountAddedInWallet']);
	define('RETURNREPLAY',$notification['receiveReplayFromAdmin']);
	define('NEWPROMO',$notification['newPrormoPosted']);
	define('CUSTOMSTATUS',$notification['orderStatusByAdmin']);
	define('REMINDER',$notification['reminder']);
?>