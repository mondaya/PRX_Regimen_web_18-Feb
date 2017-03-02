<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.redeem-nct.php");
	
	$module = 'redeem-nct';
	$table = 'tbl_orders';
	
	$winTitle = 'My Redeem Request -' .SITE_NM;
    $headTitle = 'My Redeem Request';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));

    $page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;


    if(isset($_POST['submitRedeem']) && $_POST['submitRedeem'] == 'Submit'){
    	extract($_POST);

    	$paypalEmail = getTableValue('tbl_users','paypalEmail',array('id'=>$sessUserId));

    	if($paypalEmail == ''){
    		$_SESSION['msgType'] = disMessage(array('type' => 'err', 'var' => 'Please update your paypal email before redeem request.'));
    		redirectPage(SITE_URL.'editprofile/'.$sessUserId);
    	}
		
		//Insert in request table
    	$data = array();
    	$data['userId'] = $sessUserId;
    	$data['amount'] = strval($amount);
    	$data['createdDate'] = date('Y-m-d H:i:s');
    	$db->insert('tbl_redeem_request',$data);

    	//update pending and credit amount
    	$oldRedeem = getTableValue('tbl_users','redeemAmount',array("id"=>$sessUserId));
    	$oldCredit = getTableValue('tbl_users','creditAmount',array("id"=>$sessUserId));

    	$finalRedeem = strval($oldRedeem + $amount);
    	$finalCredit = strval($oldCredit - $amount);
    	$db->update('tbl_users',array("redeemAmount"=>$finalRedeem,"creditAmount"=>$finalCredit),array("id"=>$sessUserId));

    	//Send mail to admin
    	$contArray = array(
			"USER_NM"=>$sessUserName,
			"AMOUNT"=>SITE_CURR.$amount
		);
		sendMail(ADMIN_EMAIL,"redeem_request",$contArray);

    	$msgType = disMessage(array('type' => 'suc', 'var' => 'Request sent successfully.'));


    }
		
	
	
	$mainObj = new redeem($module,$page);

	$pageContent = $mainObj->getPageContent();
	$redeemRequest = $mainObj->getRedeemRequest();
	$pagination = $mainObj->getPagination();

	$creditAmount = getTableValue('tbl_users',"creditAmount",array("id"=>$sessUserId));

	$fields = array(
		'%REQUEST_LIST%','%SITE_CURR%',"%CREDIT_AMT%","%PAGINATION%"
	);

	$fields_replace = array(
		$redeemRequest,SITE_CURR,$creditAmount,$pagination
	);

	$pageContent = str_replace($fields, $fields_replace, $pageContent);

 	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>