<?php
$module = 'logout-nct';
require_once("../../includes-nct/config-nct.php");
if(isset($_SESSION["sessUserId"]) && $_SESSION["sessUserId"] != "") {
	//session_destroy();
	 session_unset();
	 session_start();
	$msgType=$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'succLogout'));
	redirectPage(SITE_URL);
}

?>
