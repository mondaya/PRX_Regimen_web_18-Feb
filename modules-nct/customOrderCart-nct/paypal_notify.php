<?php  
require_once("../../includes-nct/config-nct.php");
list($id,$amountOrignal,$sessUserId)=split('__',$_POST["custom"]);
//echo '<pre>';
//print_r($_POST);exit;
//mail('hiral.babariya@ncrypted.com', 'Payment test', $_POST['payment_status']);
if($_POST['payment_status'] == 'Completed')
{	
			//Update custom order table
			$paidAmount = convertCurrency('7',$_POST['mc_gross']);
			$order = array();
			$order['paidAmount'] = strval($amountOrignal);
			$order['paymentStatus'] = 'y';
			$order['transactionId'] = $_POST["txn_id"];
			$order['createdDate'] = date('Y-m-d H:i:s');
			$order['deliveryStatus']="p";
			$db->update('tbl_custom_orders',$order,array("id"=>$id));
			
			//Send mail to admin
			$orders = $db->pdoQuery("select productName,productPrice,shippingAmount,dutiesAmount,adminCharge,discountAmount,paidAmount,quantity,size,color from tbl_custom_orders where id = ".$id."")->result();

			$firstName = getTableValue('tbl_users','firstName',array("id"=>$sessUserId));
			$contArray = array(
				"USER_NM"=>$firstName,
				"PRODUCT_NM"=>$orders['productName'],
				"QUANTITY"=>$orders['quantity'],
				"PRICE"=>SITE_CURR.' '.$orders['productPrice'],
				"SIZE"=>$orders['size'],
				"COLOR"=>$orders['color'],
				"DUTIES"=>SITE_CURR.' '.$orders['dutiesAmount'],
				"ADMIN_CHARGE"=>SITE_CURR.' '.$orders['adminCharge'],
				"SHIPPING"=>SITE_CURR.' '.$orders['shippingAmount'],
				"DISCOUNT"=>SITE_CURR.' '.$orders['discountAmount'],
				"PAID_AMOUNT"=>SITE_CURR.' '.$orders['paidAmount']
			);
			sendMail(ADMIN_EMAIL,"custom_order_admin",$contArray);

			//Send mail to buyer
			$email = getTableValue('tbl_users','email',array("id"=>$sessUserId));
			sendMail(base64_decode($email),"custom_order_buyer",$contArray);


			//For referral module
			$fees = $db->pdoQuery("select referral from tbl_admin_amount where id = 1")->result();
			$referrals = $db->pdoQuery("Select r.id,r.userId,u.firstName,u.email,u.referralAmount FROM tbl_referral_users as r
				LEFT JOIN tbl_users as u ON ( r.userId = u.id )
				WHERE r.email = '".base64_decode($email)."' and isPurchase = 'n' and paidStatus = 'n'")->result();

			if($referrals['id'] > 0){

				//update referral amount
				$referralAmount = $orders['productPrice'] * $fees['referral'] / 100;
				$oldReferralAmount = $referrals['referralAmount'];
				$finalReferralAmount = $oldReferralAmount + $referralAmount;
				$db->update('tbl_users',array('referralAmount'=>strval($finalReferralAmount),'creditAmount'=>strval($finalReferralAmount)),array('id'=>$referrals['userId']));

				//update referral status
				$db->update('tbl_referral_users',array('isPurchase'=>'y','paidStatus'=>'y'),array('id'=>$referrals['id']));

				//send email notification to
				$contArray = array(
					"USER_NM"=>$referrals['firstName'],
					"BONUS"=>SITE_CURR.number_format($referralAmount,2),
					"REFERRAL_USER_NM"=>$sessUserName
				);
				sendMail(base64_decode($referrals['email']),"referral_bonus",$contArray);

			}

			//For transaction history
			$txn = array();
			$txn['user_id'] = $sessUserId;
			$txn['transaction_id'] = $_POST["txn_id"];
			$txn['transaction_type'] = 'p';
			$txn['payment_gateway'] = 'p';
			$txn['paid_amount'] = strval($orders['paidAmount']);
			$db->insert('tbl_payment_history',$txn);

			


}
?>