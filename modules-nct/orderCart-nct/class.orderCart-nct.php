<?php
class orderCart extends Home{
	
	protected $db;
	public $module;
		
	function __construct($module) {
		global $db,$fields,$sessUserId,$currencyId,$currencyCode,$currencySign;
				
		$this->db = $db;
		$this->fields = $fields;
		$this->table = 'tbl_product_deal';
		$this->module=$module;
		$this->sessUserId=$sessUserId;
		$this->currencyId=$currencyId;
		$this->currencyCode=$currencyCode;
		$this->currencySign=$currencySign;
		
	}


	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();

		$query = "SELECT o.*,p.productName,p.weight FROM tbl_orders as o 
				  LEFT JOIN tbl_product_deals as p ON(p.id = o.productId)
				  where o.paymentStatus = 'n' AND o.userId = ".$this->sessUserId."";
		$fetchRes = $this->db->pdoQuery($query)->results();

		//echo '<pre>';
		//print_r($orders);exit;

		$fields = array(
			"%CURR_SIGN%","%PRODUCT_PRICE%","%TOTAL_PRODUCT_PRICE%","%QUANTITY%","%DUTIES%","%ADMIN_CHARGES%","%SHIPPING_CHARGE%","%DISCOUNT%","%TOTAL_AMOUNT%","%DELIVERY_D%","%DELIVERY_P%","%PICK_OPTION%","%PICK_CENTER%","%D2D_OPTION%","%TOTAL_AMOUNT_USD%","%TOTAL_AMOUNT_NAIRA%"
		);
		$amountInDoller = '';
		foreach ($fetchRes as $key => $orders) {
			

			//For pricing
			$price += convertCurrency($this->currencyId,$orders['productPrice']);
			$totalPrice += convertCurrency($this->currencyId,$orders['productPrice']*$orders['quantity']);
			$dutiesAmount += convertCurrency($this->currencyId,$orders['dutiesAmount']);
			$adminCharge += convertCurrency($this->currencyId,$orders['adminCharge']);
			$shippingAmount += convertCurrency($this->currencyId,$orders['shippingAmount']);
			$discountAmount += convertCurrency($this->currencyId,$orders['discountAmount']);

			$totalAmount += $totalPrice + $dutiesAmount + $adminCharge + $shippingAmount - $discountAmount;

			$delivery_d = $orders['deliveryOption'] == 'd'?'selected':'';
			$delivery_p = $orders['deliveryOption'] == 'p'?'selected':'';

			$pickOption = $d2dOption = $pickCenter = '';
			if($orders['deliveryOption'] == 'p'){		
				$pickOption = $this->getPickOption($orders['pick_point'],$orders['stateId']);
				$pickCenter = $this->getPickCenter($orders['pick_point']);
			}else if($orders['deliveryOption'] == 'd'){
				$d2dOption = $this->getD2doption();
			}

			$amount += ($orders['productPrice']*$orders['quantity']) + $orders['dutiesAmount'] + $orders['adminCharge'] + $orders['shippingAmount'] - $orders['discountAmount'];
			$amountInDoller += convertCurrency('1',$amount);
		}
			
			$fields_replace = array(
				$this->currencySign,number_format($price,2),number_format($totalPrice,2),$orders['quantity'],number_format($dutiesAmount,2),number_format($adminCharge,2),number_format($shippingAmount,2),number_format($discountAmount,2),number_format(convertCurrency($this->currencyId,$amount),2),$delivery_d,$delivery_p,$pickOption,$pickCenter,$d2dOption,number_format($amountInDoller,2),number_format(convertCurrency(7,$amount),2)
			);

			$orderDetail .= str_replace($fields,$fields_replace,$final_result);

		

