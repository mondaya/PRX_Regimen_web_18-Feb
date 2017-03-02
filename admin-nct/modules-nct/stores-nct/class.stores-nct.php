<?php
class Stores extends Home {
	public $data = array();

	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {
		 
		global $db, $fields;
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_banners';

		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();
		if($this->id>0){
			$qrySel = $this->db->select('tbl_stores', array("*"),array("id"=>$id))->result();
			$this->data['storeName'] = $this->storeName = $qrySel['storeName'];
			$this->data['categoryId'] = $this->categoryId = $qrySel["categoryId"];
			$this->data['subcategoryId'] = $this->subcategoryId = $qrySel["subcategoryId"];
			$this->data['storeLink'] = $this->storeLink = $qrySel['storeLink'];
			$this->data['storeCartLink'] = $this->storeCartLink = $qrySel['storeCartLink'];
			$this->data['storeImage'] = $this->storeImage = $qrySel['storeImage'];
			//$this->data['banner_link'] = $this->banner_link = $qrySel['banner_link'];
			$this->data['isActive'] = $this->isActive = $qrySel['isActive'];
			$this->data['isScrap'] = $this->isScrap = $qrySel['isScrap'];
			$this->data['createdDate'] = $this->createdDate = $qrySel['createdDate'];
		}else{
			$this->data['storeName'] = $this->storeName = "";
			$this->data['storeImage'] = $this->storeImage = "";
			//$this->data['banner_link'] = $this->banner_link = "";
			$this->data['isActive'] = $this->isActive = "";
			$this->data['isScrap'] = $this->isScrap = "";
			$this->data['createdDate'] = $this->createdDate = "";
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

	public function viewForm() {
		$storeImage = '<img src="'.checkImage('store-nct/'.$this->id.'/', $this->storeImage).'" alt="'.$this->storeName.'" title="'.$this->storeName.'"height="100" width="100" />';
		$content = $this->displayBox(array("label"=>"Store Name&nbsp;:","value"=>$this->storeName)).
		$this->displayBox(array("label"=>"Store Image&nbsp;:","value"=>$storeImage)).
		//$this->displayBox(array("label"=>"Redirection Link/URL&nbsp;:","value"=>'<a href="'.$this->banner_link.'" target="_blank">'.$this->banner_link.'</a>')).
		//$this->displayBox(array("label"=>"Scrap Status&nbsp;:","value"=>$this->isScrap == 'y'?'Scrapped':'Not Scrapped')).
		$this->displayBox(array("label"=>"Status&nbsp;:","value"=>$this->isActive == 'y'?'Active':'Deactive')).
		$this->displayBox(array("label"=>"Created Date&nbsp;:","value"=>date("F j, Y", strtotime($this->createdDate))));
		return $content;
	}

	public function getForm() {
        $content = '';

        $getSelectBoxOption=$this->getSelectBoxOption();
		$fields = array("%VALUE%","%SELECTED%","%DISPLAY_VALUE%");

		//For category option
		$qrySelCountry=$this->db->pdoQuery("SELECT * FROM tbl_categories where isActive='y' ORDER BY categoryName")->results();

		foreach ($qrySelCountry as $fetchRes) {
			$categoryIdArray = explode(',', $this->categoryId);
			$selected = '';
			if( in_array($fetchRes['id'], $categoryIdArray))
			{
			    $selected = "selected";
			}

			$fields_replace = array($fetchRes['id'],$selected,$fetchRes['categoryName']);
			$category_option.=str_replace($fields,$fields_replace,$getSelectBoxOption);
		}

		//For sub category option
		if($this->type == 'add'){

			$subcategory_option = "<option name='subcategoryId' value=''>Please select sub category</option>";

		}else{
			$qrySelCountry=$this->db->pdoQuery("SELECT * FROM tbl_subcategory where isActive='y' and categoryId IN (".$this->categoryId.") ORDER BY subcategoryName")->results();

			foreach ($qrySelCountry as $fetchRes) {
				//$selected = ($this->subcategoryId==$fetchRes['id'])?"selected":"";

				$subcategoryIdArray = explode(',', $this->subcategoryId);
				$selected = '';
				if( in_array($fetchRes['id'], $subcategoryIdArray))
				{
				    $selected = "selected";
				}

				$fields_replace = array($fetchRes['id'],$selected,$fetchRes['subcategoryName']);
				$subcategory_option.=str_replace($fields,$fields_replace,$getSelectBoxOption);
			}
		}

		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
		$main_content = $main_content->parse();
		$static_a=($this->isActive == 'y' ? 'checked':'');
		$static_d=($this->isActive != 'y' ? 'checked':'');
		$s_static_a=($this->isScrap == 'y' ? 'checked':'');
		$s_static_d=($this->isScrap != 'y' ? 'checked':'');
		$fields = array("%STORE_NM%", "%STORE_SRC%", "%STATUS_A%", "%STATUS_D%","%S_STATUS_A%", "%S_STATUS_D%", "%TYPE%", "%ID%","%OLD_IMAGE%","%STORE_LINK%","%STORE_CART_LINK%","%CATEGORY_OPTION%",
				"%SUBCATEGORY_OPTION%");
		$fields_replace = array($this->storeName, checkImage('store-nct/'.$this->id.'/', $this->storeImage), $static_a, $static_d,$s_static_a, $s_static_d, $this->type, $this->id, $this->storeImage,$this->storeLink,$this->storeCartLink,$category_option,$subcategory_option);
		$content=str_replace($fields,$fields_replace,$main_content);
		return sanitize_output($content);
	}

	public function getSelectBoxOption(){
		$content = '';
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/select_option-nct.tpl.php");
		$content.= $main_content->parse();
		return sanitize_output($content);
	}

	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		extract($this->searchArray);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );
		$whereCond = '';
		if(isset($chr) && !empty($chr)) { $whereCond = " WHERE (storeName LIKE '%".$chr."%')"; }

		if(isset($sort)) $sorting = $sort.' '. $order;
		else $sorting = 'id DESC';

		$qrySel1 =$this->db->pdoQuery("SELECT * FROM $this->table".$whereCond." ORDER BY ".$sorting."")->results();
			
		$qrySel =$this->db->pdoQuery("SELECT * FROM $this->table".$whereCond." ORDER BY ".$sorting." LIMIT ".$offset." ,".$rows." ")->results();

		$totalRow = count($qrySel1);
		foreach($qrySel as $fetchRes) {
			$id = $fetchRes['id'];
			$status = ($fetchRes['isActive']=="y") ? "checked" : "";
			$switch  =(in_array('status',$this->Permission))?$this->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$fetchRes['id']."","check"=>$status)):'';
			$storeImage = '<img src="'.checkImage('store-nct/'.$id.'/', $fetchRes['storeImage']).'" alt="'.$fetchRes['storeName'].'" title="'.$fetchRes['storeName'].'"height="100" width="100" />';
			$operation='';
			$operation .= (in_array('edit',$this->Permission))?$this->operation(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$id."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$id."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';
			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=view&id=".$id."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):'';
			$final_array =  array($fetchRes['storeName'], $storeImage);
			if(in_array('status', $this->Permission)){
				$final_array = array_merge($final_array, array($switch));
			}
			if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){
				$final_array = array_merge($final_array, array($operation));
			}
			$row_data[] = $final_array;
		}
		$result["sEcho"]=$sEcho;
		$result["iTotalRecords"] = (int)$totalRow;
		$result["iTotalDisplayRecords"] = (int)$totalRow;
		$result["aaData"] = $row_data;
		return $result;
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

	public function toggel_switch($text){
		$text['action'] = isset($text['action']) ? $text['action'] : 'Enter Action Here: ';
		$text['check'] = isset($text['check']) ? $text['check'] : '';
		$text['name'] = isset($text['name']) ? $text['name'] : '';
        $text['class'] = isset($text['class']) ? ''.trim($text['class']) : '';
        $text['extraAtt'] = isset($text['extraAtt']) ? $text['extraAtt'] : '';
        $text['switch_action'] = isset($text['switch_action']) ? $text['switch_action'] : 'updateStatus';
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module.'/switch-nct.tpl.php');
		$main_content=$main_content->parse();
		$fields = array("%NAME%","%CLASS%","%ACTION%","%SWITCH_ACTION%","%EXTRA%","%CHECK%");
		$fields_replace = array($text['name'],$text['class'],$text['action'],$text['switch_action'],$text['extraAtt'],$text['check']);
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
	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/".$this->module.".tpl.php");
		$main_content->breadcrumb = $this->getBreadcrumb();
		$final_result = $main_content->parse();
		return $final_result;
	}
}