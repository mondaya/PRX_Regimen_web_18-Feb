<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.orderCart-nct.php");
$module = 'orderCart-nct';

$mainObj = new orderCart($module);

$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');
$countryId='';
if($action == 'changeShipping'){
	$data = array();
	$deliveryOption = $_POST['deliveryOption'];
	
	$db->update('tbl_orders',array("deliveryOption"=>$deliveryOption),array("userId"=>$sessUserId));

	if($deliveryOption == 'p'){
		$content = '';	
		$query = $db->pdoQuery("select pp.id,pp.stateId,s.stateName,c.countryName from tbl_pick_points as pp 
			LEFT JOIN tbl_state as s ON(s.id = pp.stateId)
			LEFT JOIN tbl_country as c ON(c.id = pp.countryId)
			where pp.isActive = 'y' group by pp.stateId order by pp.id desc")->results();

		$content .= '<div class="form-group">
	                <select class="gender comment" name="pickCenter" id="pickCenter">
	                	<option value="">Select Pickup Center</option>';
		foreach ($query as $key => $value) {
			$content.='<option value="'.$value['stateId'].'">'.$value['stateName'].','.$value['countryName'].'</option>';
		}

		$content.='</select>
	            </div>';

	    $data['pickCenter'] = $content;
	    echo json_encode($data);
	}
	else if($deliveryOption == 'd'){
		$content = '';

		$fetchRes = $db->pdoQuery("select id,productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_orders where paymentStatus = 'n' AND userId = ".$sessUserId."")->results();

		$finalShippingTotal = $finalTotal =$dutiesAmount =$adminCharge=$discountAmount=0;
		
		foreach ($fetchRes as $key => $orders) {
			
			$productPrice = $orders['productPrice']*$orders['quantity'];
			$finalShipping = getd2dShippingAmount($productPrice);
			$adminChargeu= getAdminCharge($productPrice,$countryId);
			$dutiesAmountu=getDutiesAmount($productPrice,$countryId);
			$db->update('tbl_orders',array("shippingAmount"=>strval($finalShipping),"adminCharge"=>strval($adminChargeu),"dutiesAmount"=>strval($dutiesAmountu)),array("id"=>$orders['id'])); 

			$finalShippingTotal += $finalShipping;
			$shipping = $currencySign.number_format(convertCurrency($currencyId,$finalShippingTotal),2);
			$finalTotal += $finalShipping + ($orders['productPrice'] * $orders['quantity']) + $orders['dutiesAmount'] + $orders['adminCharge'] - $orders['discountAmount'];
			$totalPrice = $currencySign.number_format(convertCurrency($currencyId,$finalTotal),2);
			$dutiesAmount+=  $dutiesAmountu; 
			$adminCharge += $adminChargeu; 
			
		}
		//For d2d option
		$address = getTableValue('tbl_users','address',array('id'=>$sessUserId));

		$content = '<div class="form-group col-lg-8">
				<select class="comment" name="d2dOption" id="d2dOption">
					<option value="">'.$address.'</option>
					<option value="addNewAddress">Add new address</option>
				</select></div>';

		$data['shipping'] = $shipping;
		$data['dutiesAmount'] = $currencySign.number_format(convertCurrency($currencyId,$dutiesAmount,2));
		
		$data['adminCharge'] =  $currencySign.number_format(convertCurrency($currencyId,$adminCharge,2));
		$data['totalPrice'] = $totalPrice;
		$data['d2dOption'] = $content;

		echo json_encode($data);
	}

}
else if($action == 'changePickPoints'){
	$data = array();

	$pickId = $_POST['pickId'];
	$stateId = getTableValue('tbl_pick_points','stateId',array('id'=>$pickId));
	
	$pick_point = getTableValue('tbl_pick_points','id',array("stateId"=>$stateId));

	$fetchRes = $db->pdoQuery("select id,productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_orders where paymentStatus = 'n' AND userId = ".$sessUserId."")->results();
	$countryId = getTableValue('tbl_pick_points','countryId',array('id'=>$pickId));
	foreach ($fetchRes as $key => $orders) {

		//$finalShipping = ($orders['productPrice']*$orders['quantity']) * $shippingAmount / 100;
		$productPrice = $orders['productPrice']*$orders['quantity'];
		$finalShipping = getPickShippingAmount($productPrice,$stateId);
		$adminChargeu= getAdminCharge($productPrice,$countryId);
		$dutiesAmountu=getDutiesAmount($productPrice,$countryId);
			
		$db->update('tbl_orders',array("shippingAmount"=>strval($finalShipping),"pick_point"=>$pickId,"adminCharge"=>strval($adminChargeu),"dutiesAmount"=>strval($dutiesAmountu)),array("id"=>$orders['id']));

		$finalShippingTotal += $finalShipping;
		$shipping = $currencySign.number_format(convertCurrency($currencyId,$finalShippingTotal),2);
		$finalTotal += $finalShipping + ($orders['productPrice'] * $orders['quantity']) + $orders['dutiesAmount'] + $orders['adminCharge'] - $orders['discountAmount'];
		$totalPrice = $currencySign.number_format(convertCurrency($currencyId,$finalTotal),2);
		$dutiesAmount+=  $dutiesAmountu; 
		$adminCharge += $adminChargeu;
	}

	$data['shipping'] = $shipping;
	$data['dutiesAmount'] = $currencySign.number_format(convertCurrency($currencyId,$dutiesAmount,2));
	$data['adminCharge'] =  $currencySign.number_format(convertCurrency($currencyId,$adminCharge,2));
	$data['totalPrice'] = $totalPrice;

	echo json_encode($data);
}

else if($action == 'applyCoupon'){
	$data = array();
	$today = date('Y-m-d');
	$couponCode = $_POST['couponCode'];
	
	$query = "select id,discount from tbl_coupons where is_active = 'y' and coupon_code = '".$couponCode."' AND start_date <= '".$today."' AND end_date >= '".$today."'";
	$coupon = $db->pdoQuery($query);
	$totalRow = $coupon->affectedRows();
	$fetchRes = $coupon->result();

	if($totalRow == 0){
		$data['message'] = "<p style='color:red;font-size:15px;'>Invalid code</p>";
	}else{
		$discount = $fetchRes['discount'];
		$couponId = $fetchRes['id'];

		$fetchRes = $db->pdoQuery("select id,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity from tbl_orders where paymentStatus = 'n' AND userId = ".$sessUserId."")->results();

		//$productPrice = getTableValue('tbl_orders','productPrice',array("id"=>$id));

		foreach ($fetchRes as $key => $orders) {
			$totalAmount = ($orders['productPrice'] * $orders['quantity']) + ($orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']);
			$discountPrice = ($totalAmount * $discount) / 100;
			
			$productPrice = $orders['productPrice'] * $orders['quantity'];
			$finalAmount += ($productPrice + $orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']) - $discountPrice;

			//Update discount in custom order table

			$db->update("tbl_orders",array("discountAmount"=>strval($discountPrice),"is_coupon_used"=>"y","couponId"=>$couponId),array("id"=>$orders['id']));

			$discountPriceTotal += $discountPrice;
			$data['discountPrice'] = $currencySign.number_format(convertCurrency($currencyId,$discountPriceTotal),2);
			$data['finalAmount'] = $currencySign.number_format(convertCurrency($currencyId,$finalAmount),2);
			$data['message'] = "<p style='color:green;font-size:15px;'>Congratulations, Your are getting ".$discount."% discount</p>";

		}
	}
	echo json_encode($data);
	
} else if($action == 'changePickCenter'){
		$content = '';
		$stateId = $_POST['stateId'];

		$query = $db->pdoQuery("select * from tbl_pick_points where isActive = 'y' and stateId = ".$stateId." order by id desc")->results();

		$content .= '<div class="form-group">
	                <select class="gender comment" name="pickOption" id="pickOption">
	                	<option value="">Select Pickup Address</option>';
		foreach ($query as $key => $value) {
			$content.='<option value="'.$value['id'].'">'.$value['pointAddress'].'</option>';
		}

		$content.='</select>
	            </div>';

	    $data['pickOption'] = $content;
	    echo json_encode($data);

} else if($action == 'addNewAddress'){

		$content = $mainObj->getUserData($sessUserId);

	    $data['addNewAddress'] = $content;
	    echo json_encode($data);

}

//echo json_encode($data);

?>