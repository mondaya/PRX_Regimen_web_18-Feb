<?php
$reqAuth = true;
require_once("../../includes-nct/config-nct.php");
require_once("class.settings-nct.php");

$module = 'settings-nct';
$winTitle = 'Settings '.SITE_NM;
$headTitle = 'Settings';
$metaTag = getMetaTags(array("description"=>$winTitle,"keywords"=>$headTitle,"author"=>AUTHOR));

$objPref = new settings();
$pageContent = NULL;
$table = "tbl_users";

$action = isset($_GET['action']) ? $_GET['action'] : '';
$userId = isset($_GET['id']) ? $_GET['id'] : '';

if(isset($_POST['CancelForm'])) {
	redirectPage(get_link('account_settings', $sessUserId));
}

if(isset($_POST['submitpassword']) && $_SERVER['REQUEST_METHOD'] == "POST") {
	extract($_POST);
	$passArr = array(
		'curruntpass'=>isset($curruntpass) ? $curruntpass : '',
		'newpass'=>isset($newpass) ? $newpass : '',
		'confirmpass'=>isset($confirmpass) ? $confirmpass :''
		);

	if(!empty($passArr['curruntpass']) && !empty($passArr['newpass'])) {
		$changeReturn = $objPref->submitProcedure($passArr);
	}
}

if(isset($_POST['sbtUpdate']) && $_SERVER['REQUEST_METHOD']=="POST") {
	extract($_POST);
	$address = isset($_POST['address']) ? $_POST['address'] : '';
	$country = isset($_POST['country']) ? $_POST['country'] : '';
	$state = isset($_POST['state']) ? $_POST['state'] : '';
	$city = isset($_POST['city']) ? $_POST['city'] : '';
	$zipCode = isset($_POST['zipCode']) ? $_POST['zipCode'] : '';
	if(!empty($address) &&  !empty($country) && !empty($state) && !empty($city) && !empty($zipCode)) {
		$insArr = array(
			'address'=>isset($address) ? $address : '',
			'zipCode'=>isset($zipCode) ? $zipCode : '',
			'countryId'=>isset($country) ? $country : '',
			'stateId'=>isset($state) ? $state : '',
			'cityId'=>isset($city) ? $city : ''
			);

		if((int)getTableValue('tbl_users','id',array('id'=>$sessUserId)) >= 0) {
			$insArr['createdDate'] = date('Y-m-d H:i:s');
			$db->update('tbl_users',$insArr,array('id'=>$sessUserId));
			global $sessUserName;
			$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Your Address Changes have been saved.'));
		}
	} else{
		$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Fill the values first'));
	}
}

if($_GET['action']=='changepass') {
	$pageContent .= $objPref->changepass();
} else {
	$pageContent = $objPref->getPageContent($sessUserId);
}

if(isset($_POST['btnNotification']) && $_SERVER['REQUEST_METHOD']=="POST") {
	//print_r($_POST);
	extract($_POST);
	$data = array();
	$data['newProductPosted'] = $newProductPosted!=''?$newProductPosted:'n';
	$data['amountAddedInWallet'] = $amountAddedInWallet!=''?$amountAddedInWallet:'n';
	//$data['receiveReplayFromAdmin'] = $receiveReplayFromAdmin!=''?$receiveReplayFromAdmin:'n';
	$data['newPrormoPosted'] = $newPrormoPosted!=''?$newPrormoPosted:'n';
	$data['orderStatusByAdmin'] = $orderStatusByAdmin!=''?$orderStatusByAdmin:'n';
	$data['reminder'] = $reminder!=''?$reminder:'n';
		

	$exist = getTableValue('tbl_notifications','id',array('userId'=>$sessUserId));

	if($exist > 0){
		$db->update('tbl_notifications',$data,array("userId"=>$sessUserId));
	}else{
		$data['userId'] = $sessUserId;
		$db->insert('tbl_notifications',$data);
	}		

	$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Notification Setting updated successfully.'));
	redirectPage(SITE_URL.'settings');
}


require_once(DIR_TMPL."parsing-nct.tpl.php");
?>