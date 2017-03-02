<?php
class Country extends Home {
	
	public $status;
	public $data = array();
	
	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {		
		global $db, $fields, $sessCataId;		
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_country';	
	
		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();	
		if($this->id>0){
			$qrySel = $this->db->select($this->table, array("id","countryName","isActive"),array("id"=>$id))->result();
			$fetchRes = $qrySel;
			$this->data['countryName'] = $this->countryName = $fetchRes['countryName'];
			$this->data['isActive'] = $this->isActive = $fetchRes['isActive'];
		}else{
			$this->data['countryName'] = $this->countryName = '';
			$this->data['isActive'] = $this->isActive = 'y';
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
				$this->data['content'] =  '';
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
	
	public function getForm() {
		/*$content = '';
		$content.=$this->fields->form_start(array("name"=>"frmCont","extraAtt"=>"novalidate='novalidate'")).
			$this->fields->textBox(array("label"=>"".MEND_SIGN."Country : ","name"=>"countryName","class"=>"logintextbox-bg required","value"=>$this->data['countryName'])).
			$this->fields->radio(array("label"=>"Status: ","name"=>"isActive","class"=>"radioBtn-bg required","value"=>($this->isActive != '' ? $this->isActive:'n'),"values"=>array("y"=>"Active","n"=>"Deactive")));
			$content.=$this->fields->hidden(array("name"=>"type","value"=>$this->type)).
			$this->fields->hidden(array("name"=>"id","value"=>$this->id)).
			$this->fields->buttonpanel_start().
			$this->fields->button(array("onlyField"=>true,"name"=>"submitAddForm", "type"=>"submit", "class"=>"green", "value"=>"Submit", "extraAtt"=>"")).
			$this->fields->button(array("onlyField"=>true,"name"=>"cn", "type"=>"button", "class"=>"btn-toggler", "value"=>"Cancel", "extraAtt"=>"")).
			$this->fields->buttonpanel_end();	
            $content .= $this->fields->form_end();
		return sanitize_output($content);*/

		$content = '';
			$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
			$main_content = $main_content->parse();
			$status_a=($this->isActive == 'y' ? 'checked':'');
			$status_d=($this->isActive != 'y' ? 'checked':'');
	
			$fields = array("%MEND_SIGN%","%COUNTRY_NAME%","%STATUS_A%","%STATUS_D%","%TYPE%","%ID%");
	
			$fields_replace = array(MEND_SIGN,$this->data['countryName'],$status_a,$status_d,$this->type,$this->id);
	
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
			$whereCond["countryName LIKE"] = "%$chr%";
		}

		if(isset($sort))
			$sorting = $sort.' '. $order;
		else
			$sorting = 'id DESC';

		$totalRow = $this->db->count($this->table, $whereCond);
		
		$qrySel = $this->db->select("tbl_country",array("*"), $whereCond, " ORDER BY $sorting limit $offset , $rows" )->results();
		foreach($qrySel as $fetchRes) {
			$id =  $fetchRes['id'];
			$status = $fetchRes['isActive'];

			$status = ($fetchRes['isActive']=="y") ? "checked" : "";
			
			$switch  =(in_array('status',$this->Permission))?$this->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$id."","check"=>$status)):'';			
			$operation='';
			
			$operation .= (in_array('edit',$this->Permission))?$this->operation(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$id."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$id."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';

			
			//$row_data[] = array($fetchRes["countryName"],$switch,$operation);			
			$final_array =  array($fetchRes["countryName"]);
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