<?php 
require_once("../../includes-nct/config-nct.php");

/*if($_POST['payment_status'] == 'Completed')
{*/
	$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Payment done successfully'));

	redirectPage(SITE_URL.'wallet');
/*}else{
	$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Something went wrong in payment! Please try again.'));
	redirectPage(SITE_URL);
}*/

?>

