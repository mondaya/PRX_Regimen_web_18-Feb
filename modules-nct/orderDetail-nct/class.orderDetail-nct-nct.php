<?php
class orderDetail {
	
	protected $db;
	public $module;
		
	function __construct($module,$id) {
		global $db,$fields,$sessUserId,$currencyId,$currencyCode,$currencySign;
				
		$this->db = $db;
		$this->id = $id;
		$this->sessUserId = $sessUserId;
		$this->fields = $fields;
		$this->table = 'tbl_product_deal';
		$this->module=$module;
		$this->currencyId=$currencyId;
		$this->currencyCode=$currencyCode;
		$this->currencySign=$currencySign;
		
	}

	public function getShippingDetail(){

	}

	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();

		$query = "SELECT o.*,p.id as productId,p.productName,p.isDiscount,p.discountPrice,p.actualPrice,c.categoryName,sc.subcategoryName FROM tbl_orders as o
				  LEFT JOIN tbl_product_deals as p ON(o.productId = p.id)
				  LEFT JOIN tbl_categories as c ON(p.categoryId = c.id)
				  LEFT JOIN tbl_subcategory as sc ON(p.subcategoryId = sc.id)
				  where o.id = ".$this->id."";
		$orders = $this->db->pdoQuery($query)->result();

		//For shipping detail
		$shippingDetail = '';
		if($orders['paymentStatus'] == 'y'){
			$result = NULL;
			$content = new Templater(DIR_TMPL.$this->module."/shippingDetail-nct.tpl.php");
			$result = $content->parse();

			$deliveryOption = $orders['deliveryOption'] == 'p'?'Pick Point':'Door To Door Delivery';
			$shipping = (($orders['deliveryStatus'] == 's')?'Shipped':(($orders['deliveryStatus'] == 'd')?'Delivered':(($orders['deliveryStatus'] == 'p')?'Pending':'Returned')));
			

			$address = getDeliveryAddress($orders['deliveryOption'],$orders['pick_point']);
			if($orders['deliveryOption'] == 'p'){
				$addressTitle = 'Pick point address';
			}else if($orders['deliveryOption'] == 'd'){
				$addressTitle = 'Delivery address';
			}

			
			$fields1 = array(
				"%DELIVERY_OPTION%","%ADDRESS_TITLE%","%ADDRESS%","%SHIPPING_STATUS%"
			);

			$fields_replace1 = array(
				$deliveryOption,$addressTitle,$address,$shipping
			);

			$shippingDetail = str_replace($fields1,$fields_replace1,$result);
		}

		//For return button
		$return = NULL;
		if($orders['deliveryStatus'] != 'r' && $orders['deliveryStatus']=='d'){
			$content = new Templater(DIR_TMPL.$this->module."/returnButton-nct.tpl.php");
			$return = $content->parse();
		}

		$imageName = getTableValue("tbl_product_image","name",array("productId"=>$orders['productId']));
		$imgSrc = checkImage("product/".$orders['productId'].'/'.$imageName);

		//For delivery days
		$deliveryDays = getDeliveryDays($orders['deliveryOption'],$this->sessUserId,$orders['pick_point']);

		//Discount price
		$actualPrice = $currencySign = '';
		if($orders['isDiscount'] == 'y'){
			$price = number_format($orders['actualPrice'],2);
			$currencySign = $this->currencySign;
			$actualPrice = $currencySign.$price;
		}
	
		$fields = array(
			"%ID%","%ORDER_ID%","%PRODUCT_NM%","%CURR_SIGN%","%PRODUCT_PRICE%","%ACTUAL_PRICE%","%TOTAL_PRODUCT_PRICE%","%QUANTITY%","%DATE%","%STATUS%","%DUTIES%","%ADMIN_CHARGES%","%SHIPPING_CHARGE%","%DISCOUNT%","%TOTAL_AMOUNT%","%SHIPPING_DETAIL%","%CATE_NM%","%SUB_CATE_NM%","%IMG_SRC%","%RETURN_BUTTON%","%RETURN_POLICY%","%DELIVERY_DAYS%"
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
		
		$fields_replace = array(
			$orders['id'],$orders['orderId'],$orders['productName'],$this->currencySign,number_format($price,2),$actualPrice,number_format($totalPrice,2),$orders['quantity'],date('m-d-Y',strtotime($orders['createdDate'])),$status,number_format($dutiesAmount,2),number_format($adminCharge,2),number_format($shippingAmount,2),number_format($discountAmount,2),number_format($totalAmount,2),$shippingDetail,$orders['categoryName'],$orders['subcategoryName'],$imgSrc,$return,RETURN_POLICY,$deliveryDays
		);

		$orderDetail = str_replace($fields,$fields_replace,$final_result);

		return $orderDetail;
	}

	

}