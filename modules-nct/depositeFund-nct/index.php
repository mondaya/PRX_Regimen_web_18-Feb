<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.depositeFund-nct.php");
	
	$module = 'depositeFund-nct';
	$table = 'tbl_orders';
	
	$winTitle = 'Deposite Fund -' .SITE_NM;
    $headTitle = 'Deposite Fund';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));

    if(isset($_POST['submitDeposite']) && $_POST['submitDeposite'] == 'Pay'){
    	extract($_POST);
    	$amountUsd = number_format(convertCurrency(USD_CURR_ID,$amount),2);
    	?>
	   	
	   	<form action="<?php echo PAYPAL_URL;?>" method="post" name="formcart">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="<?php echo ADMIN_PAYPAL; ?>">
                <input type="hidden" name="item_name" value="Deposite Fund"> 
                <input type="hidden" name="currency_code" value="USD">
                <input type="hidden" name="amount" value="<?php echo $amountUsd; ?>">
                <input type="hidden" name="custom" value="<?php echo $amount.'__'.$sessUserId; ?>"/>
                <input type="hidden" name="bn" value="NCryptedTechnologies_SP_EC">
                <input type="hidden" name="return" value="<?php echo SITE_MOD.'depositeFund-nct/paypal_thankyou.php'; ?>">
                <input type="hidden" name="cancel_return" value="<?php echo SITE_URL; ?>">
                <input type="hidden" name="notify_url" value="<?php echo SITE_MOD.'depositeFund-nct/paypal_notify.php'; ?>">
        </form>
        <script type="text/javascript">
			document.formcart.submit();
		</script>

    <?php }
	$mainObj = new deposite($module);

	$pageContent = $mainObj->getPageContent();
 	
 	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>