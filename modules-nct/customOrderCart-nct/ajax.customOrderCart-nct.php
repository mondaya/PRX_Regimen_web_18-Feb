<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.customOrderCart-nct.php");
$module = 'customOrderCart-nct';

$mainObj = new customOrderCart($module);

$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');
$countryId='';
if($action == 'changeShipping'){
	$data = array();
	$deliveryOption = $_POST['deliveryOption'];
	$id = $_POST['id'];

	$db->update('tbl_custom_orders',array("deliveryOption"=>$deliveryOption),array("id"=>$id));

	if($deliveryOption == 'p'){
	
		$query = $db->pdoQuery("select pp.id,pp.stateId,s.stateName,c.countryName from tbl_pick_points as pp 
			LEFT JOIN tbl_state as s ON(s.id = pp.stateId)
			LEFT JOIN tbl_country as c ON(c.id = pp.countryId)
			where pp.isActive = 'y' group by pp.stateId order by pp.id desc")->results();

		$content = '<div class="form-group">
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
		$orders = $db->pdoQuery("select productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_custom_orders where id = ".$id."")->result();

		$productPrice = $orders['productPrice']*$orders['quantity'];
	    $finalShipping = getd2dShippingAmount($productPrice);
	    $adminCharge= getAdminCharge($productPrice,$countryId);
		$dutiesAmount=getDutiesAmount($productPrice,$countryId);
		$db->update('tbl_custom_orders',array("shippingAmount"=>strval($finalShipping),"dutiesAmount"=>strval($dutiesAmount),"adminCharge"=>strval($adminCharge)),array("id"=>$id));

		$data['shipping'] = $currencySign.number_format(convertCurrency($currencyId,$finalShipping),2);
		$finalTotal = $finalShipping + ($orders['productPrice'] * $orders['quantity']) + $dutiesAmount + $adminCharge - $orders['discountAmount'];
		$data['totalPrice'] = $currencySign.number_format(convertCurrency($currencyId,$finalTotal),2);
		$data['adminCharge'] = $currencySign.number_format(convertCurrency($currencyId,$adminCharge),2);
		$data['dutiesAmount'] = $currencySign.number_format(convertCurrency($currencyId,$dutiesAmount),2);

		//For d2d option
		$address = getTableValue('tbl_users','address',array('id'=>$sessUserId));
		$content .= '<div class="form-group col-lg-8">
			<select class="comment" name="d2dOption" id="d2dOption">
				<option value="">'.$address.'</option>
				<option value="addNewAddress">Add new address</option>
			</select></div>';

		$data['d2dOption'] = $content;

		echo json_encode($data);
	}

}
else if($action == 'changePickPoints'){
	$data = array();
	//$stateId = $_POST['stateId'];
	$id = $_POST['id'];
	//$pick_point = getTableValue('tbl_pick_points','id',array("stateId"=>$stateId));

	$pickId = $_POST['pickId'];
	$stateId = getTableValue('tbl_pick_points','stateId',array('id'=>$pickId));
	$countryId = getTableValue('tbl_pick_points','countryId',array('id'=>$pickId));
	
	$pick_point = getTableValue('tbl_pick_points','id',array("stateId"=>$stateId));

	$orders = $db->pdoQuery("select productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_custom_orders where id = ".$id."")->result();

	$productPrice = $orders['productPrice']*$orders['quantity'];
	$finalShipping = getPickShippingAmount($productPrice,$stateId);
	$adminCharge= getAdminCharge($productPrice,$countryId);
	$dutiesAmount=getDutiesAmount($productPrice,$countryId);
	$db->update('tbl_custom_orders',array("shippingAmount"=>strval($finalShipping),"pick_point"=>$pick_point,"dutiesAmount"=>strval($dutiesAmount),"adminCharge"=>strval($adminCharge)),array("id"=>$id));

	$data['shipping'] = $currencySign.number_format(convertCurrency($currencyId,$finalShipping),2);
	$finalTotal = $finalShipping + ($orders['productPrice'] * $orders['quantity']) + $dutiesAmount + $adminCharge - $orders['discountAmount'];
	$data['totalPrice'] = $currencySign.number_format(convertCurrency($currencyId,$finalTotal),2);
	$data['adminCharge'] = $currencySign.number_format(convertCurrency($currencyId,$adminCharge),2);
	$data['dutiesAmount'] = $currencySign.number_format(convertCurrency($currencyId,$dutiesAmount),2);

	echo json_encode($data);
}

else if($action == 'applyCoupon'){
	$data = array();
	$today = date('Y-m-d');
	$couponCode = $_POST['couponCode'];
	$id = $_POST['id'];

	$query = "select id,discount from tbl_coupons where is_active = 'y' and coupon_code = '".$couponCode."' AND start_date <= '".$today."' AND end_date >= '".$today."'";
	$coupon = $db->pdoQuery($query);
	$totalRow = $coupon->affectedRows();
	$fetchRes = $coupon->result();

	if($totalRow == 0){
		$data['message'] = "<p style='color:red;font-size:15px;'>Invalid code</p>";
	}else{
		$discount = $fetchRes['discount'];
		$couponId = $fetchRes['id'];

		$orders = $db->pdoQuery("select productPrice,shippingAmount,dutiesAmount,adminCharge,quantity from tbl_custom_orders where id = ".$id."")->result();

		$totalAmount = ($orders['productPrice'] * $orders['quantity']) + ($orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']);
		
		$discountPrice = ($totalAmount * $discount) / 100;
		$productPrice = $orders['productPrice'] * $orders['quantity'];
		$finalAmount = ($productPrice + $orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']) - $discountPrice;

		//Update discount in custom order table
		$db->update("tbl_custom_orders",array("discountAmount"=>strval($discountPrice),"is_coupon_used"=>"y","couponId"=>$couponId),array("id"=>$id));

		$data['discountPrice'] = $currencySign.number_format(convertCurrency($currencyId,$discountPrice),2);
		$data['finalAmount'] = $currencySign.number_format(convertCurrency($currencyId,$finalAmount),2);
		$data['message'] = "<p style='color:green;font-size:15px;'>Congratulations, Your are getting ".$discount."% discount</p>";

	}

	echo json_encode($data);

} else if($action == 'changePickCenter'){

		$stateId = $_POST['stateId'];

		$query = $db->pdoQuery("select * from tbl_pick_points where isActive = 'y' and stateId = ".$stateId." order by id desc")->results();

		$content = '<div class="form-group">
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

?>