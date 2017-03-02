<?php 
require_once("../../../includes-nct/config-nct.php");
print_r($_REQUEST);
	$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Redeem amount credited in user wallet successfully.'));
	redirectPage(SITE_ADM_MOD.'redeem-nct');

?>

