<?php
class transactionDetail {
	
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

		$productList = NULL;
		$main_content1 = new Templater(DIR_TMPL.$this->module."/product_list-nct.tpl.php");
		$productList = $main_content1->parse();

		$query = "SELECT o.*,p.id as productId,p.productName,c.categoryName,sc.subcategoryName FROM tbl_orders as o
				  LEFT JOIN tbl_product_deals as p ON(o.productId = p.id)
				  LEFT JOIN tbl_categories as c ON(p.categoryId = c.id)
				  LEFT JOIN tbl_subcategory as sc ON(p.subcategoryId = sc.id)
				  where o.transactionId = '".$this->id."'";
		
		$fetchRes = $this->db->pdoQuery($query)->results();

		foreach ($fetchRes as $key => $orders) {

			$imageName = getTableValue("tbl_product_image","name",array("productId"=>$orders['productId']));
			$imgSrc = checkImage("product/".$orders['productId'].'/'.$imageName);
			$productPrice = convertCurrency($this->currencyId,$orders['productPrice']);
			
			//For product list 
			$fields1 = array(
				'%IMG_SRC%','%PRODUCT_NM%','%ORDER_ID%','%QUANTITY%','%PURCHASE_DATE%','%PRODUCT_PRICE%','%CURR_SIGN%','%PRODUCT_ID%'
			);

			$fields_replace1 = array(
				$imgSrc,$orders['productName'],$orders['orderId'],$orders['quantity'],date('m-d-Y',strtotime($orders['createdDate'])),number_format($productPrice,2),$this->currencySign,$orders['productId']
			);

			$products .= str_replace($fields1,$fields_replace1,$productList);

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

			$deliveryDays = getDeliveryDays($orders['deliveryOption'],$this->sessUserId,$orders['pick_point']);

			
			$fields = array(
				"%TRANSACTION_ID%","%CURR_SIGN%","%TOTAL_PRODUCT_PRICE%","%DUTIES%","%ADMIN_CHARGES%","%SHIPPING_CHARGE%","%DISCOUNT%","%TOTAL_AMOUNT%","%SHIPPING_DETAIL%","%PRODUCT_LIST%","%DELIVERY_DAYS%"
			);

			//For pricing
			$totalPrice += convertCurrency($this->currencyId,$orders['productPrice']*$orders['quantity']);
			$dutiesAmount += convertCurrency($this->currencyId,$orders['dutiesAmount']);
			$adminCharge += convertCurrency($this->currencyId,$orders['adminCharge']);
			$shippingAmount += convertCurrency($this->currencyId,$orders['shippingAmount']);
			$discountAmount += convertCurrency($this->currencyId,$orders['discountAmount']);

			$totalAmount = $totalPrice + $dutiesAmount + $adminCharge + $shippingAmount - $discountAmount;

			$fields_replace = array(
				$orders['transactionId'],$this->currencySign,number_format($totalPrice,2),number_format($dutiesAmount,2),number_format($adminCharge,2),number_format($shippingAmount,2),number_format($discountAmount,2),number_format($totalAmount,2),$shippingDetail,$products,$deliveryDays
			);

			

		}
		
		$orderDetail = str_replace($fields,$fields_replace,$final_result);
		
		return $orderDetail;
	}

	

}