<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.profile-nct.php");

$module = 'profile-nct';

$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');
$type = isset($_GET["type"]) ? trim($_GET["type"]) : (isset($_POST["type"]) ? trim($_POST["type"]) : '');
$id = isset($_GET["id"]) ? trim($_GET["id"]) : (isset($_POST["id"]) ? trim($_POST["id"]) : 0);
$selected = isset($_GET["selected"]) ? trim($_GET["selected"]) : (isset($_POST["selected"]) ? trim($_POST["selected"]) : 0);
$mainObj = new Profile(0);
$final_data = array();

if(isset($action) && $action=='get_state')
{
	

	$final_data['content']=$mainObj->getStateOption($id,$selected);
	$final_data['status']=200;
}
else if(isset($action) && $action=='get_city')
{
	$final_data['content']=$mainObj->getCityOption($id,$selected);
	$final_data['status']=200;
}
$final_data['content']=preg_replace('/\{([A-Z_]+)\}/e', "$1",$final_data['content']);
echo json_encode($final_data);
exit;
?>