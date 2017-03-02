<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.transactionDetail-nct.php");

	$id = isset($_GET['id']) && $_GET['id']!=''?$_GET['id']:'';
	$action = isset($_GET['action']) && $_GET['action']!=''?$_GET['action']:"";

	//Check for product exist
	$exist = getTotalRows('tbl_orders',array("transactionId"=>$id),'id');

	if($exist == 0){
		$_SESSION['msgType'] = disMessage(array('type'=>'err','var'=>"Invalid request"));
		redirectPage(SITE_URL);
	}
	
	$table = "tbl_product_deals";
	$module = 'transactionDetail-nct';

	$winTitle = 'Transaction Detail '.SITE_NM;
	$headTitle = 'Transaction Detail';
	$metaTag = getMetaTags(array("description"=>$winTitle,"keywords"=>$headTitle,"author"=>AUTHOR));

	$mainObj = new transactionDetail($module,$id);

	$pageContent = $mainObj->getPageContent();
		
 	$fields = array(
 	);

	$fields_replace = array(
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");
	
	
?>