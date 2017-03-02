<?php
class category extends Home {

	public $status;
	public $data = array();

	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {
		global $db, $fields, $sessCataId;
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_categories';

		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();
		if($this->id>0){
			$qrySel = $this->db->select($this->table, array("id","categoryName","categoryPhoto","description","isActive","is_display","createdDate"),array("id"=>$id))->result();
			$fetchRes = $qrySel;
			$this->data['categoryName'] = $this->categoryName = $fetchRes['categoryName'];
			$this->data['description'] = $this->description = $fetchRes['description'];
			$this->data['categoryPhoto'] = $this->categoryPhoto = checkImage('category/'.$this->id.'/'.$fetchRes['categoryPhoto']);
			$this->categoryPhoto = '<img src="'.$this->categoryPhoto.'" style="height:100px;">';
			$this->data['isActive'] = $this->isActive = $fetchRes['isActive'];
			$this->data['is_display'] = $this->is_display = $fetchRes['is_display'];
			$this->data['createdDate'] = $this->createdDate = $fetchRes['createdDate'];

		}else{
			$this->data['categoryName'] = $this->categoryName = '';
			$this->data['description'] = $this->description = '';
			$this->data['isActive'] = $this->isActive = 'y';
			$this->data['is_display'] = $this->is_display = 'n';
		}
		switch($type){
			case 'add' : {
				$this->data['content'] =  $this->getForm($type);
				break;
			}
			case 'edit' : {
				$this->data['content'] =  $this->getForm($type);
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
		$content = $this->displayBox(array("label"=>"Category Name&nbsp;:","value"=>$this->categoryName)).
		$content = $this->displayBox(array("label"=>"Description&nbsp;:","value"=>$this->description)).
		$this->displayBox(array("label"=>"Category Image&nbsp;:","value"=>$this->categoryPhoto)).
		$this->displayBox(array("label"=>"Status&nbsp;:","value"=>$this->isActive == 'y'?'Active':'Deactive')).
		$this->displayBox(array("label"=>"Posted date&nbsp;:","value"=>$this->createdDate));
		return $content;
	}
	public function getForm($type) {

		$content = '';
			if($type=='add'){
				$file_attach = "";
			}
			else{
				$main_content_img = new Templater(DIR_ADMIN_TMPL.$this->module."/img_form-nct.tpl.php");
				$main_content_img = $main_content_img->parse();
				$fields_attach = array("%CATEGORY_PHOTO%");
				$fields_replace_attach = array($this->data['categoryPhoto']);

				$file_attach = 	str_replace($fields_attach, $fields_replace_attach, $main_content_img);
			}

			$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
			$main_content = $main_content->parse();
			$status_a=($this->isActive == 'y' ? 'checked':'');
			$status_d=($this->isActive != 'y' ? 'checked':'');

			$home_a=($this->is_display == 'y' ? 'checked':'');
			$home_d=($this->is_display != 'y' ? 'checked':'');
			$add_more=SITE_ADM_IMG.'More-128.png';

			$fields = array("%MEND_SIGN%","%CATEGORY_NAME%","%DESCRIPTION%","%STATUS_A%","%STATUS_D%","%HOME_A%","%HOME_D%","%TYPE%","%ID%","%CAT_PHOTO%","%ADD_MORE%");

			$fields_replace = array(MEND_SIGN,$this->data['categoryName'],$this->data['description'],$status_a,$status_d,$home_a,$home_d,$this->type,$this->id,$file_attach,$add_more);

			$content=str_replace($fields,$fields_replace,$main_content);
		return sanitize_output($content);
	}

	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		extract($this->searchArray);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );
		$whereCond = array();
		if(isset($chr) && $chr != '') {
			$whereCond["categoryName LIKE"] = "%$chr%";
		}

		if(isset($sort))
			$sorting = $sort.' '. $order;
		else
			$sorting = 'id DESC';

		$totalRow = $this->db->count($this->table, $whereCond);

		$qrySel = $this->db->select($this->table,array("id","categoryName","description","isActive","createdDate"), $whereCond, " ORDER BY $sorting limit $offset , $rows")->results();
		foreach($qrySel as $fetchRes) {
			$id =  $fetchRes['id'];
			$status = $fetchRes['isActive'];
			$status = ($fetchRes['isActive']=="y") ? "checked" : "";

			$switch  =(in_array('status',$this->Permission))?$this->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$id."","check"=>$status)):'';
			$operation='';

			$operation .= (in_array('edit',$this->Permission))?$this->operation(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$id."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$id."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';
			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=view&id=".$id."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):'';


			//$row_data[] = array($fetchRes["countryName"],$switch,$operation);
			$final_array =  array($fetchRes["categoryName"],substr($fetchRes["description"],0,50));
			if(in_array('status',$this->Permission)){
				$final_array =  array_merge($final_array, array($switch));
			}
			if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission)  ){
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