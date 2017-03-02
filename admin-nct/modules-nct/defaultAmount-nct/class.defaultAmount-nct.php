<?php
class defaultAmount extends Home {
	
	public $status;
	public $data = array();
	
	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {		
		global $db, $fields, $sessCataId;		
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_admin_amount';	
	
		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();	
		if($this->id>0){
			$qrySel = $this->db->select($this->table, "*",array("id"=>$id))->result();
			$fetchRes = $qrySel;
			$this->data['shipping'] = $this->shipping = $fetchRes['shipping'];
			$this->data['duties'] = $this->duties = $fetchRes['duties'];
			$this->data['adminCharge'] = $this->adminCharge = $fetchRes['adminCharge'];
		}else{
			$this->data['shipping'] = $this->shipping = '';
			$this->data['duties'] = $this->duties = '';
			$this->data['adminCharge'] = $this->adminCharge = '';
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
		$content = $this->displayBox(array("label"=>"Shipping Amount (%)&nbsp;:","value"=>$this->shipping));
		$content .= $this->displayBox(array("label"=>"Duties Amount (%)&nbsp;:","value"=>$this->duties));
		$content .= $this->displayBox(array("label"=>"Admin Charge (%)&nbsp;:","value"=>$this->adminCharge));
		return $content;
	}
	public function getForm() {

		$content = '';
			$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
			$main_content = $main_content->parse();
			$status_a=($this->isActive == 'y' ? 'checked':'');
			$status_d=($this->isActive != 'y' ? 'checked':'');
	
			$fields = array("%MEND_SIGN%","%SHIPPING%","%DUTIES%","%ADMIN_CHARGE%","%TYPE%","%ID%");
	
			$fields_replace = array(MEND_SIGN,$this->data['shipping'],$this->data['duties'],$this->data['adminCharge'],$this->type,$this->id);
	
			$content=str_replace($fields,$fields_replace,$main_content);
		return sanitize_output($content);
	}
	
	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		extract($this->searchArray);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );
		$whereCond = array();
		$whereCond = "id = '1'";
		
		if(isset($sort))
			$sorting = $sort.' '. $order;
		else
			$sorting = 'id DESC';

		$totalRow = $this->db->count($this->table, $whereCond);
		
		$qrySel = $this->db->select("tbl_admin_amount","*", $whereCond, " ORDER BY $sorting limit $offset , $rows" )->results();
		foreach($qrySel as $fetchRes) {
			$id =  $fetchRes['id'];
			$operation='';
			
			$operation .= (in_array('edit',$this->Permission))?$this->operation(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$id."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';
			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=view&id=".$fetchRes['id']."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):'';
			
			
			//$row_data[] = array($fetchRes["countryName"],$switch,$operation);			
			$final_array =  array($fetchRes["shipping"],$fetchRes["duties"],$fetchRes["adminCharge"]);
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