<?php  
require_once("../../includes-nct/config-nct.php");
list($amount,$sessUserId)=split('__',$_POST["custom"]);

if($_POST['payment_status'] == 'Completed')
{	
	$oldCredit = getTableValue("tbl_users","creditAmount",array("id"=>$sessUserId));
	$finalAmount = strval($oldCredit + $amount);
	$db->update("tbl_users",array("creditAmount"=>$finalAmount),array("id"=>$sessUserId));

	//For notification
	$users = $db->pdoQuery("Select id,firstName,email from tbl_users where id=".$sessUserId."")->result();			
	//For dashboard notifications
	$db->insert("tbl_user_notifications",array("notificationType"=>4,"fromId"=>'',"toId"=>$users['id'],"amount"=>strval($amount),"createdDate"=>date("Y-m-d H:i:s")));

	//For email to user
	$contArray = array(
		"USER_NM"=>$users['firstName'],
		"AMOUNT"=>SITE_CURR.$amount
	);
	sendMail(base64_decode($users['email']),"amount_added",$contArray);

	//For transaction history
	$txn = array();
	$txn['user_id'] = $sessUserId;
	$txn['transaction_id'] = $_POST["txn_id"];
	$txn['transaction_type'] = 'd';
	$txn['payment_gateway'] = 'p';
	$txn['paid_amount'] = strval($amount);
	$db->insert('tbl_payment_history',$txn);
}
?>