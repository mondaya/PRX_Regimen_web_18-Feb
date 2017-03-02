<?php 
require_once("../../includes-nct/config-nct.php");
//mail('hiral.babariya@ncrypted.com', 'Payment test', $_POST['payment_status']);
/*if($_POST['payment_status'] == 'Completed')
{*/
	$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Payment done successfully'));
	redirectPage(SITE_URL.'myCustomOrder');
/*}else{
	$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Something went wrong in payment! Please try again.'));
	redirectPage(SITE_URL);
}*/
?>

