<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.orderCart-nct.php");

	$table = "tbl_product_deals";
	$module = 'orderCart-nct';

	$winTitle = 'Order Cart '.SITE_NM;
	$headTitle = 'Order Cart';
	$metaTag = getMetaTags(array("description"=>$winTitle,"keywords"=>$headTitle,"author"=>AUTHOR));
	//Check for product exist
	$exist = getTotalRows('tbl_cart',array("userId"=>$sessUserId),'id');

	if($exist == 0){
		$_SESSION['msgType'] = disMessage(array('type'=>'err','var'=>"Cart is empty"));
		redirectPage(SITE_URL);
	}

	if(!isset($_POST['payWallet']) && !isset($_POST['payPaypal'])){
		//Remove discount on page refresh
		$discount = array();
		$discount['is_coupon_used'] = 'n';
		$discount['couponId'] = '0';
		$discount['discountAmount'] = '0';
		$db->update('tbl_orders',$discount,array('userId'=>$sessUserId,'paymentStatus'=>'n')); 
	}

	$action = isset($_GET['action']) && $_GET['action']!=''?$_GET['action']:"";
	$quantity = isset($_GET['quantity']) && $_GET['quantity']!=''?$_GET['quantity']:"";
	$cartId = isset($_GET['cartId']) && $_GET['cartId']!=''?$_GET['cartId']:"";

	if($action == 'delete'){
		$cartId = $_GET['cartId'];
		$pId = $_GET['pId'];
		
		$db->delete('tbl_cart',array("id"=>$cartId));
		$db->delete('tbl_orders',array("productId"=>$pId,"userId"=>$sessUserId,"paymentStatus"=>"n"));
		$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>"Item deleted successfully"));
		redirectPage(SITE_URL.'orderCart');
	}
	else if($action == 'changeQuantity'){

		$productId = getTableValue("tbl_cart","productId",array("id"=>$cartId));
		$pQuantity = getTableValue("tbl_product_deals","quantity",array("id"=>$productId));

		if($quantity <= $pQuantity && $quantity != 0){
			$db->update("tbl_cart",array("quantity"=>$quantity),array("id"=>$cartId));
		}
		redirectPage(SITE_URL.'orderCart');
	}

	//Insert cart product in order table
	$cart = $db->pdoQuery("Select c.productId,c.quantity as cartQuantity,p.id,p.isDiscount,p.actualPrice,p.discountPrice from tbl_cart as c 
		LEFT JOIN tbl_product_deals as p ON(p.id = c.productId)
		where c.userId = ".$sessUserId."")->results();
	//echo '<pre>';
	//print_r($cart);exit;

	$data = array();
	//$fees = $db->pdoQuery("select * from tbl_admin_amount where id = 1")->result();
	$countryId='';
	foreach ($cart as $key => $value) {
		if($value['isDiscount'] == 'y'){
			$productPrice = $value['discountPrice'];
		}else{
			$productPrice = $value['actualPrice'];
		}	

		$alreadyExist = getTableValue("tbl_orders","id",array("userId"=>$sessUserId,"productId"=>$value['productId'],"paymentStatus"=>"n"));

		
		
		$data['userId'] = $sessUserId;
		$data['orderId'] = 'ORDER'.time().strtoupper(genrateRandom(4));
		$data['productId'] = $value['productId'];
		$data['productPrice'] = strval($productPrice);
		$data['quantity'] = $value['cartQuantity'];
		$totalProductPrice = strval($productPrice * $value['cartQuantity']);
		
        

		if($alreadyExist > 0){
			
			//For update shipping amount
			$deliveryOption = getTableValue('tbl_orders','deliveryOption',array('id'=>$alreadyExist));

			if($deliveryOption == 'd'){

				$dutiesAmount = getDutiesAmount($totalProductPrice,$countryId);
				$adminCharge = getAdminCharge($totalProductPrice,$countryId);
		        $data['dutiesAmount'] = strval($dutiesAmount);
		        $data['adminCharge'] = strval($adminCharge);
				$productPrice = $data['productPrice']*$data['quantity'];
				$shippingAmount = getd2dShippingAmount($productPrice);
				$data['shippingAmount'] = strval($shippingAmount);

			}else if($deliveryOption == 'p'){

				$pick_point = getTableValue('tbl_orders','pick_point',array('id'=>$alreadyExist));
				$stateId = getTableValue('tbl_pick_points','stateId',array('id'=>$pick_point));
				$countryId = getTableValue('tbl_pick_points','countryId',array('id'=>$pick_point));
				$dutiesAmount = getDutiesAmount($totalProductPrice,$countryId);
				$adminCharge = getAdminCharge($totalProductPrice,$countryId);
		        $data['dutiesAmount'] = strval($dutiesAmount);
		        $data['adminCharge'] = strval($adminCharge);
				$productPrice = $data['productPrice']*$data['quantity'];
				$shippingAmount = getPickShippingAmount($productPrice,$stateId);
				$data['shippingAmount'] = strval($shippingAmount);

			}

			$db->update("tbl_orders",$data,array("id"=>$alreadyExist));
		}else{
			$db->Insert("tbl_orders",$data);	
		}

		        
	}

	//exit;
	if(isset($_POST['payWallet']) && $_POST['payWallet'] == 'Pay'){
		checkBuyStatus($sessUserId);
		checkAddress($sessUserId);
		//print_r($_POST);
		extract($_POST);

		//Get payment amount
		$query = $db->pdoQuery("select sum(productPrice*quantity) as totalPrice,sum(shippingAmount) as totalShipping,sum(dutiesAmount) as totalDuties,sum(adminCharge) as totalAdminCharge,sum(discountAmount) totalDiscount from tbl_orders where userId = ".$sessUserId." and paymentStatus = 'n'")->results();
		foreach ($query as $key => $price) {
			$amount = $price['totalPrice'] + $price['totalShipping'] + $price['totalDuties'] + $price['totalAdminCharge'] - $price['totalDiscount'];
		}
		

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
			$fetchRes = $db->pdoQuery("select id,productId,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity,discountAmount from tbl_orders where userId = ".$sessUserId." and paymentStatus = 'n' ")->results();
			$productPricem = $shippingAmount = $dutiesAmount = $adminCharge = $discountPrice = $paidAmount ='';

			$transactionId = 'TXN'.strtoupper(uniqid());
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
				$order['transactionId'] = $transactionId;
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
			
			$contArray = array(
				"USER_NM"=>$sessUserName,
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
			$txn['transaction_id'] = $transactionId;
			$txn['transaction_type'] = 'p';
			$txn['payment_gateway'] = 'w';
			$txn['paid_amount'] = strval($paidAmount);
			$db->insert('tbl_payment_history',$txn);

			$_SESSION['msgType'] = disMessage(array('type'=>'suc','var'=>'You are purchase product sucessfully.'));
			redirectPage(SITE_URL.'myOrder');

		}
	}

	if(isset($_POST['payPaypal']) && $_POST['payPaypal'] == 'Pay'){
		checkBuyStatus($sessUserId);
		checkAddress($sessUserId);
		extract($_POST);
		//$amount = (float) str_replace(',', '', $amount);
		//Get payment amount
		$query = $db->pdoQuery("select sum(productPrice*quantity) as totalPrice,sum(shippingAmount) as totalShipping,sum(dutiesAmount) as totalDuties,sum(adminCharge) as totalAdminCharge,sum(discountAmount) totalDiscount from tbl_orders where userId = ".$sessUserId." and paymentStatus = 'n'")->results();
		foreach ($query as $key => $price) {
			$price = $price['totalPrice'] + $price['totalShipping'] + $price['totalDuties'] + $price['totalAdminCharge'] - $price['totalDiscount'];
			$priceUsd = number_format(convertCurrency(USD_CURR_ID,$price),2);
		}

		$fetchRes = $db->pdoQuery("select id,productId,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity,discountAmount from tbl_orders where userId = ".$sessUserId." and paymentStatus = 'n' ")->results();

			$productName = '';
			foreach ($fetchRes as $key => $orders) {
				$productName .= getTableValue("tbl_product_deals","productName",array("id"=>$orders['productId'])).',';
			}
		?>

		<form action="<?php echo PAYPAL_URL;?>" method="post" name="formcart" id="formcart">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="<?php echo ADMIN_PAYPAL; ?>">
                <input type="hidden" name="item_name" value="<?php echo $productName; ?>"> 
                <input type="hidden" name="currency_code" value="USD">
                <input type="hidden" name="amount" value="<?php echo $priceUsd; ?>">
                <input type="hidden" name="custom" value="<?php echo $price.'__'.$sessUserId; ?>"/>
                <input type="hidden" name="bn" value="NCryptedTechnologies_SP_EC">
                <input type="hidden" name="return" value="<?php echo SITE_MOD.'orderCart-nct/paypal_thankyou.php'; ?>">
                <input type="hidden" name="cancel_return" value="<?php echo SITE_URL; ?>">
                <input type="hidden" name="notify_url" value="<?php echo SITE_MOD.'orderCart-nct/paypal_notify.php'; ?>">
        </form>
        <script type="text/javascript">
			document.formcart.submit();
		</script>
		
	<?php }

	$mainObj = new orderCart($module);
	$productList = $mainObj->getProductList();
	$pageContent = $mainObj->getPageContent();
		
 	$fields = array(
 		"%PRODUCT_LIST%"
 	);

	$fields_replace = array(
		$productList
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");
	
	
?>