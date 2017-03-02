<?php  
require_once("../../includes-nct/config-nct.php");
list($amountOrignal,$sessUserId)=split('__',$_POST["custom"]);
//echo '<pre>';
//print_r($_POST);exit;
//mail('hiral.babariya@ncrypted.com', 'Payment test', json_encode($sessUserId));
if($_POST['payment_status'] == 'Completed')
{	
			
			//Update custom order table
			$fetchRes = $db->pdoQuery("select id,productId,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity,discountAmount from tbl_orders where userId = ".$sessUserId." and paymentStatus = 'n' ")->results();
			$productPricem = $shippingAmount = $dutiesAmount = $adminCharge = $discountPrice = $paidAmount ='';
			foreach ($fetchRes as $key => $orders) {

				//Delete cart 
				$db->delete("tbl_cart",array("userId"=>$sessUserId));

				//Update product quantity
				$product = array();
				$oldQuantity = getTableValue("tbl_product_deals","quantity",array("id"=>$orders['productId']));
				$product['quantity'] = $oldQuantity - $orders['quantity'];
				$db->update('tbl_product_deals',$product,array("id"=>$orders['productId']));

				$productPrice = $orders['productPrice'] * $orders['quantity'];
				$finalAmount = ($productPrice + $orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']) - $orders['discountAmount'];

				$order = array();
				$order['paidAmount'] = strval($finalAmount);
				$order['paymentStatus'] = 'y';
				$order['transactionId'] = $_POST["txn_id"];
				$order['createdDate'] = date('Y-m-d H:i:s');
				$db->update('tbl_orders',$order,array("id"=>$orders['id']));

				//For mail data
				$productName .= getTableValue("tbl_product_deals","productName",array("id"=>$orders['productId'])).',';
				$quantity .= $orders['quantity'].',';
				$productPricem += $orders['productPrice'];
				$shippingAmount += $orders['shippingAmount'];
				$dutiesAmount += $orders['dutiesAmount'];
				$adminCharge += $orders['adminCharge'];
				$discountAmount += $orders['discountAmount'];
				$paidAmount += $finalAmount;

			}

			//Send mail to admin
			$firstName = getTableValue('tbl_users','firstName',array("id"=>$sessUserId));
			$contArray = array(
				"USER_NM"=>$firstName,
				"PRODUCT_NM"=>$productName,
				"QUANTITY"=>$quantity,
				"PRICE"=>SITE_CURR.' '.$productPricem,
				"DUTIES"=>SITE_CURR.' '.$dutiesAmount,
				"ADMIN_CHARGE"=>SITE_CURR.' '.$adminCharge,
				"SHIPPING"=>SITE_CURR.' '.$shippingAmount,
				"DISCOUNT"=>SITE_CURR.' '.$discountAmount,
				"PAID_AMOUNT"=>SITE_CURR.' '.$paidAmount
			);
			sendMail(ADMIN_EMAIL,"order_admin",$contArray);

			//Send mail to buyer
			$email = getTableValue('tbl_users','email',array("id"=>$sessUserId));
			sendMail(base64_decode($email),"order_buyer",$contArray);

			//For referral module
			$fees = $db->pdoQuery("select referral from tbl_admin_amount where id = 1")->result();
			$referrals = $db->pdoQuery("Select r.id,r.userId,u.firstName,u.email,u.referralAmount FROM tbl_referral_users as r
				LEFT JOIN tbl_users as u ON ( r.userId = u.id )
				WHERE r.email = '".base64_decode($email)."' and isPurchase = 'n' and paidStatus = 'n'")->result();

			if($referrals['id'] > 0){

				//update referral amount
				$referralAmount = $productPricem * $fees['referral'] / 100;
				$oldReferralAmount = $referrals['referralAmount'];
				$finalReferralAmount = $oldReferralAmount + $referralAmount;
				
				$oldCreditAmount =getTableValue('tbl_users','creditAmount',array('id'=>$sessUserId));
				$finalCreditAmount = $oldCreditAmount + $referralAmount;

				$db->update('tbl_users',array('referralAmount'=>strval($finalReferralAmount),'creditAmount'=>strval($finalCreditAmount)),array('id'=>$referrals['userId']));

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
			$txn['paid_amount'] = strval($paidAmount);
			$db->insert('tbl_payment_history',$txn);


}
?>