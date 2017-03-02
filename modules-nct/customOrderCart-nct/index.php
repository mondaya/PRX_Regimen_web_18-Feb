<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.customOrderCart-nct.php");

	$id = isset($_GET['id']) && $_GET['id']!=''?$_GET['id']:'';

	//Check for product exist
	$exist = getTotalRows('tbl_custom_orders',array("id"=>$id),'id');

	if($exist == 0){
		$_SESSION['msgType'] = disMessage(array('type'=>'err','var'=>"Invalid request"));
		redirectPage(SITE_URL);
	}

	if(!isset($_POST['payWallet']) && !isset($_POST['payPaypal'])){
		//Remove discount on page refresh
		$discount = array();
		$discount['is_coupon_used'] = 'n';
		$discount['couponId'] = '0';
		$discount['discountAmount'] = '0';
		$db->update('tbl_custom_orders',$discount,array('userId'=>$sessUserId,'paymentStatus'=>'n')); 
	}

	$table = "tbl_product_deals";
	$module = 'customOrderCart-nct';

	$winTitle = 'Custom Order Cart '.SITE_NM;
	$headTitle = 'Custom Order Cart';
	$metaTag = getMetaTags(array("description"=>$winTitle,"keywords"=>$headTitle,"author"=>AUTHOR));


	if(isset($_POST['payWallet']) && $_POST['payWallet'] == 'Pay'){
		checkBuyStatus($sessUserId);
		checkAddress($sessUserId);
		//print_r($_POST);
		extract($_POST);

		//Get payment amount
		$price = $db->pdoQuery("select productPrice,shippingAmount,dutiesAmount,adminCharge,discountAmount,quantity from tbl_custom_orders where id = ".$id."")->result();
		
		$amount = ($price['productPrice']* $price['quantity']) + $price['shippingAmount'] + $price['dutiesAmount'] + $price['adminCharge'] - $price['discountAmount'];
		
		$creditAmount = getTableValue('tbl_users','creditAmount',array("id"=>$sessUserId));
		$email = getTableValue('tbl_users','email',array("id"=>$sessUserId));
		
		if($creditAmount < $amount){
			$msgType = disMessage(array('type'=>'err','var'=>'You have not sufficient amount in your wallet. Please deposit amount or use other payment option.'));
		}else{
			//Deduct user wallet amount
			$user = array();
			$remainAmount = $creditAmount - $amount;
			$user['creditAmount'] = strval($remainAmount);
			$db->update('tbl_users',$user,array("id"=>$sessUserId));

			//Update custom order table
			$order = array();
			$order['paidAmount'] = strval($amount);
			$order['paymentStatus'] = 'y';
			$order['transactionId'] = 'TXN'.strtoupper(uniqid());
			$order['createdDate'] = date('Y-m-d H:i:s');
			$order['deliveryStatus']="p";
			$db->update('tbl_custom_orders',$order,array("id"=>$id));

			//Send mail to admin
			$orders = $db->pdoQuery("select productName,productPrice,shippingAmount,dutiesAmount,adminCharge,discountAmount,paidAmount,quantity,size,color from tbl_custom_orders where id = ".$id."")->result();

			$contArray = array(
				"USER_NM"=>$sessUserName,
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
			sendMail(base64_decode($email),"custom_order_buyer",$contArray);


			//For referral module
			$fees = $db->pdoQuery("select referral from tbl_admin_amount where id = 1")->result();
			$referrals = $db->pdoQuery("Select r.id,r.userId,u.firstName,u.email,u.referralAmount FROM tbl_referral_users as r
				LEFT JOIN tbl_users as u ON ( r.userId = u.id )
				WHERE r.email = '".base64_decode($email)."' and isPurchase = 'n' and paidStatus = 'n'")->result();

			if($referrals['id'] > 0){

				//update referral amount
				$referralAmount = strval($orders['productPrice'] * $fees['referral'] / 100);
				$oldReferralAmount = strval($referrals['referralAmount']);
				$finalReferralAmount = strval($oldReferralAmount + $referralAmount);
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
			$txn['transaction_id'] = 'TXN'.strtoupper(uniqid());
			$txn['transaction_type'] = 'p';
			$txn['payment_gateway'] = 'w';
			$txn['paid_amount'] = strval($orders['paidAmount']);
			$db->insert('tbl_payment_history',$txn);


			$_SESSION['msgType'] = disMessage(array('type'=>'suc','var'=>'You are purchase product sucessfully.'));
			redirectPage(SITE_URL.'myCustomOrder');

		}
	}

	if(isset($_POST['payPaypal']) && $_POST['payPaypal'] == 'Pay'){
		checkBuyStatus($sessUserId);
		checkAddress($sessUserId);
		extract($_POST);
	
		$orders = $db->pdoQuery("select productName,productPrice,shippingAmount,dutiesAmount,adminCharge,discountAmount,paidAmount,quantity,size,color from tbl_custom_orders where id = ".$id."")->result();
		
		$price = ($orders['productPrice']* $orders['quantity']) + $orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge'] - $orders['discountAmount'];
		$priceUsd = number_format(convertCurrency(USD_CURR_ID,$price),2);
		
		?>

		<form action="<?php echo PAYPAL_URL;?>" method="post" name="formcart" id="formcart">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="<?php echo ADMIN_PAYPAL; ?>">
                <input type="hidden" name="item_name" value="<?php echo $orders['productName']; ?>"> 
                <input type="hidden" name="currency_code" value="USD">
                <input type="hidden" name="amount" value="<?php echo $priceUsd; ?>">
                <input type="hidden" name="custom" value="<?php echo $id.'__'.$price.'__'.$sessUserId; ?>"/>
                <input type="hidden" name="bn" value="NCryptedTechnologies_SP_EC">
                <input type="hidden" name="return" value="<?php echo SITE_MOD.'customOrderCart-nct/paypal_thankyou.php'; ?>">
                <input type="hidden" name="cancel_return" value="<?php echo SITE_URL; ?>">
                <input type="hidden" name="notify_url" value="<?php echo SITE_MOD.'customOrderCart-nct/paypal_notify.php'; ?>">
        </form>
        <script type="text/javascript">
			document.formcart.submit();
		</script>
	<?php }

	$mainObj = new customOrderCart($module,$id);

	$pageContent = $mainObj->getPageContent();
		
 	$fields = array(
 	);

	$fields_replace = array(
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");
	
	
?>