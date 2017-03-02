<?php
class Coupons extends Home {
	public $data = array();

	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {
		global $db, $fields;
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_coupons';

		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();
		if($this->id>0){
			$qrySel = $this->db->select($this->table, array("*"),array("id"=>$id))->result();
			$this->data['coupon_code'] = $this->coupon_code = $qrySel['coupon_code'];
			$this->data['start_date'] = $this->start_date = $qrySel['start_date'];
			$this->data['end_date'] = $this->end_date = $qrySel['end_date'];
			$this->data['discount'] = $this->discount = $qrySel['discount'];
			$this->data['is_active'] = $this->is_active = $qrySel['is_active'];
			$this->data['created_date'] = $this->created_date = $qrySel['created_date'];
		}else{
			$this->data['coupon_code'] = $this->coupon_code = '';
			$this->data['start_date'] = $this->start_date = '';
			$this->data['end_date'] = $this->end_date = '';
			$this->data['discount'] = $this->discount = '';
			$this->data['is_active'] = $this->is_active = 'n';
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
		$content = $this->displayBox(array("label"=>"Promo code&nbsp;:","value"=>$this->coupon_code)).
		$this->displayBox(array("label"=>"Start date of Promo code&nbsp;:","value"=>date("F j, Y", strtotime($this->start_date)))).
		$this->displayBox(array("label"=>"End date of Promo code&nbsp;:","value"=>date("F j, Y", strtotime($this->end_date)))).
		$this->displayBox(array("label"=>"Discount(%)&nbsp;:","value"=>$this->discount)).
		$this->displayBox(array("label"=>"Status&nbsp;:","value"=>$this->is_active == 'y'?'Active':'Deactive')).
		$this->displayBox(array("label"=>"Created Date&nbsp;:","value"=>date("F j, Y", strtotime($this->created_date))));
		return $content;
	}

	public function getForm() {
        $content = '';
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
		$main_content = $main_content->parse();
		$static_a=($this->is_active == 'y' ? 'checked':'');
		$static_d=($this->is_active != 'y' ? 'checked':'');
		$fields = array("%COUPON_CODE%", "%START_DATE%", "%END_DATE%", "%DISCOUNT%", "%STATUS_A%", "%STATUS_D%", "%TYPE%", "%ID%");
		$fields_replace = array($this->coupon_code, $this->start_date, $this->end_date, $this->discount, $static_a, $static_d, $this->type, $this->id);
		$content=str_replace($fields,$fields_replace,$main_content);
		return sanitize_output($content);
	}

	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		extract($this->searchArray);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );
		$whereCond = '';
		if(isset($chr) && !empty($chr)) { $whereCond = " WHERE (coupon_code LIKE '%".$chr."%')"; }

		if(isset($sort)) $sorting = $sort.' '. $order;
		else $sorting = 'id DESC';

		$qrySel1 =$this->db->pdoQuery("SELECT * FROM $this->table".$whereCond." ORDER BY ".$sorting."")->results();
		
		$qrySel =$this->db->pdoQuery("SELECT * FROM $this->table".$whereCond." ORDER BY ".$sorting." LIMIT ".$offset." ,".$rows." ")->results();
		
		$totalRow = count($qrySel1);
		foreach($qrySel as $fetchRes) {
			$id = $fetchRes['id'];
			$status = ($fetchRes['is_active']=="y") ? "checked" : "";
			$switch  =(in_array('status',$this->Permission))?$this->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$fetchRes['id']."","check"=>$status)):'';

			$operation='';
			$operation .= (in_array('edit',$this->Permission))?$this->operation(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$id."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$id."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';
			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=view&id=".$id."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):'';
			$final_array =  array($fetchRes['coupon_code'], date("F j, Y", strtotime($fetchRes['start_date'])), date("F j, Y", strtotime($fetchRes['end_date'])), $fetchRes['discount']);
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