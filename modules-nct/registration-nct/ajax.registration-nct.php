<?php
	$content = NULL;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.registration-nct.php");
	$module = 'registration-nct';

	$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');
	$type = isset($_GET["type"]) ? trim($_GET["type"]) : (isset($_POST["type"]) ? trim($_POST["type"]) : '');
	$id = isset($_GET["id"]) ? trim($_GET["id"]) : (isset($_POST["id"]) ? trim($_POST["id"]) : 0);
	$mainObj = new Registration($db, $module, 0);
	$final_data = array();

	if(isset($_GET['email']) && !empty($_GET['email'])) {
		extract($_GET);
		$cnt = getTableValue('tbl_users', 'id', array('email'=>base64_encode($email)));
		if((int)$cnt <= 0){
			echo "true";
		} else {
			echo "false";
		}
		exit();
	}

	if(isset($_GET['code']) && !empty($_GET['code'])) {
		extract($_GET);
		if($code != $_SESSION['rand_code']) {
			$valid='false';
		} else {
			$valid='true';
		}
		echo $valid;
		exit;
	}

	if($action == 'getStates'){
		extract($_POST);
		echo json_encode($objHome->getStates($cId));
		exit;
	}

	if($action == 'getCities'){
		extract($_POST);
		echo json_encode($objHome->getCities($sId));
		exit;
	}
	echo json_encode($final_data);
	exit;
?>