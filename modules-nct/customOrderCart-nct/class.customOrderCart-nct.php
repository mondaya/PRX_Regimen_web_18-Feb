<?php
class customOrderCart extends Home{
	
	protected $db;
	public $module;
		
	function __construct($module,$id) {
		global $db,$fields,$sessUserId,$currencyId,$currencyCode,$currencySign;
				
		$this->db = $db;
		$this->id = $id;
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

		$query = "SELECT * FROM tbl_custom_orders where id = ".$this->id."";
		$orders = $this->db->pdoQuery($query)->result();

		$fields = array(
			"%ID%","%ORDER_ID%","%PRODUCT_NM%","%CURR_SIGN%","%PRODUCT_PRICE%","%TOTAL_PRODUCT_PRICE%","%QUANTITY%","%SIZE%","%COLOR%","%DATE%","%STATUS%","%DUTIES%","%ADMIN_CHARGES%","%SHIPPING_CHARGE%","%DISCOUNT%","%TOTAL_AMOUNT%","%DELIVERY_D%","%DELIVERY_P%","%PICK_OPTION%","%PICK_CENTER%","%D2D_OPTION%","%TOTAL_AMOUNT_USD%","%TOTAL_AMOUNT_NAIRA%"
		);

		//For pricing
		$price = convertCurrency($this->currencyId,$orders['productPrice']);
		$totalPrice = convertCurrency($this->currencyId,$orders['productPrice']*$orders['quantity']);
		$dutiesAmount = convertCurrency($this->currencyId,$orders['dutiesAmount']);
		$adminCharge = convertCurrency($this->currencyId,$orders['adminCharge']);
		$shippingAmount = convertCurrency($this->currencyId,$orders['shippingAmount']);
		$discountAmount = convertCurrency($this->currencyId,$orders['discountAmount']);

		$totalAmount = $totalPrice + $dutiesAmount + $adminCharge + $shippingAmount - $discountAmount;

		$status = (($orders['order_status']=='a')?'Accepted':(($orders['order_status'] == 'r')?'Rejected':'Pending'));

		$delivery_d = $orders['deliveryOption'] == 'd'?'selected':'';
		$delivery_p = $orders['deliveryOption'] == 'p'?'selected':'';

		$pickOption = $d2dOption = $pickCenter = '';
		if($orders['deliveryOption'] == 'p'){		
			$pickOption = $this->getPickOption($orders['pick_point'],$orders['stateId']);
			$pickCenter = $this->getPickCenter($orders['pick_point']);
		}else if($orders['deliveryOption'] == 'd'){
			$d2dOption = $this->getD2doption();
		}

		$amount = ($orders['productPrice']*$orders['quantity']) + $orders['dutiesAmount'] + $orders['adminCharge'] + $orders['shippingAmount'] - $orders['discountAmount'];
		$amountInDoller = convertCurrency('1',$amount);
		
		$fields_replace = array(
			$orders['id'],$orders['orderId'],$orders['productName'],$this->currencySign,number_format($price,2),number_format($totalPrice,2),$orders['quantity'],$orders['size'],$orders['color'],date('m-d-Y',strtotime($orders['createdDate'])),$status,number_format($dutiesAmount,2),number_format($adminCharge,2),number_format($shippingAmount,2),number_format($discountAmount,2),number_format($totalAmount,2),$delivery_d,$delivery_p,$pickOption,$pickCenter,$d2dOption,number_format($amountInDoller,2),number_format(convertCurrency(7,$amount),2)
		);

		$orderDetail = str_replace($fields,$fields_replace,$final_result);

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