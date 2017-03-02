<?php
class Templates extends Home{
	
	public $category;
	public $status;
	public $data = array();
	
	public function __construct($id=0, $searchArray=array(), $type='') {		
		$this->data['id'] = $this->id = $id;
		$this->table = 'tbl_email_templates';	
	
		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();				
		if($this->id>0){
			$qrySel = $this->db->select($this->table, array("id","subject","templates","updated_date"),array("id"=>$id))->result();
			$fetchRes = $qrySel;
			$this->data['subject'] = $this->subject = $fetchRes['subject'];
			$this->data['templates'] = $this->templates = $fetchRes['templates'];
		}else{
			$this->data['subject'] = $this->subject = '';
			$this->data['templates'] = $this->templates = '';
		}
		switch($type){
			case 'add' : {
				$this->data['content'] =  (in_array('add',$this->Permission))?$this->getForm():'';
				break;
			}
			case 'edit' : {
				$this->data['content'] =  (in_array('edit',$this->Permission))?$this->getForm():'';
				break;
			}
			case 'view' : {
				$this->data['content'] =  (in_array('view',$this->Permission))?$this->viewForm():'';
				break;
			}
			case 'delete' : {
				$this->data['content'] =  (in_array('delete',$this->Permission))?json_encode($this->dataGrid()):'';
				break;
			}
			case 'datagrid' :  {
				$this->data['content'] =  (in_array('module',$this->Permission))?json_encode($this->dataGrid()):'';
			}
		}

	}	
	public function viewForm(){
		/*$content = $this->fields->displayBox(array("label"=>"Subject&nbsp;:","value"=>$this->subject)).
		'<div class="clearfix"></div><label class="control-label col-md-3">Templates&nbsp;:</label><hr/>'.$this->templates;
		return $content;*/
	}	
	public function getForm() {
		$content = NULL;
		
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
		$main_content = $main_content->parse();

		$fields = array("%SUBJECT%","%TEMPLATE%","%TYPE%","%ID%");
		$fields_replace = array($this->subject,$this->templates,$this->type,$this->id);
		$content=str_replace($fields,$fields_replace,$main_content);
		return $content;	
	}
	
	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		extract($this->searchArray);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );
		$whereCond = array();
		$whereCond = array("status"=> "a");
		if(isset($chr) && $chr != '') {
			$whereCond = $whereCond + array("and subject LIKE"=> "%$chr%");
		}

		if(isset($sort))
			$sorting = $sort.' '. $order;
		else
			$sorting = 'id DESC';

		$totalRow = $this->db->count($this->table, $whereCond);
		
		$qrySel = $this->db->select("tbl_email_templates",array("id","constant,subject","templates","updated_date"), $whereCond, " ORDER BY $sorting limit $offset , $rows" )->results();
		foreach($qrySel as $fetchRes) {
			$id =  $fetchRes['id'];
			$operation = '<a href="ajax.'.$this->module.'.php?action=edit&id='.$fetchRes['id'].'" class="btn default btn-xs black btnEdit"><i class="fa fa-edit"></i>&nbsp;Edit</a>';
			$final_array = array($fetchRes["constant"],$fetchRes["subject"]);
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
	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/".$this->module.".tpl.php");
		$main_content->breadcrumb = $this->getBreadcrumb();
		$main_content->getForm = $this->getForm();
		$final_result = $main_content->parse();
		return $final_result;
	}
}