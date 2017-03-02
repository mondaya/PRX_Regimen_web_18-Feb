<?php
require_once("../config-nct.php");

$today = date('Y-m-d');
$qry ="Select r.id as reminderId,r.reminder_title,DATE(r.reminder_date) as reminderDate,u.id,u.firstName,u.deviceToken,u.deviceType,u.email from tbl_reminders as r LEFT JOIN tbl_users as u ON(r.userId = u.id) WHERE DATE(r.reminder_date) = '".$today."'";
$fetchRes = $db->pdoQuery($qry)->results();

//echo '<pre>';
//print_r($fetchRes);

foreach ($fetchRes as $key => $value) {
	
	//Send email notification
	$reminder = getTableValue("tbl_notifications","reminder",array("userId"=>$value['id']));

	if($reminder == 'y'){
		$contArray = array(
			"USER_NM"=>$value['firstName'],
			"REMINDER"=>$value['reminder_title'],
			"REMINDER_DATE"=>date('m-d-Y',strtotime($value['reminderDate']))
		);

		sendMail(base64_decode($value['email']),"reminder",$contArray);
		
		//For dashboard notifications
		$rowscheck=$db->pdoQuery("SELECT * FROM tbl_user_notifications WHERE notificationType=7 and refId=".$value['reminderId'])->results();

		if(count($rowscheck)==0)
		{
			
			$db->insert("tbl_user_notifications",array("notificationType"=>7,"fromId"=>'',"toId"=>$value['id'],"refId"=>$value['reminderId'],"createdDate"=>date("Y-m-d H:i:s")));
		}else
		{
			$db->update('tbl_user_notifications',array("createdDate"=>date("Y-m-d H:i:s")),array("notificationType"=>7,"refId"=>$value['reminderId']));
		}
		

	}
	

}

	//start to push notification
	
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$currencyCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
		
	   $query = "SELECT * from tbl_user_notifications WHERE  DATE_FORMAT(createdDate,'%Y-%m-%d')='$today'";
		
		$result = $db->pdoQuery($query);
	 	$fetchRes = $result->results();
	 	$totalRow = $result->affectedRows();
		 
		 if($totalRow>0)
		 {
		 	foreach ($fetchRes as $value) 
			 	{
			 		//notification data

					$notificationType = $value['notificationType'];
					$toId = isset($value['toId'])?$value['toId']:0;
					$refId = isset($value['refId'])?$value['refId']:0;
					$deviceType = getTableValue('tbl_users',"deviceType",array("id"=>$toId));
					$deviceToken = getTableValue('tbl_users',"deviceToken",array("id"=>$toId));
					$amount = isset($value['amount'])?convertCurrency($currencyId,$value['amount']):0;
					$amount=number_format($amount,2);
					$createdDate = isset($value['createdDate'])?$value['createdDate']:'1900-12-31 00:00:00';

					$productName = getTableValue('tbl_product_deals',"productName",array("id"=>$refId));
					$productName = isset($productName)?$productName:'';
					$coupon_code = getTableValue('tbl_coupons',"coupon_code",array("id"=>$refId));
					$coupon_code =isset($coupon_code)?$coupon_code:'';
					$customProductName = getTableValue('tbl_custom_orders',"productName",array("id"=>$refId));
					$customProductName = isset($customProductName)?$customProductName:'';
					$status = getTableValue('tbl_custom_orders',"order_status",array("id"=>$refId));

					$orderStatus = $status=='a'?'accepted':'rejected';
					$reminder_title = getTableValue('tbl_reminders',"reminder_title",array("id"=>$refId));
					$reminder_title=isset($reminder_title)?$reminder_title:'';

					$notification = '';
					$title="";
					$newproductposted=getTableValue('tbl_notifications',"newProductPosted",array("userId"=>$toId));
					$amountAddedInWallet=getTableValue('tbl_notifications',"amountAddedInWallet",array("userId"=>$toId));
					$receiveReplayFromAdmin=='y';
					$newPrormoPosted=getTableValue('tbl_notifications',"newPrormoPosted",array("userId"=>$toId));
					$orderStatusByAdmin=getTableValue('tbl_notifications',"orderStatusByAdmin",array("userId"=>$toId));
					$reminder=getTableValue('tbl_notifications',"reminder",array("userId"=>$toId));;
					$flag =false;
					if($notificationType=='1' && $amountAddedInWallet=='y')
					{
						$flag=true;
						$title="Product return refund";
						$notification = 'Product return refund amount '.$currencySign.$amount.' is received from admin for '.$productName;
					}
							
					if($notificationType=='2' && $amountAddedInWallet=='y')
					{
						$flag=true;
						$title="Redeem amount funded";
						$notification = 'Redeem amount '.$currencySign.$amount.' is funded';
					}		
					if($notificationType=='3' && $newproductposted=='y')
					{	
						$flag=true;
						$title="New product deal";
						$notification = 'New product deal as '.$productName.' is posted by admin';
					}
					if($notificationType=='4' && $amountAddedInWallet=='y')
					{
						$flag=true;
						$title="Wallet";
						$notification = $currencySign.$amount.' is added in your wallet';
					}

					if($notificationType=='5' && $newPrormoPosted=='y')
					{
						$flag=true;
						$title="New promo code is posted";
						$notification = 'A new promo code is posted on website by admin as '.$coupon_code;
					}
					if($notificationType=='6' && $orderStatusByAdmin=='y')
					{
						$flag=true;
						$title="Custom Order status";
						$notification = 'Custom order status is '.$orderStatus.' by admin for '.$customProductName;
					}

					if($notificationType=='7' && $reminder=='y')
					{
						$flag=true;
						$title="Reminder";
						$notification = 'Reminder for '.$reminder_title;
					}
					//send push notification for android
					if($flag)
					{						
						if($deviceType=='a' && $deviceToken!='')
						{
							$params	= array("pushtype"=>"android", 'registration_id'=>$deviceToken, 'msg'=>$notification,'title'=>$title);

							$rtn = $pushObj->sendMessage($params);

							//print_r($rtn);
						}
					}
			}
		 }
	



?>