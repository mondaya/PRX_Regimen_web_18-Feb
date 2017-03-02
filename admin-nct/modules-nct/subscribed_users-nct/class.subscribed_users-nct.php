<?php
class SubscribedUsers extends Home {
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
		$this->table = 'tbl_newsletter_subscriber';

		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();
		switch($type){
			case 'delete' : {
				$this->data['content'] =  (in_array('delete',$this->Permission))?json_encode($this->dataGrid()):'';
				break;
			}
			case 'datagrid' : {
				$this->data['content'] =  (in_array('module',$this->Permission))?json_encode($this->dataGrid()):'';
			}
		}
	}

	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		extract($this->searchArray);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );
		$whereCond = '';
		if(isset($chr) && !empty($chr)) {
			$whereCond = " WHERE (u.firstName LIKE '%".$chr."%' OR u.lastName LIKE '%".$chr."%' OR s.email LIKE '%".base64_encode($chr)."%' )";
		}

		if(isset($sort))
			$sorting = $sort.' '. $order;
		else
			$sorting = 'id DESC';

		$qrySel1 =$this->db->pdoQuery("SELECT s.id FROM $this->table as s LEFT JOIN tbl_users as u ON u.email=s.email".$whereCond." ORDER BY ".$sorting." ")->results();
		
		$totalRow = count($qrySel1);
		$qrySel =$this->db->pdoQuery("SELECT s.*, u.firstName, u.lastName,u.member FROM $this->table as s LEFT JOIN tbl_users as u ON u.email=s.email".$whereCond." ORDER BY ".$sorting." LIMIT ".$offset." ,".$rows." ")->results();
		foreach($qrySel as $fetchRes) {
			$status = ($fetchRes['is_active']=="y") ? "checked" : "";
			$switch  =(in_array('status',$this->Permission))?$this->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$fetchRes['id']."","check"=>$status)):'';

			$operation='';
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$fetchRes['id']."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';

			$firstName = (isset($fetchRes["firstName"]) && $fetchRes["firstName"]!='')?$fetchRes["firstName"]:'';
			$lastName = (isset($fetchRes["lastName"]) && $fetchRes["lastName"]!='')?$fetchRes["lastName"]:'';
			$email = (isset($fetchRes["email"])  && $fetchRes["email"]!='')?base64_decode($fetchRes["email"]):'N/A';
			$final_array =  array($firstName, $lastName, $email, time_diff($fetchRes["member"]));
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