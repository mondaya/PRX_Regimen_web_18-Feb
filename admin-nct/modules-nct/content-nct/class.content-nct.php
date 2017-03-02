<?php
class Content extends Home{
	
	public $page_name;
	public $page_title; 
	public $meta_keyword;
	public $meta_desc;
	public $page_desc;
	public $isActive;

	public $data = array();
	
	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {		
		global $db, $fields, $sessCataId;		
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_content';	
	
		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
			parent::__construct();
		if($this->id>0){
			$qrySel = $this->db->select($this->table, array("pId","pageName","section","pageTitle","metaKeyword","metaDesc","pageDesc","isActive","createdDate"),array("pId"=>$id))->result();
			$fetchRes = $qrySel;
			$this->data['pageName'] = $this->pageName = $fetchRes['pageName'];
			$this->data['section'] = $this->section = $fetchRes['section'];
			$this->data['pageTitle'] = $this->pageTitle = $fetchRes['pageTitle'];
			$this->data['metaKeyword'] = $this->metaKeyword = $fetchRes['metaKeyword'];
			$this->data['metaDesc'] = $this->metaDesc = $fetchRes['metaDesc'];
			$this->data['pageDesc'] = $this->pageDesc = $fetchRes['pageDesc'];
			$this->data['isActive'] = $this->isActive = $fetchRes['isActive'];	
		}else{
			$this->data['pageName'] = $this->pageName = '';
			$this->data['section'] = $this->section = '';
			$this->data['pageTitle'] = $this->pageTitle = '';
			$this->data['metaKeyword'] = $this->metaKeyword = '';
			$this->data['metaDesc'] = $this->metaDesc = '';
			$this->data['pageDesc'] = $this->pageDesc = '';	
			$this->data['isActive'] = $this->isActive = 'a';
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
		$content = $this->displayBox(array("label"=>"Page Name&nbsp;:","value"=>$this->pageName)).
		$this->displayBox(array("label"=>"Page Title&nbsp;:","value"=>$this->pageTitle)).
		$this->displayBox(array("label"=>"Met Keyword&nbsp;:","value"=>$this->metaKeyword)).
		$this->displayBox(array("label"=>"Meta Description&nbsp;:","value"=>$this->metaDesc)).
		$this->displayBox(array("label"=>"Page Description&nbsp;:","value"=>$this->pageDesc));
		return $content;
	}
	public function getForm() {
			$content = '';
			$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
			$main_content = $main_content->parse();
			$static_a=($this->isActive == 'y' ? 'checked':'');
			$static_d=($this->isActive != 'y' ? 'checked':'');

			$static_s=($this->section == 's' ? 'checked':'');
			$static_h=($this->section == 'h' ? 'checked':'');
			$static_w=($this->section == 'w' ? 'checked':'');
	
			$fields = array("%PAGE_NAME%","%PAGE_TITLE%","%META_KEYWORD%","%META_DESCRIPTION%","%PAGE_DESCRIPTION%","%STATIC_A%","%STATIC_D%","%TYPE%","%ID%","%STATIC_S%","%STATIC_H%","%STATIC_W%");
	
			$fields_replace = array($this->data['pageName'],$this->data['pageTitle'],$this->data['metaKeyword'],$this->data['metaDesc'],$this->data['pageDesc'],$static_a,$static_d,$this->type,$this->id,$static_s,$static_h,$static_w);
	
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
			$whereCond = array("pageName LIKE"=> "%$chr%");
			$whereCond =array("pageTitle LIKE"=>"%$chr%");
		}

		if(isset($sort))
			$sorting = $sort.' '. $order;
		else
			$sorting = 'pId DESC';

		$totalRow = $this->db->count($this->table, $whereCond);
		
		$qrySel = $this->db->select("tbl_content",array("pId","pageName","pageTitle","metaKeyword","metaDesc","pageDesc","isActive","createdDate"), $whereCond, " ORDER BY $sorting limit $offset , $rows" )->results();
		foreach($qrySel as $fetchRes) {
			$status = ($fetchRes['isActive']=="y") ? "checked" : "";
				   
			$switch  =(in_array('status',$this->Permission))?$this->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$fetchRes['pId']."","check"=>$status)):'';			
			$operation='';
			$operation .= (in_array('edit',$this->Permission))?$this->operation(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$fetchRes['pId']."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$fetchRes['pId']."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';
			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=view&id=".$fetchRes['pId']."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):'';
						   
			$final_array =  array($fetchRes["pageTitle"],$fetchRes["pageName"]);
			if(in_array('status',$this->Permission)){
				$final_array =  array_merge($final_array, array($switch));
			}			
			if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){ 		
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