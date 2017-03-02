<?php
class Orders extends Home {
	public $status;
	public $data = array();

	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {
		global $db, $fields, $sessCataId;
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_orders';

		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();
		if($this->id>0) {
			$productImage = '';
			$qrySel = $this->db->pdoQuery("SELECT o.*, u.firstname, u.lastname, u.email, u.countryId, u.stateId, u.cityId, u.paypalemail,u.address, p.productname, p.storeid, c.categoryName, s.subcategoryName 
				FROM tbl_orders AS o 
				INNER JOIN tbl_users AS u ON u.id=o.userid 
				INNER JOIN tbl_product_deals AS p ON p.id=o.productid 
				INNER JOIN tbl_categories AS c ON c.id=p.categoryid 
				INNER JOIN tbl_subcategory AS s ON s.id=p.subcategoryid where o.id = ".$this->id."")->result();
			//User Details
			$this->data['email'] = $this->email = base64_decode($qrySel['email']);
			$this->data['name'] = $this->name = $qrySel['firstname'].' '.$qrySel['lastname'];
			$this->data['address'] = $this->address = $qrySel['address'];
			$this->data['countryId'] = $this->countryId = $qrySel['countryId'];
			$this->data['stateId'] = $this->stateId = $qrySel['stateId'];
			$this->data['cityId'] = $this->cityId = $qrySel['cityId'];
			$countryName = getTableValue('tbl_country', 'countryName', array('id'=>$this->countryId));
			$stateName = getTableValue('tbl_state', 'stateName', array('id'=>$this->stateId));
			$cityName = getTableValue('tbl_city', 'cityName', array('id'=>$this->cityId));
			$this->data['location'] = $this->location = $cityName.', '.$stateName.', '.$countryName;
			$this->data['paypalEmail'] = $this->paypalEmail = base64_decode($qrySel['paypalemail']);

			// product
			$this->data['productName'] = $this->productName = $qrySel['productname'];
			$productImage = '';
			$query = $this->db->pdoQuery("select id,name from tbl_product_image where productId=?", array($qrySel['productId']))->results();
			if(!empty($query)) {
				foreach ($query as $key => $value) {
					$productImage .= '<img src="'.SITE_UPD.'product/'.$qrySel['productId'].'/'.$value['name'].'" height="100px">';
				}
			}
			$this->data['productImage'] = $this->productImage = $productImage;
			$this->data['orderId'] = $this->orderId = $qrySel['orderId'];
			$this->data['categoryName'] = $this->categoryName = $qrySel['categoryName'];
			$this->data['subcategoryName'] = $this->subcategoryName = $qrySel['subcategoryName'];
			$this->data['quantity'] = $this->quantity = $qrySel['quantity'];
			$this->data['createdDate'] = $this->createdDate = $qrySel['createdDate'];
			$this->data['productPrice'] = $this->productPrice = $qrySel['productPrice'];
			$this->data['dutiesAmount'] = $this->dutiesAmount = $qrySel['dutiesAmount'];
			$this->data['adminCharge'] = $this->adminCharge = $qrySel['adminCharge'];
			$this->data['discountAmount'] = $this->discountAmount = $qrySel['discountAmount'];
			$this->data['shippingAmount'] = $this->shippingAmount = $qrySel['shippingAmount'];
			$this->data['paidAmount'] = $this->paidAmount = $qrySel['paidAmount'];
			$this->data['storeId'] = $this->storeId = $qrySel['storeid'];
			$this->data['storeName'] = $this->storeName = $qrySel['storename'];
			$this->data['pick_point'] = $this->pick_point = $qrySel['pick_point'];
			$this->data['deliveryOption'] = $this->deliveryOption = $qrySel['deliveryOption'];
			if($this->deliveryOption=='p') {
				$ppoint = getTableValue('tbl_pick_points', 'pointName', array('id'=>$this->pick_point));
				$this->data['pick_point_name'] = $this->pick_point_name = $ppoint;
			}
			$this->data['deliveryStatus'] = $this->deliveryStatus = $qrySel['deliveryStatus'];
			$this->data['deliveryDays'] = $this->deliveryDays = getDeliveryDays($this->deliveryOption,$qrySel['userId'],$this->pick_point);
			$this->data['currencyValue'] = $this->currencyValue = $qrySel['currencyValue'];
		}else{
			$this->data['name'] = $this->name = '';
			$this->data['email'] = $this->email = '';
			$this->data['countryId'] = $this->countryId = 0;
			$this->data['stateId'] = $this->stateId = 0;
			$this->data['cityId'] = $this->cityId = 0;
			$this->data['paypalEmail'] = $this->paypalEmail = '';
			$this->data['productName'] = $this->productName = '';
			$this->data['productImage'] = $this->productImage = '';
			$this->data['orderId'] = $this->orderId = 0;
			$this->data['categoryName'] = $this->categoryName = '';
			$this->data['subcategoryName'] = $this->subcategoryName = '';
			$this->data['quantity'] = $this->quantity = 0;
			$this->data['createdDate'] = $this->createdDate = '';
			$this->data['actualPrice'] = $this->actualPrice = 0;
			$this->data['isDiscount'] = $this->isDiscount = 'n';
			$this->data['discountPercentage'] = $this->discountPercentage = 0;
			$this->data['discountPrice'] = $this->discountPrice = 0;
			$this->data['dutiesAmount'] = $this->dutiesAmount = 0;
			$this->data['adminCharge'] = $this->adminCharge	 = 0;
			$this->data['shippingAmount'] = $this->shippingAmount = 0;
			$this->data['storeId'] = $this->storeId = 0;
			$this->data['storeName'] = $this->storeName = '';
			$this->data['pick_point'] = $this->pick_point = '';
			$this->data['deliveryOption'] = $this->deliveryOption = '';
			$this->data['discountAmount'] = $this->discountAmount = 0;
			$this->data['currencyValue'] = $this->currencyValue = 1;
		}
		switch($type) {
			case 'view' : {
				$this->data['content'] =  $this->viewForm();
				break;
			}
			case 'edit' : {
				$this->data['content'] =  $this->getForm($type);
				break;
			}
			case 'delete' : {
				$this->data['content'] =  json_encode($this->dataGrid());
				break;
			}
			case 'datagrid' : {
				$this->data['content'] =  json_encode($this->dataGrid());
			}
		}

	}
	public function viewForm() {
		$delivery_option = ((!empty($this->deliveryOption) && $this->deliveryOption=='p')?'Pickup Point':'Door to door');
		$discount = (!empty($this->discountAmount)?SITE_CURR.number_format(($this->discountAmount), 2):'N/A');
		$content = $this->displayBox(array("label"=>"<b>User details</b>&nbsp:","value"=>'')).
		$this->displayBox(array("label"=>"Name&nbsp;:","value"=>$this->name)).
		$this->displayBox(array("label"=>"Email&nbsp;:","value"=>$this->email)).
		$this->displayBox(array("label"=>"Location&nbsp;:","value"=>$this->location)).
		$this->displayBox(array("label"=>"Paypal Email&nbsp;:","value"=>$this->paypalEmail)).
		$this->displayBox(array("label"=>"<b>Product details</b>&nbsp;","value"=>'')).
		$this->displayBox(array("label"=>"Name&nbsp;:","value"=>$this->productName)).
		$this->displayBox(array("label"=>"Images&nbsp;:","value"=>$this->productImage)).
		$this->displayBox(array("label"=>"OrderID&nbsp;:","value"=>$this->orderId)).
		$this->displayBox(array("label"=>"Category&nbsp;:","value"=>$this->categoryName)).
		$this->displayBox(array("label"=>"Sub Category&nbsp;:","value"=>$this->subcategoryName)).
		//$this->displayBox(array("label"=>"Store name&nbsp;:","value"=>$this->storeName)).
		$this->displayBox(array("label"=>"Purchased Quantity&nbsp;:","value"=>$this->quantity)).
		$this->displayBox(array("label"=>"Purchase date&nbsp;:","value"=>$this->createdDate)).
		$this->displayBox(array("label"=>"<b>Amount details</b>&nbsp;","value"=>'')).
		$this->displayBox(array("label"=>"Product price&nbsp;:","value"=>SITE_CURR.number_format(($this->productPrice), 2))).
		$this->displayBox(array("label"=>"Duties and handling amount&nbsp;:","value"=>SITE_CURR.number_format(($this->dutiesAmount), 2))).
		$this->displayBox(array("label"=>"Admin charges&nbsp;:","value"=>SITE_CURR.number_format(($this->adminCharge), 2))).
		$this->displayBox(array("label"=>"Shipping amount&nbsp;:","value"=>SITE_CURR.number_format(($this->shippingAmount), 2))).
		$content .= $this->displayBox(array("label"=>"Coupon code discount&nbsp;:","value"=>$discount)).
		$this->displayBox(array("label"=>"Total amount&nbsp;:","value"=>SITE_CURR.number_format(($this->paidAmount), 2))).
		$this->displayBox(array("label"=>"Deliver option&nbsp;:","value"=>$delivery_option));
		// return $content;
		$address = getDeliveryAddress($this->deliveryOption,$this->pick_point);
		if($this->deliveryOption=='p') {
			$content .= $this->displayBox(array("label"=>"Pick-up point&nbsp;:","value"=>$address));
		}else{
			$content .= $this->displayBox(array("label"=>"Shipping address&nbsp;:","value"=>$address));
		}

		$d_staus = (($this->deliveryStatus=='d')?'Delivered':(($this->deliveryStatus=='s')?'Shipped':(($this->deliveryStatus=='r')?'Return':'Pending')));
		$content .= $this->displayBox(array("label"=>"Delivery Days&nbsp;:","value"=>$this->deliveryDays));
		$content .= $this->displayBox(array("label"=>"Shipping Status&nbsp;:","value"=>$d_staus));
		return $content;
	}

	public function getForm($type) {

		$content = '';
			
			$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
			$main_content = $main_content->parse();
			
			$delivery_p=($this->deliveryStatus == 'p' ? 'checked':'');
			$delivery_d=($this->deliveryStatus == 'd' ? 'checked':'');
			$delivery_s=($this->deliveryStatus == 's' ? 'checked':'');
			
			$fields = array("%TYPE%","%ID%","%PRODUCT_NAME%","%DELIVERY_S%","%DELIVERY_D%","%DELIVERY_P%");

			$fields_replace = array($this->type,$this->id,$this->productName,$delivery_s,$delivery_d,$delivery_p);

			$content=str_replace($fields,$fields_replace,$main_content);
		return sanitize_output($content);
	}

	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		extract($this->searchArray);
		// printr($this->searchArray, true);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );

		$aWhere = array('1');
		$sWhere = ' WHERE 1 = ? ';
		if(isset($chr) && $chr != '') {
			$sWhere .= " AND (u.firstname LIKE ? OR u.lastname LIKE ? OR u.email LIKE ? OR p.productName LIKE ?)";
			$aWhere[] = "%$chr%"; $aWhere[] = "%$chr%"; $aWhere[] = "%$chr%"; $aWhere[] = "%$chr%";
		}

		if(!empty($date_range) && $date_range!='~'){
			$date_range =explode("~",$date_range);
			if(!empty($date_range[0]) && !empty($date_range[1])) {
				$date_from = date("Y-m-d", strtotime($date_range[0]));
				$date_to = date("Y-m-d", strtotime($date_range[1]));
				$whereCond .= "AND o.createdDate >= ?  AND o.createdDate <= ?";
				$aWhere[] = "$date_from"; $aWhere[] = "$date_to";
			}
		}

		if(isset($country) && !empty($country)) {
			$whereCond .= (empty($sWhere)?' WHERE ':' AND ')."(u.countryId = ".$country.")";
		}

		if(isset($state) && $state != '') {
			$whereCond .= (empty($sWhere)?' WHERE ':' AND ')."(u.stateId = ".$state.")";
		}

		if(isset($status) && $status != '') {
			$whereCond .= (empty($sWhere)?' WHERE ':' AND ')."(o.deliveryStatus = '".$status."')";
		}

		if(isset($sort)){
			//$sorting = (in_array($sort,array('stateName')) ? 's.' : 'c.').$sort.' '. $order;
			$alias = '';
			if($sort == 'firstName'){
				$alias = 'u.';
			}else if($sort == 'productName'){
				$alias = 'p.';
			}else if($sort == 'paidAmount'){
				$alias = 'o.';
			}
			$sorting = $alias.$sort.' '. $order;

		}else{
			 $sorting = 'o.id DESC';
		}

		$qrySel1 = $this->db->pdoQuery("SELECT o.id FROM tbl_orders as o INNER JOIN tbl_users AS u ON u.id=o.userId INNER JOIN tbl_product_deals AS p ON p.id=o.productId $sWhere  AND o.paymentStatus = 'y'".$whereCond." ORDER BY ".$sorting."", $aWhere)->results();

		$qrySel = $this->db->pdoQuery("SELECT o.*, u.firstName, u.lastName, u.email, p.productName FROM tbl_orders as o INNER JOIN tbl_users AS u ON u.id=o.userId INNER JOIN tbl_product_deals AS p ON p.id=o.productId $sWhere  AND o.paymentStatus = 'y'".$whereCond." ORDER BY ".$sorting." LIMIT ".$offset." ,".$rows."", $aWhere)->results();

		$totalRow = count($qrySel1);
		foreach($qrySel as $fetchRes) {
			$id =  $fetchRes['id'];

			$operation='';
			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=view&id=".$id."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):''; 
			
			$operation .= (in_array('edit',$this->Permission))?$this->operation(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$id."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';

			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$id."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';

			$final_array = array($fetchRes["firstName"].' '.$fetchRes["lastName"], base64_decode($fetchRes["email"]), $fetchRes["productName"], SITE_CURR.number_format(($fetchRes["paidAmount"]), 2));



			if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) || in_array('status',$this->Permission)) {
				$final_array =  array_merge($final_array, array($operation));
			}
			$row_data[] = $final_array;
		}
		$result["sEcho"]=$sEcho;
		$result["iTotalRecords"] = (int)$totalRow;
		$result["iTotalDisplayRecords"] = (int)$totalRow;
		$result["aaData"] = $row_data;
		return $result;
	}

	public function getSelectBoxOption() {
		$content = '';
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/select_option-nct.tpl.php");
		$content.= $main_content->parse();
		return sanitize_output($content);
	}

	public function toggel_switch($text) {
		$text['action'] = isset($text['action']) ? $text['action'] : 'Enter Action Here: ';
		$text['check'] = isset($text['check']) ? $text['check'] : '';
		$text['name'] = isset($text['name']) ? $text['name'] : '';
        $text['class'] = isset($text['class']) ? ''.trim($text['class']) : '';
        $text['extraAtt'] = isset($text['extraAtt']) ? $text['extraAtt'] : '';

		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module.'/switch-nct.tpl.php');
		$main_content=$main_content->parse();
		$fields = array("%NAME%","%CLASS%","%ACTION%","%EXTRA%","%CHECK%");
		$fields_replace = array($text['name'],$text['class'],$text['action'],$text['extraAtt'],$text['check']);
		return str_replace($fields,$fields_replace,$main_content);
	}

	public function operation($text) {
		$text['href'] = isset($text['href']) ? $text['href'] : 'Enter Link Here: ';
		$text['value'] = isset($text['value']) ? $text['value'] : '';
		$text['name'] = isset($text['name']) ? $text['name'] : '';
        $text['class'] = isset($text['class']) ? ''.trim($text['class']) : '';
        $text['extraAtt'] = isset($text['extraAtt']) ? $text['extraAtt'] : '';

		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module.'/operation-nct.tpl.php');
		$main_content=$main_content->parse();
		$fields = array("%HREF%","%CLASS%","%VALUE%","%EXTRA%");
		$fields_replace = array($text['href'],$text['class'],$text['value'],$text['extraAtt']);
		return str_replace($fields,$fields_replace,$main_content);
	}

	public function displaybox($text) {
 		$text['label'] = isset($text['label']) ? $text['label'] : 'Enter Text Here: ';
		$text['value'] = isset($text['value']) ? $text['value'] : '';
		$text['name'] = isset($text['name']) ? $text['name'] : '';
		$text['class'] = isset($text['class']) ? 'form-control-static '.trim($text['class']) : 'form-control-static';
        $text['onlyField'] = isset($text['onlyField']) ? $text['onlyField'] : false;
        $text['extraAtt'] = isset($text['extraAtt']) ? $text['extraAtt'] : '';

		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module.'/displaybox.tpl.php');
		$main_content=$main_content->parse();
		$fields = array("%LABEL%","%CLASS%","%VALUE%");
		$fields_replace = array($text['label'],$text['class'],$text['value']);
		return str_replace($fields,$fields_replace,$main_content);
	}

	public function getPageContent() {
		$final_result = NULL;
		$country_option = '<option value="">Select Country</option>';
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/".$this->module.".tpl.php");
		$main_content->breadcrumb = $this->getBreadcrumb();
		$search = array('%COUNTRY%');
		$country = $this->db->select('tbl_country', array('*'), array(), 'ORDER BY countryName ASC')->results();
		foreach ($country as $key => $value) {
			$country_option .= '<option value="'.$value['id'].'">'.$value['countryName'].'</option>';
		}
		$replce = array($country_option);
		$final_result = $main_content->parse();
		$final_result = str_replace($search, $replce, $final_result);
		return $final_result;
	}
}