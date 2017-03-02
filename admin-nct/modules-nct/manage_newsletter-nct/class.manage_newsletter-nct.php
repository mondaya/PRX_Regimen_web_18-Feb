<?php
class Content extends Home{
	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {
		global $db, $fields, $sessCataId;
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_newsletter';

		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
			parent::__construct();
		if($this->id>0){
			$qrySel = $this->db->select($this->table, array("*"), array("id"=>$id))->result();
			$this->data['name'] = $this->name = $qrySel['name'];
			$this->data['subject'] = $this->subject = $qrySel['subject'];
			$this->data['description'] = $this->description = $qrySel['description'];
			$this->data['is_active'] = $this->is_active = $qrySel['is_active'];
			$this->data['created_date'] = $this->created_date =  $qrySel['created_date'];
		}else{
			$this->data['name'] = $this->name = '';
			$this->data['subject'] = $this->subject = '';
			$this->data['description'] = $this->description = '';
			$this->data['is_active'] = $this->is_active = 'a';
			$this->data['created_date'] = $this->created_date = '';
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
			case 'send' : {
				$this->data['content'] =  $this->sendForm();
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
		$content = $this->displayBox(array("label"=>"Newsletter Name&nbsp;:","value"=>$this->name)).
		$content = $this->displayBox(array("label"=>"Newsletter Subject&nbsp;:","value"=>$this->subject)).
		$content = $this->displayBox(array("label"=>"Newsletter Content&nbsp;:","value"=>dispContent($this->description))).
		$this->displayBox(array("label"=>"Status&nbsp;:","value"=>$this->is_active == 'y'?'Active':'Deactive')).
		$this->displayBox(array("label"=>"Posted date&nbsp;:","value"=>$this->created_date));
		return $content;
	}

	public function getForm() {
        $content = '';
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
		$main_content = $main_content->parse();
		$static_a=($this->is_active == 'y' ? 'checked':'');
		$static_d=($this->is_active != 'y' ? 'checked':'');
		$fields = array("%NEWSLETTER_NAME%","%NEWSLETTER_SUBJECT%","%NEWSLETTER_CONTENT%","%STATIC_A%","%STATIC_D%","%TYPE%","%ID%");
		$fields_replace = array($this->data['name'], $this->data['subject'], $this->data['description'], $static_a, $static_d, $this->type, $this->id);
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
			$whereCond = array("name LIKE"=> "%$chr%");
		}

		if(isset($sort))
			$sorting = $sort.' '. $order;
		else
			$sorting = 'id DESC';

		$totalRow = $this->db->count($this->table, $whereCond);

		$qrySel = $this->db->select($this->table, array("*"), $whereCond, " ORDER BY $sorting limit $offset , $rows")->results();
		foreach($qrySel as $fetchRes) {
			$status = ($fetchRes['is_active']=="y") ? "checked" : "";
			$switch  =(in_array('status',$this->Permission))?$this->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$fetchRes['id']."","check"=>$status)):'';
			$operation='';

			$operation .=$this->operation(array("href"=>"ajax.".$this->module.".php?action=send&id=".$fetchRes['id']."","class"=>"btn default blue btn-xs green btn-viewbtn","value"=>'<i class="fa fa-send"></i>&nbsp;Send')).'&nbsp;&nbsp;';
			$operation .= (in_array('edit',$this->Permission))?$this->operation(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$fetchRes['id']."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$fetchRes['id']."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';
			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=view&id=".$fetchRes['id']."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):'';
			$final_array = array($fetchRes["name"], substr(dispContent($fetchRes["description"]),50), date("F j, Y", strtotime($fetchRes["created_date"])));
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

	public function sendForm() {
        $content = $option_module = '';
		$getSelectBoxOption=$this->getSelectBoxOption();
		$fields = array("%VALUE%","%SELECTED%","%DISPLAY_VALUE%");
		$qrySelModule = $this->db->pdoQuery("SELECT * FROM tbl_newsletter_subscriber where is_active=?", array('y'))->results();
		foreach ($qrySelModule as $fetchRes) {
			$fields_replace = array(base64_decode($fetchRes['email']), "", base64_decode($fetchRes['email']));
			$option_module.=str_replace($fields,$fields_replace,$getSelectBoxOption);
		}

        $main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/send_form-nct.tpl.php");
        $main_content = $main_content->parse();
        $fields = array("%SUBSCRIBER%", "%TYPE%", "%ID%", "%NEWS_LTR%");
	    $fields_replace = array($option_module, $this->type, $this->id, $this->name);
	    $content=str_replace($fields,$fields_replace,$main_content);
		return sanitize_output($content);
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