<?php
	$content = NULL;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.home-nct.php");
	$module = 'home-nct';

	$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');
	$type = isset($_GET["type"]) ? trim($_GET["type"]) : (isset($_POST["type"]) ? trim($_POST["type"]) : '');
	$id = isset($_GET["id"]) ? trim($_GET["id"]) : (isset($_POST["id"]) ? trim($_POST["id"]) : 0);
	$mainObj = new Home();

	if(!empty($action) && $action=='change_currency' && !empty($id)) {
		$ref_url = ((isset($_GET['url']) && !empty($_GET['url']))?$_GET['url']:SITE_URL);
		$code = getTableValue('tbl_currency','code',array("id"=>$id));
		$sign = getTableValue('tbl_currency','sign',array("id"=>$id));
		$_SESSION['currencyId'] = $id;
		$_SESSION['currencyCode'] = $code;
		$_SESSION['currencySign'] = $sign;
		redirectPage($ref_url);
		exit();
	}
	else if($action == 'updateAddress'){
		extract($_POST);

		$data = array();
		if($salute != '' && $firstName != '' && $lastName != '' && $mobile != '' && $address != '' && $country != '' && $state != '' && $city != '' && $zipCode != '' ){
			$data['salute'] = $salute;
			$data['firstName'] = $firstName;
			$data['lastName'] = $lastName;
			$data['mobileNumber'] = $mobile;
			$data['address'] = $address;
			$data['countryId'] = $country;
			$data['stateId'] = $state;
			$data['cityId'] = $city;
			$data['zipCode'] = $zipCode;
			$db->update('tbl_users',$data,array('id'=>$sessUserId));
		}
	}

?>