<?php
	$reqAuth = true;
	$module = 'userprofile-nct';
	require_once("../../includes-nct/config-nct.php");
	require_once("class.userprofile-nct.php");
	$winTitle = $headTitle = SITE_NM;
	$userid = (int)$_REQUEST['id'];
	$action = $_REQUEST['action'];

	$objUser = new UserProfile();
	$styles = array(array("main.css", SITE_CSS), array("cropper.min.css", SITE_CSS), array("jquery.simple-dtpicker.css", SITE_CSS), array("jquery-ui.css", SITE_CSS));
	$scripts = array(array("cropper.min.js", SITE_JS), array("main.js", SITE_JS), array("jquery.simple-dtpicker.js", SITE_JS), array("jquery-ui.js", SITE_JS));

	$metaTag = getMetaTags(array("description" => $winTitle, "keywords" => $headTitle, "author" => AUTHOR));
	$table = "tbl_reminders";

	if (isset($_POST['updateProfile']) && $_SERVER['REQUEST_METHOD'] == "POST") {
		$returnData = $objUser->getUserData();
	}

	if (isset($_POST['sbtUpdate']) && $_SERVER['REQUEST_METHOD'] == "POST") {
		extract($_POST);
		$objPost->salute = isset($salute) ? $salute : 'mr';
		$objPost->firstName = isset($firstName) ? $firstName : '';
		$objPost->lastName = isset($lastName) ? $lastName : '';
		$objPost->secret = isset($secret) ? $secret : '';
		$objPost->gender = isset($gender) ? $gender : 'm';
		$objPost->birthDate = isset($birthDate) ? date('Y-m-d', strtotime($birthDate)) : date('Y-m-d');
		$objPost->address = isset($address) ? $address : '';
		$objPost->countryId = isset($country) ? $country : '';
		$objPost->stateId = isset($state) ? $state : '';
		$objPost->cityId = isset($city) ? $city : '';
		$objPost->zipCode = isset($zipCode) ? $zipCode : '';
		$objPost->code   = !empty($countryCode)?$countryCode:'';
		$objPost->mobileNumber = isset($mobile) ? $mobile : 0;
		$objPost->paypalEmail = isset($paypalEmail) ? base64_encode($paypalEmail) : '';
		//$objPost->buyStatus = 'n';

		if(!empty($objPost->firstName)) {
			
			//Get old value
			$users = $db->select('tbl_users','*',array('id'=>$sessUserId))->result();
			//echo '<pre>';
			//print_r($users);exit;
				$db->update('tbl_users', (array)$objPost, array('id'=>$sessUserId));
				
				//Send email notification to user and admin
				$email = getTableValue('tbl_users','email',array('id'=>$sessUserId));
				$arrayCont = array(
					'USER_NM' => $objPost->firstName
				);
				sendMail(ADMIN_EMAIL, 'profile_admin', $arrayCont);
				sendMail(base64_decode($email), 'profile_user', $arrayCont);

				$msgType = $_SESSION["msgType"] = disMessage(array('type' => 'suc', 'var' => 'Your profile updated successfully. Please wait for admin purchase approval.'));
				redirectPage(get_link('profile', $sessUserId));

		} else {
			$msgType = $_SESSION["msgType"] = disMessage(array('type' => 'err', 'var' => 'fillAllvalues'));
		}
	}

	if (isset($_POST['action']) && !empty($_POST['action']) && ($_POST['action']=='setReminder' || $_POST['action']=='editReminder')) {
		extract($_POST);
		$objpost = new stdClass();
		$objpost->reminder_title = !empty($reminder_title)?$reminder_title:'';
		$objpost->reminder_date = $reminder_date;
		$objpost->ipaddress = get_ip_address();
		if(!empty($objpost->reminder_title) && !empty($objpost->reminder_date)) {
			if($action=='setReminder') {
				$objpost->userId = $sessUserId;
				$db->insert($table, (array)$objpost);
				$msgType = $_SESSION["msgType"] = disMessage(array('type' => 'suc', 'var' => 'Reminder added successfully'));
			} else if($action=='editReminder' && !empty($id) && is_numeric($id)) {
				$db->update($table, (array)$objpost, array('id'=>$id));
				$msgType = $_SESSION["msgType"] = disMessage(array('type' => 'suc', 'var' => 'Reminder updated successfully'));
			}
		} else {
			$msgType = $_SESSION["msgType"] = disMessage(array('type' => 'err', 'var' => 'fillAllvalues'));
		}
		redirectPage(get_link('profile', $sessUserId));
		exit();
	}

	if(empty($userid) || !is_numeric($userid)) {
		redirectPage(SITE_URL);
		exit;
	}

	if ($action == 'editprofile') {
		$pageContent = $objUser->getUserData($userid);
		$winTitle = 'Edit Profile - ' . SITE_NM;
		$headTitle = 'Edit Profile';
	} else {
		$pageContent = $objUser->getPageContent($userid);
		$winTitle = 'User Profile - ' . SITE_NM;
		$headTitle = 'User Profile';
	}
	require_once(DIR_TMPL . "parsing-nct.tpl.php");
?>