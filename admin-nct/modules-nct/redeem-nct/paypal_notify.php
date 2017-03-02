<?php  
require_once("../../../includes-nct/config-nct.php");
list($amount,$sessUserId,$redeemId)=split('__',$_POST["custom"]);
//error_reporting(E_ALL);

if($_POST['payment_status'] == 'Completed')
{	

	$fetchRes = $db->select('tbl_users',array('redeemAmount','firstName','email'),array('id'=>$sessUserId))->result();

	//Update redeem status
	$db->update("tbl_redeem_request",array("status"=>"f"),array("id"=>$redeemId));

	//Update user credit amount
	$oldRedeemAmount = $fetchRes['redeemAmount'];
	$newRedeemAmount = strval($oldRedeemAmount - $amount);
	$db->update("tbl_users",array("redeemAmount"=>$newRedeemAmount),array("id"=>$sessUserId));

	//for dashboard notifications
	$db->insert("tbl_user_notifications",array("notificationType"=>2,"fromId"=>'',"toId"=>$sessUserId,"amount"=>strval($amount),"createdDate"=>date("Y-m-d H:i:s")));

	//For email to user
	$contArray = array(
		"USER_NM"=>$fetchRes['firstName'],
		"AMOUNT"=>SITE_CURR.$amount
	);
	sendMail(base64_decode($fetchRes['email']),"redeem_funded",$contArray);
}
?>