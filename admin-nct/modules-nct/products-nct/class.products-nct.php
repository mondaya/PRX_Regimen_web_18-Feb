<?php
class products extends Home {

	public $status;
	public $data = array();

	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {
		global $db, $fields, $sessCataId;
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_product_deals';

		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();
		if($this->id>0){
			$qrySel = $this->db->pdoQuery("SELECT p.*,c.categoryName,s.subcategoryName FROM tbl_product_deals as p INNER JOIN tbl_categories as c ON(p.categoryId = c.id)
		        INNER JOIN tbl_subcategory as s ON(p.subcategoryId = s.id) where p.id = ".$this->id."")->result();

			$fetchRes = $qrySel;

			//print_r($fetchRes);exit;

			$this->data['productName'] = $this->productName = $fetchRes['productName'];
			$categoryName =getTableValue('tbl_categories','categoryName',array("id"=>$fetchRes["categoryId"]));
			$subcategoryName = getTableValue('tbl_subcategory','subcategoryName',array("id"=>$fetchRes["subcategoryId"]));

			$this->data['categoryId'] = $this->categoryId = $fetchRes["categoryId"];
			$this->data['subcategoryId'] = $this->subcategoryId = $fetchRes["subcategoryId"];
			$this->data['categoryName'] = $this->categoryName = $categoryName;
			$this->data['subcategoryName'] = $this->subcategoryName = $subcategoryName;

			$this->data['quantity'] = $this->quantity = $fetchRes['quantity'];
			$this->data['productDescription'] = $this->productDescription = $fetchRes['productDescription'];
			$this->data['actualPrice'] = $this->actualPrice = $fetchRes['actualPrice'];
			$this->data['isDiscount'] = $this->isDiscount = $fetchRes['isDiscount'];
			$this->data['discountPrice'] = $this->discountPrice = $fetchRes['discountPrice'];
			$this->data['discountPercentage'] = $this->discountPercentage = $fetchRes['discountPercentage'];
			$this->data['weight'] = $this->weight = $fetchRes['weight'];

			$this->data['isActive'] = $this->isActive = $fetchRes['isActive'];
			$this->data['createdDate'] = $this->createdDate = $fetchRes['createdDate'];
		}else{

			$this->data['isActive'] = $this->isActive = 'y';
			$this->data['isDiscount'] = $this->isDiscount = 'n';
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
		$query = $this->db->pdoQuery("select id,name from tbl_product_image where productId = ".$this->id."")->results();
		foreach ($query as $key => $value) {
			$productImage .= '
			<img src="'.SITE_UPD.'product/'.$this->id.'/'.$value['name'].'" height="100px">

			';
		}

		$content = $this->displayBox(array("label"=>"Product Name&nbsp;:","value"=>$this->productName)).
		$this->displayBox(array("label"=>"Category&nbsp;:","value"=>$this->categoryName)).
		$this->displayBox(array("label"=>"Sub Category&nbsp;:","value"=>$this->subcategoryName)).
		$this->displayBox(array("label"=>"Images&nbsp;:","value"=>$productImage)).
		$this->displayBox(array("label"=>"Quantities&nbsp;:","value"=>$this->quantity)).
		$this->displayBox(array("label"=>"Description&nbsp;:","value"=>$this->productDescription)).
		$this->displayBox(array("label"=>"Price&nbsp;:","value"=>SITE_CURR.$this->actualPrice)).
		$this->displayBox(array("label"=>"Discount Price&nbsp;:","value"=>SITE_CURR.$this->discountPrice)).
		$this->displayBox(array("label"=>"Save(%)&nbsp;:","value"=>$this->discountPercentage)).
		$this->displayBox(array("label"=>"Weight(KG)&nbsp;:","value"=>$this->weight)).
		$this->displayBox(array("label"=>"Status&nbsp;:","value"=>$this->isActive == 'y'?'Active':'Deactive'));
		$this->displayBox(array("label"=>"Created Date&nbsp;:","value"=>$this->createdDate));
		return $content;
	}
	public function getForm() {
		$content = '';

		$getSelectBoxOption=$this->getSelectBoxOption();
		$fields = array("%VALUE%","%SELECTED%","%DISPLAY_VALUE%");

		//For category option
		$qrySelCountry=$this->db->pdoQuery("SELECT * FROM tbl_categories where isActive='y' ORDER BY categoryName")->results();

		foreach ($qrySelCountry as $fetchRes) {
			$selected = ($this->categoryId==$fetchRes['id'])?"selected":"";

			$fields_replace = array($fetchRes['id'],$selected,$fetchRes['categoryName']);
			$category_option.=str_replace($fields,$fields_replace,$getSelectBoxOption);
		}

		//For sub category option
		if($this->type == 'add'){

			$subcategory_option = "<option name='subcategoryId' value=''>Please select sub category</option>";

		}else{
			$qrySelCountry=$this->db->pdoQuery("SELECT * FROM tbl_subcategory where isActive='y' and categoryId = ".$this->categoryId." ORDER BY subcategoryName")->results();

			foreach ($qrySelCountry as $fetchRes) {
				$selected = ($this->subcategoryId==$fetchRes['id'])?"selected":"";

				$fields_replace = array($fetchRes['id'],$selected,$fetchRes['subcategoryName']);
				$subcategory_option.=str_replace($fields,$fields_replace,$getSelectBoxOption);
			}
		}

		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
			$main_content = $main_content->parse();
			$status_a=($this->isActive == 'y' ? 'checked':'');
			$status_d=($this->isActive != 'y' ? 'checked':'');

			$discount_y=($this->isDiscount == 'y' ? 'checked':'');
			$discount_n=($this->isDiscount != 'y' ? 'checked':'');

			if($type=='add'){
				$img = "";
			}
			else{
				//$main_content_img = new Templater(DIR_ADMIN_TMPL.$this->module."/img_form-nct.tpl.php");
				//$main_content_img = $main_content_img->parse();
				//$fields_attach = array("%CATEGORY_PHOTO%");
				//$fields_replace_attach = array($this->data['subcategoryImage']);

				//$file_attach = 	str_replace($fields_attach, $fields_replace_attach, $main_content_img);
				$img = '';
				$query = $this->db->pdoQuery("select id,name from tbl_product_image where productId = ".$this->id."")->results();
				$img .= '<div class="product-img">';
				foreach ($query as $key => $value) {
					$img .= '<div class="single-product-img" id="img_'.$value['id'].'">
					<img id="delete-img" src="'.SITE_IMG.'delete_icon.gif" onclick="deleteProductImg('.$value['id'].')">
					<img src="'.SITE_UPD.'product/'.$this->id.'/'.$value['name'].'" height="100px">

					</div>';
				}
				$img .='</div>';


			}

			$add_more=SITE_ADM_IMG.'More-128.png';

			$fields = array(
				"%MEND_SIGN%",
				"%PRODUCT_NAME%",
				"%CATEGORY_OPTION%",
				"%SUBCATEGORY_OPTION%",
				"%PRODUCT_QUA%",
				"%DESCRIPTION%",
				"%PRODUCT_PRICE%",
				"%DISCOUNT_Y%",
				"%DISCOUNT_N%",
				"%DISCOUNT_PRICE%",
				"%WEIGHT%",
				"%STATUS_A%",
				"%STATUS_D%",
				"%TYPE%",
				"%ID%",
				"%ADD_MORE%",
				"%DISPLAY_DISCOUNT%",
				"%PRODUCT_PHOTO%",
				"%SITE_CURR%"
			);

			$displayDiscount = $this->data['isDiscount']=='n'?'style="display:none;"':'';

			$fields_replace = array(
				MEND_SIGN,
				$this->data['productName'],
				$category_option,
				$subcategory_option,
				$this->data['quantity'],
				$this->data['productDescription'],
				$this->data['actualPrice'],
				$discount_y,
				$discount_n,
				$this->data['discountPrice'],
				$this->data['weight'],
				$status_a,
				$status_d,
				$this->type,
				$this->id,
				$add_more,
				$displayDiscount,
				$img,
				SITE_CURR

			);

			//echo '<pre>';
			//print_r($fields_replace);exit;

			$content=str_replace($fields,$fields_replace,$main_content);
		return sanitize_output($content);
	}

	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		extract($this->searchArray);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );

		$aWhere = array('1');
		$sWhere = ' WHERE 1 = ? ';
		if(isset($chr) && $chr != '') {
			$sWhere .= " AND (p.productName LIKE ? OR s.subcategoryName LIKE ? OR c.categoryName LIKE ? )";
			$aWhere[] = "%$chr%";
			$aWhere[] = "%$chr%";
			$aWhere[] = "%$chr%";
		}

		if(isset($category) && $category != '') {
			$whereCond .= (empty($sWhere)?' WHERE ':' AND ')."(p.categoryId = ".$category.")";
		}

		if(isset($subcategory) && $subcategory != '') {
			$whereCond .= (empty($sWhere)?' WHERE ':' AND ')."(p.subcategoryId = ".$subcategory.")";
		}

		if(isset($sort)){
		//$sorting = (in_array($sort,array('stateName')) ? 's.' : 'c.').$sort.' '. $order;
		$alias = '';
		if($sort == 'productName'){
			$alias = 'p.';
		}else if($sort == 'categoryName'){
			$alias = 'c.';
		}else if($sort == 'subcategoryName'){
			$alias = 's.';
		}
		$sorting = $alias.$sort.' '. $order;

		}else{
			 $sorting = 'p.id DESC';
		}
		
		$qrySel1 = $this->db->pdoQuery("SELECT p.id FROM tbl_product_deals as p INNER JOIN tbl_categories as c ON(p.categoryId = c.id)
		    INNER JOIN tbl_subcategory as s ON(p.subcategoryId = s.id)
			$sWhere ".$whereCond." ORDER BY $sorting", $aWhere)->results();

		$qrySel = $this->db->pdoQuery("SELECT p.*,c.categoryName,s.subcategoryName FROM tbl_product_deals as p INNER JOIN tbl_categories as c ON(p.categoryId = c.id)
		    INNER JOIN tbl_subcategory as s ON(p.subcategoryId = s.id)
			$sWhere ".$whereCond." ORDER BY $sorting limit $offset , $rows", $aWhere)->results();

		$totalRow = count($qrySel1);
		foreach($qrySel as $fetchRes) {
			$id =  $fetchRes['id'];
			$status = $fetchRes['isActive'];

			$status = ($fetchRes['isActive']=="y") ? "checked" : "";
			$switch  =(in_array('status',$this->Permission))?$this->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$id."","check"=>$status)):'';
			$operation='';

			$operation .= (in_array('edit',$this->Permission))?$this->operation(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$id."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$id."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';
			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=view&id=".$id."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):'';

			$final_array =  array($fetchRes["productName"],$fetchRes["categoryName"],$fetchRes["subcategoryName"],SITE_CURR.$fetchRes["actualPrice"]);
			if(in_array('status',$this->Permission)){
				$final_array =  array_merge($final_array, array($switch));
			}
			if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) || in_array('status',$this->Permission)){
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