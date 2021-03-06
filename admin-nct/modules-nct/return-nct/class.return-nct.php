<?php
class Return_request extends Home {

	public $status;
	public $data = array();

	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {
		global $db, $fields, $sessCataId;
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_return_request';

		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();
		if($this->id>0){
			$qrySel = $this->db->pdoQuery("SELECT r.id,r.productId,r.subject,r.message,u.firstName,u.lastName,u.email,u.paypalEmail,p.productName,r.subject,r.adminPaid,o.paidAmount,o.orderId,o.quantity,o.createdDate,o.discountAmount,o.deliveryOption,o.dutiesAmount,o.adminCharge,o.shippingAmount,o.paidAmount,o.deliveryStatus,o.productPrice,ct.cityName,co.countryName,s.stateName,pc.categoryName,psc.subcategoryName FROM tbl_return_request as r
				  LEFT JOIN tbl_product_deals as p ON(r.productId = p.id)
				  LEFT JOIN tbl_categories as pc ON(p.categoryId = pc.id)
				  LEFT JOIN tbl_subcategory as psc ON(p.subcategoryId = psc.id)
				  LEFT JOIN tbl_users as u ON(r.userId = u.id)
				  LEFT JOIN tbl_city as ct ON(u.cityId = ct.id)
				  LEFT JOIN tbl_state as s ON(u.stateId = s.id)
				  LEFT JOIN tbl_country as co ON(u.countryId = co.id)
				  LEFT JOIN tbl_orders as o ON(r.orderId = o.id) where r.id = ".$this->id."")->result();

			$fetchRes = $qrySel;

			$this->data['userName'] = $this->userName = $fetchRes["firstName"].' '.$fetchRes["lastName"];
			$this->data['email'] = $this->email = $fetchRes["email"];
			$this->data['location'] = $this->location = $fetchRes["cityName"].','.$fetchRes["stateName"].','.$fetchRes["countryName"];
			$this->data['paypalEmail'] = $this->paypalEmail = $fetchRes["paypalEmail"];
			$this->data['productName'] = $this->productName = $fetchRes["productName"];
			$this->data['productId'] = $this->productId = $fetchRes["productId"];
			$this->data['orderId'] = $this->orderId = $fetchRes["orderId"];
			$this->data['categoryName'] = $this->categoryName = $fetchRes["categoryName"];
			$this->data['subcategoryName'] = $this->subcategoryName = $fetchRes["subcategoryName"];
			$this->data['quantity'] = $this->quantity = $fetchRes["quantity"];
			$this->data['purchaseDate'] = $this->purchaseDate = date('Y-m-d',strtotime($fetchRes["createdDate"]));
			$this->data['discountAmount'] = $this->discountAmount = SITE_CURR.'&nbsp'.$fetchRes["discountAmount"];
			$this->data['productPrice'] = $this->productPrice = SITE_CURR.'&nbsp'.$fetchRes["productPrice"];
			$this->data['deliveryOption'] = $this->deliveryOption = $fetchRes["deliveryOption"]=='p'?'Pick-up point':'Door-to-door delivery';
			$this->data['dutiesAmount'] = $this->dutiesAmount = SITE_CURR.'&nbsp'.$fetchRes["dutiesAmount"];
			$this->data['adminCharge'] = $this->adminCharge = SITE_CURR.'&nbsp'.$fetchRes["adminCharge"];
			$this->data['shippingAmount'] = $this->shippingAmount = SITE_CURR.'&nbsp'.$fetchRes["shippingAmount"];
			$this->data['totalAmount'] = $this->totalAmount = SITE_CURR.'&nbsp'.$fetchRes["paidAmount"];
			$this->data['reason'] = $this->reason = $fetchRes["subject"];
			$this->data['message'] = $this->message = $fetchRes["message"];

			if($fetchRes["deliveryStatus"] == 'p'){
				$deliveryStatus = 'Pending';
			}else if($fetchRes["deliveryStatus"] == 's'){
				$deliveryStatus = 'Shipped';
			}else if($fetchRes["deliveryStatus"] == 'd'){
				$deliveryStatus = 'Delivered';
			}else if($fetchRes["deliveryStatus"] == 'r'){
				$deliveryStatus = 'Return';
			}

			$this->data['deliveryStatus'] = $this->deliveryStatus = $deliveryStatus;

		}else{
			//$this->data['blogName'] = $this->blogName = '';

		}
		switch($type){
			case 'add' : {
				$this->data['content'] =  $this->getForm();
				break;
			}
			case 'edit' : {
				$this->data['content'] =  $this->getForm();
				break;
			}
			case 'view' : {
				$this->data['content'] =  $this->viewForm();
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
	public function viewForm(){

		//For product image
		$productImage = '';
		$query = $this->db->pdoQuery("select id,name from tbl_product_image where productId = ".$this->productId."")->results();
		foreach ($query as $key => $value) {
			$productImage .= '
			<img src="'.SITE_UPD.'product/'.$this->productId.'/'.$value['name'].'" height="100px">

			';
		}

		//For return image
		$addedImage = '';
		$query = $this->db->pdoQuery("select id,imageName from tbl_return_image where returnId = ".$this->id."")->results();
		foreach ($query as $key => $value) {
			$addedImage .= '
			<img src="'.SITE_UPD.'returnImage/'.$value['imageName'].'" height="100px">

			';
		}

		$content = $this->displayBox(array("label"=>"User Name&nbsp;:","value"=>$this->userName)).
		$this->displayBox(array("label"=>"User email&nbsp;:","value"=>base64_decode($this->email))).
		$this->displayBox(array("label"=>"User location&nbsp;:","value"=>$this->location)).
		$this->displayBox(array("label"=>"User Paypal&nbsp;:","value"=>base64_decode($this->paypalEmail))).
		$this->displayBox(array("label"=>"Product name&nbsp;:","value"=>$this->productName)).
		$this->displayBox(array("label"=>"Images&nbsp;:","value"=>$productImage)).
		$this->displayBox(array("label"=>"Order id&nbsp;:","value"=>$this->orderId)).
		$this->displayBox(array("label"=>"Product category&nbsp;:","value"=>$this->categoryName)).
		$this->displayBox(array("label"=>"Product subcategory&nbsp;:","value"=>$this->subcategoryName)).
		$this->displayBox(array("label"=>"Purchased quantity&nbsp;:","value"=>$this->quantity)).
		$this->displayBox(array("label"=>"Purchase date&nbsp;:","value"=>$this->purchaseDate)).
		$this->displayBox(array("label"=>"Product price&nbsp;:","value"=>$this->productPrice)).
		$this->displayBox(array("label"=>"Deliver option&nbsp;:","value"=>$this->deliveryOption)).
		$this->displayBox(array("label"=>"Duties and handling amount&nbsp;:","value"=>$this->dutiesAmount)).
		$this->displayBox(array("label"=>"Admin charges&nbsp;:","value"=>$this->adminCharge)).
		$this->displayBox(array("label"=>"Shipping amount&nbsp;:","value"=>$this->shippingAmount)).
		$this->displayBox(array("label"=>"Coupon code discount&nbsp;:","value"=>$this->discountAmount)).
		$this->displayBox(array("label"=>"Total amount&nbsp;:","value"=>$this->totalAmount)).
		$this->displayBox(array("label"=>"Subject&nbsp;:","value"=>$this->reason)).
		$this->displayBox(array("label"=>"Message&nbsp;:","value"=>$this->message)).
		$this->displayBox(array("label"=>"Added image&nbsp;:","value"=>$addedImage)).
		$this->displayBox(array("label"=>"Shipping Status&nbsp;:","value"=>$this->deliveryStatus));
		return $content;
	}

	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		//print_r($this->searchArray);exit;
		extract($this->searchArray);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );

		$aWhere = array('1');
		$sWhere = ' WHERE 1 = ? ';
		if(isset($chr) && $chr != '') {
			$sWhere .= " AND (p.productName LIKE ? OR u.firstName LIKE ? )";
			$aWhere[] = "%$chr%";
			$aWhere[] = "%$chr%";
		}

		if($date_range !='~' && $date_range !=''){
			$date_range=explode("~",$date_range);
			//print_r($date_range);exit;
			if(!empty($date_range[0]) && !empty($date_range[1])) {
				$date_from=date("Y-m-d",strtotime($date_range[0]));
				$date_to=date("Y-m-d",strtotime($date_range[1]));
				$_SESSION["data_from"] =$data_from;
				$_SESSION["data_to"] =$data_to;
				$whereCond .= " AND (r.createdDate BETWEEN ? AND ?)";
				$aWhere[] = "$date_from";
				$aWhere[] = "$date_to";
			}
		}
		if(isset($sort))
			$sorting = $sort.' '. $order;
		else
			$sorting = 'r.id DESC';

		$qrySel1 = $this->db->pdoQuery("SELECT r.id FROM tbl_return_request as r
		 LEFT JOIN tbl_product_deals as p ON(r.productId = p.id)
		 LEFT JOIN tbl_users as u ON(r.userId = u.id)
		 LEFT JOIN tbl_orders as o ON(r.orderId = o.id)
		 $sWhere ".$whereCond." ORDER BY $sorting ", $aWhere)->results();

		$qrySel = $this->db->pdoQuery("SELECT r.id,u.firstName,p.productName,r.subject,r.adminPaid,o.paidAmount FROM tbl_return_request as r
		 LEFT JOIN tbl_product_deals as p ON(r.productId = p.id)
		 LEFT JOIN tbl_users as u ON(r.userId = u.id)
		 LEFT JOIN tbl_orders as o ON(r.orderId = o.id)
		 $sWhere ".$whereCond." ORDER BY $sorting limit $offset , $rows", $aWhere)->results();

		$totalRow = count($qrySel1);
		foreach($qrySel as $fetchRes) {
			$id =  $fetchRes['id'];
			$operation='';

			$finalPrice = $fetchRes["paidAmount"];

			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$id."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';

			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=view&id=".$id."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):'';

			if($fetchRes['adminPaid'] == 'n'){
				$operation .= (in_array('pay',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>SITE_URL.'admin-nct/modules-nct/return-nct/index.php?action=pay&id='.$id.'',"class"=>"btn default blue btn-xs","value"=>'&nbsp;Pay')):'';
			}

			$paid = $fetchRes['adminPaid']=='y'?'Paid':'N/A';
			$final_array =  array($fetchRes["firstName"],$fetchRes["productName"],$fetchRes["subject"],SITE_CURR.' '.number_format($finalPrice,2),$paid);

			if(in_array('pay',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) || in_array('status',$this->Permission)){
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
	public function getSelectBoxOption(){
		$content = '';
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/select_option-nct.tpl.php");
		$content.= $main_content->parse();
		return sanitize_output($content);
	}
	public function toggel_switch($text){
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
	public function operation($text){

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
	public function displaybox($text){

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
	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/".$this->module.".tpl.php");
		$main_content->breadcrumb = $this->getBreadcrumb();
		$final_result = $main_content->parse();
		return $final_result;
	}
}