		return $orderDetail;
	}

	public function getPickOption($pickId){

		$stateId = getTableValue('tbl_pick_points','stateId',array('id'=>$pickId));
		if($stateId > 0){
			$query = $this->db->pdoQuery("select * from tbl_pick_points where isActive = 'y' and stateId = ".$stateId." order by id desc")->results();
		}else{
			$query = $this->db->pdoQuery("select * from tbl_pick_points where isActive = 'y' order by id desc")->results();			
		}
		
		$content = '';
		
		$content = '<div class="form-group">
	                <select class="gender comment" name="pickPoint" id="pickOption">
	                	<option value="">Select Pick Point</option>';
		foreach ($query as $key => $value) {
			$selected = $pickId == $value['id']?'selected':'';
			$content.='<option value="'.$value['id'].'" '.$selected.'>'.$value['pointAddress'].'</option>';
		}

		$content.='</select>
	            </div>';

	    return $content;
	}

	public function getPickCenter($pickId){
		
		$content = '';
		$query = $this->db->pdoQuery("select pp.id,pp.stateId,s.stateName,c.countryName from tbl_pick_points as pp 
			LEFT JOIN tbl_state as s ON(s.id = pp.stateId)
			LEFT JOIN tbl_country as c ON(c.id = pp.countryId)
			where pp.isActive = 'y' GROUP BY pp.stateId")->results();

		$content = '<div class="form-group">
	                <select class="gender comment" name="pickCenter" id="pickCenter">
	                	<option value="">Select Pickup Center</option>';
		foreach ($query as $key => $value) {
			$selected = $pickId == $value['id']?'selected':'';
			$content.='<option value="'.$value['stateId'].'" '.$selected.'>'.$value['stateName'].','.$value['countryName'].'</option>';
		}

		$content.='</select>
	            </div>';

	    return $content;
	}

	public function getProductData(){
		$query = "SELECT c.id as cartId,o.id orderId,o.productPrice,o.quantity,p.quantity as maxQuntity,p.id,p.productName,p.weight FROM tbl_orders as o 
				  LEFT JOIN tbl_product_deals as p ON(p.id = o.productId)
				  LEFT JOIN tbl_cart as c ON(c.productId = o.productId)
				  where o.paymentStatus = 'n' AND c.userId = ".$this->sessUserId." AND o.userId = ".$this->sessUserId."";
		
		$result = $this->db->pdoQuery($query);
 		$fetchRes = $result->results();
 		return $fetchRes;
	}

	public function getProductList(){
		$content = NULL;
		$products=$this->getProductData();
		
		//echo '<pre>';
		//print_r($products);exit;

		if(empty($products)){
			
			$main_content = new Templater(DIR_TMPL."/no_table_records-nct.tpl.php");
			$content = $main_content->parse();	
		
		}else{


			$main_content = new Templater(DIR_TMPL.$this->module."/product_list-nct.tpl.php");
			$main_content = $main_content->parse();	
			$fields = array("%CART_ID%","%ID%","%PRODUCT_NM%","%WEIGHT%","%CURR_SIGN%","%PRODUCT_PRICE%","%TOTAL_PRODUCT_PRICE%","%QUANTITY%","%IMG%","%MAX_QUANTITY%");	
			
			foreach($products as $value){
				$productPrice = convertCurrency($this->currencyId,$value['productPrice']);
				$totalAmount = convertCurrency($this->currencyId,$value['productPrice']*$value['quantity']);

				$imageName = getTableValue("tbl_product_image","name",array("productId"=>$value['id']));
				$productImg = checkImage("product/".$value['id'].'/'.$imageName);

				$fields_replace = array($value['cartId'],$value['id'],$value['productName'],$value['weight'],$this->currencySign,number_format($productPrice,2),number_format($totalAmount,2),$value['quantity'],$productImg,$value['maxQuntity']);
				$content .= str_replace($fields, $fields_replace, $main_content);
			}	
		}
		
		return sanitize_output($content);
	}

	public function getD2doption(){

			$address = getTableValue('tbl_users','address',array('id'=>$this->sessUserId));
			$content = '<div class="form-group col-lg-8">
							<select class="comment" name="d2dOption" id="d2dOption">
								<option value="">'.$address.'</option>
								<option value="addNewAddress">Add new address</option>
							</select>
						 </div>';
			return $content;
	}
}