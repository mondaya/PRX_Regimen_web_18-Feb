<?php

require_once("../../includes-nct/config-nct.php");	
	
if(isset($_SESSION["adminUserId"]) && $_SESSION["adminUserId"] != "") {

	unset($_SESSION["adminUserId"]);
	unset($_SESSION["last_login"]);	
	unset($_SESSION["uName"]);	
	$_SESSION["adminUserId"] = '';
	$msgType = array('from'=>'admin','type'=>'suc','var'=>'succLogout');
/*	$qry = "UPDATE tbl_admin SET `sess_id` = '' where uName = ?";*/

	//$db->pdoQuery($qry,array('admin'));	
	//$db->update("tbl_admin",array("sess_id "=>0),array("id"=>$_SESSION["adminUserId"]));
}

redirectPage(SITE_ADM_MOD.'login-nct/');
?>
