<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");

	include("class.redeem-nct.php");
	$module = "redeem-nct";
	$table = "tbl_redeem_request";

	$styles = array(array("data-tables/DT_bootstrap.css",SITE_ADM_PLUGIN),
					array("bootstrap-switch/css/bootstrap-switch.min.css",SITE_ADM_PLUGIN));

	$scripts= array("core/datatable.js",
					array("data-tables/jquery.dataTables.js",SITE_ADM_PLUGIN),
					array("data-tables/DT_bootstrap.js",SITE_ADM_PLUGIN),
					array("bootstrap-switch/js/bootstrap-switch.min.js",SITE_ADM_PLUGIN),
					array("data-table-columnFilter.js",SITE_ADM_PLUGIN));

	chkPermission($module);
	$Permission=chkModulePermission($module);

	$metaTag = getMetaTags(array("description"=>"Admin Panel",
			"keywords"=>'Admin Panel',
			"author"=>SITE_NM));

	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;
	$action = isset($_GET["action"]) ? trim($_GET["action"]) : '';

	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Redeem Requests';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);

	//echo $action;exit;
	if($action == 'pay' && $id > 0){

		$qrySel = "SELECT u.id as userId,u.paypalEmail,u.firstName,u.creditAmount,u.email,r.amount FROM tbl_redeem_request as r
		 LEFT JOIN tbl_users as u ON(r.userId = u.id)
		 where r.status = 'p' and r.id = ".$id."";

		$qry_fetch = $db->pdoQuery($qrySel);
	 	$fetchRes = $qry_fetch->result();
		$totalRows = $qry_fetch->affectedRows();
		$paypalEmail = base64_decode($fetchRes['paypalEmail']);
		//echo '<pre>';
		//print_r($fetchRes);
		//exit;

		$amountUsd = number_format(convertCurrency(USD_CURR_ID,$fetchRes['amount']),2);

		if($totalRows > 0){?>

			<form action="<?php echo PAYPAL_URL;?>" method="post" name="formcart">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="<?php echo $paypalEmail; ?>">
                <input type="hidden" name="item_name" value="Redeem Amount"> 
                <input type="hidden" name="currency_code" value="USD">
                <input type="hidden" name="amount" value="<?php echo $amountUsd; ?>">
                <input type="hidden" name="custom" value="<?php echo $fetchRes['amount'].'__'.$sessUserId.'__'.$id; ?>"/>
                <input type="hidden" name="return" value="<?php echo SITE_ADM_MOD.'redeem-nct/paypal_thankyou.php'; ?>">
                <input type="hidden" name="cancel_return" value="<?php echo SITE_URL; ?>">
                <input type="hidden" name="notify_url" value="<?php echo SITE_ADM_MOD.'redeem-nct/paypal_notify.php'; ?>">
	        </form>
	        <script type="text/javascript">
				document.formcart.submit();
			</script>


		<?php }else{

			$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Invalid request.'));
			redirectPage(SITE_ADM_MOD.$module);

		}
	}

	if($action == 'reject' && $id > 0){

		$qrySel = "SELECT u.id as userId,u.paypalEmail,u.firstName,u.creditAmount,u.email,u.redeemAmount,r.amount FROM tbl_redeem_request as r
		 LEFT JOIN tbl_users as u ON(r.userId = u.id)
		 where r.status = 'p' and r.id = ".$id."";

		$qry_fetch = $db->pdoQuery($qrySel);
	 	$fetchRes = $qry_fetch->result();
		$totalRows = $qry_fetch->affectedRows();
		

		$amountUsd = convertCurrency(USD_CURR_ID,$fetchRes['amount']);
		//echo '<pre>';
		//print_r($fetchRes);

		if($totalRows > 0){

			//Update redeem status
			$db->update("tbl_redeem_request",array("status"=>"r"),array("id"=>$id));

			//Update user credit amount
			$oldCreditAmount = $fetchRes['creditAmount'];
			$newCreditAmount = strval($oldCreditAmount + $amountUsd);
			$db->update("tbl_users",array("creditAmount"=>$newCreditAmount),array("id"=>$fetchRes['userId']));

			//Update user redeem amount
			$oldRedeemAmount = $fetchRes['redeemAmount'];
			$newRedeemAmount = strval($oldRedeemAmount - $amountUsd);
			$db->update("tbl_users",array("redeemAmount"=>$newRedeemAmount),array("id"=>$fetchRes['userId']));

			$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Redeem request rejected successfully and redeem amount credited in user wallet successfully.'));
			redirectPage(SITE_ADM_MOD.$module);
		}else{
			$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Invalid request.'));
			redirectPage(SITE_ADM_MOD.$module);
		}
	}

	if(isset($_POST['submitAddForm'])){
		
		$qrySel = "SELECT u.id as userId,u.firstName,u.email FROM tbl_redeem_request as r
		 LEFT JOIN tbl_users as u ON(r.userId = u.id)
		 where r.id = ".$_POST['id']."";

		$qry_fetch = $db->pdoQuery($qrySel);
	 	$fetchRes = $qry_fetch->result();
		$totalRows = $qry_fetch->affectedRows();
		//print_r($fetchRes);exit;

		//For email to user
		$contArray = array(
			"USER_NM"=>$fetchRes['firstName'],
			"MESSAGE"=>$_POST['message']
		);
		sendMail(base64_decode($fetchRes['email']),"redeem_replay",$contArray);

		$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Message sent successfully.'));
		redirectPage(SITE_ADM_MOD.$module);

	}



	$objReturn = new redeem($module);
	$pageContent = $objReturn->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");
//	require_once(DIR_ADMIN_THEME."default.nct");