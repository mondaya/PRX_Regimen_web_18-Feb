<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.settings-nct.php");
$module = 'settings-nct';
$objPost = new stdClass();
$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');
$type = isset($_GET["type"]) ? trim($_GET["type"]) : (isset($_POST["type"]) ? trim($_POST["type"]) : '');
$id = isset($_GET["id"]) ? trim($_GET["id"]) : (isset($_POST["id"]) ? trim($_POST["id"]) : 0);
$mainObj = new settings($db, $module, 0);
$final_data = array();

if($action == 'checkpass'){
	extract($_POST);
	$curruntpass = $_POST['curruntpass'];
	$cnt = getTableValue('tbl_users','id',array('password'=>md5($curruntpass)));
	if((int)$cnt == 0){
		echo "false";
	}
	else{
		echo "true";
	}
	exit;
}
elseif($action == 'username')
{
	$username = $_POST['username'];
	$cnt = getTableValue('tbl_users','id',array('username'=>$username));
	if((int)$cnt == 0){
		echo "false";
	}
	else{
		echo "true";
	}
	exit();
}
elseif($action == 'password')
{
	extract($_POST);
	$password = $_POST['password'];
	$cnt = getTableValue('tbl_users','id',array('password'=>md5($password)));
	if((int)$cnt == 0){
		echo "false";
	}
	else{
		echo "true";
	}
	exit;
}
elseif($action == 'getStates'){
	extract($_POST);
	echo json_encode($objHome->getStates($cId));
	exit;
}
elseif($action == 'getCities'){
	extract($_POST);
	echo json_encode($objHome->getCities($sId));
	exit;
}
elseif($action == 'setNewsLetter'){
	extract($_POST);
	/*$getEmail = $db->select('tbl_users',array('email'),array('id'=>$sessUserId))->result();
	$getStatus = $db->select('tbl_newsletter_subscriber',array('is_active'),array('email'=>$getEmail['email']))->result();
	if($getStatus['is_active'] == 'n')
	{
		$status = 'y';
	}
	else
	{
		$status = 'n';
	}
	$db->update('tbl_newsletter_subscriber',array('is_active'=>$status),array('email'=>$getEmail['email']));
    echo json_encode(array('status'=>$status));
	exit;*/
	$getEmail = $db->select('tbl_users',array('email'),array('id'=>$sessUserId))->result();
	$getStatus = $db->select('tbl_newsletter_subscriber',array('id'),array('email'=>$getEmail['email']))->result();

	if($getStatus['id'] > 0){
		$db->delete('tbl_newsletter_subscriber',array('email'=>$getEmail['email']));
		$status = 'n';
	}else{
		$db->insert('tbl_newsletter_subscriber',array('is_active'=>'y','email'=>$getEmail['email'],'created_date'=>date('Y-m-d H:i:s')));
		$status = 'y';
    }

    echo json_encode(array('status'=>$status));exit;
}
echo json_encode($final_data);
exit;

?>