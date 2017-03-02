<?php
	$content = NULL;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.userprofile-nct.php");
	$module = 'userprofile-nct';
	$table = 'tbl_reminders';

	$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');
	$type = isset($_GET["type"]) ? trim($_GET["type"]) : (isset($_POST["type"]) ? trim($_POST["type"]) : '');
	$id = isset($_GET["id"]) ? trim($_GET["id"]) : (isset($_POST["id"]) ? trim($_POST["id"]) : 0);
	$mainObj = new UserProfile();
	$final_data = array();

	if(isset($_POST['action']) && $_POST['action']=='delReminder' && !empty($_POST['id'])) {
		$id = getTableValue($table, 'id', array('id'=>$_POST['id'], 'userId'=>$sessUserId));
		if(!empty($id)) {
			$db->delete($table, array('id'=>$_POST['id']));
				$msgType = $_SESSION["msgType"] = disMessage(array('type' => 'suc', 'var' => 'Reminder deleted successfully'));
				echo json_encode(array('result'=>'1'));
				exit();
		} else {
			echo json_encode(array('result'=>'0'));
			exit();
		}
	}

	if(isset($_GET['action']) && $_GET['action']=='getReminderModel') {
		$rem_id = ((isset($_GET['rem_id'])) && !empty($_GET['rem_id'])?$_GET['rem_id']:0);
		echo $mainObj->getReminderModel($rem_id);
		exit();
	}
	if (isset($action) && $action == 'remdel') {
	    extract($_POST);
	    $db->delete('tbl_reminders', array('id' => $id));
	    $setReminder = $mainObj->getReminder();
	    echo json_encode(array('content' => $setReminder));
	    exit;
	} elseif ($action == 'checkUserName') {
	    extract($_POST);
	    $cnt = getTableValue('tbl_users', 'id', array('username' => $username));
	    if ((int) $cnt <= 0) {
	        echo "true";
	    } else {
	        echo "false";
	    }
	    exit;
	} elseif ($action == 'getStates') {
	    extract($_POST);
	    echo json_encode($objHome->getStates($id));
	    exit;
	} elseif ($action == 'getCities') {
	    extract($_POST);
	    echo json_encode($objHome->getCities($_POST['id']));
	    exit;
	} elseif ($action == 'set_reminder') {
	    extract($_POST);
	    $insArr = array('userId' => $sessUserId,
	        'reminderTitle' => $comment,
	        'reminderTime' => $reminder,
	        'createdDate' => date('Y-m-d H:i:s'));
	    $db->insert('tbl_reminders', $insArr);
	    $setReminder = $mainObj->getReminder();

	    echo json_encode(array('content' => $setReminder));
	    exit;
	}
	echo json_encode($final_data);
	exit;
?>