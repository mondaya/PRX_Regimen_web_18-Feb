<?php
class ContactUs extends Home {
	public $data = array();

	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {
		global $db, $fields;
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_contactus';

		$this->type = ($this->id > 0 ? 'reply' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();
		if($this->id>0){
			$qrySel = $this->db->select($this->table, array("*"),array("id"=>$id))->result();
			$this->data['first_name'] = $this->first_name = $qrySel['first_name'];
			$this->data['last_name'] = $this->last_name = $qrySel['last_name'];
			$this->data['email'] = $this->email = $qrySel['email'];
			$this->data['subject'] = $this->subject = $qrySel['subject'];
			$this->data['message'] = $this->message = $qrySel['message'];
		}else{
			$this->data['first_name'] = $this->first_name = "";
			$this->data['last_name'] = $this->last_name = "";
			$this->data['email'] = $this->email = "";
			$this->data['subject'] = $this->subject = "";
			$this->data['message'] = $this->message = '';
		}
		switch($type){
			case 'reply' : {
				$this->data['content'] =  $this->getForm();
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
        $content = '';
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
		$main_content = $main_content->parse();
		$fields = array("%USER_NAME%", "%EMAIL%", "%SUBJECT%", "%MESSAGE%", "%TYPE%", "%ID%", );
		$fields_replace = array($this->first_name.' '.$this->last_name, base64_decode($this->email), $this->subject, $this->message, $this->type, $this->id);
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
			$sWhere .= " AND (cu.first_name LIKE ? OR cu.last_name LIKE ? OR cu.email LIKE ?)";
			$aWhere[] = "%$chr%"; $aWhere[] = "%$chr%"; $aWhere[] = "%$chr%";
		}

		if(isset($sort)) $sorting = $sort.' '. $order;
		else $sorting = 'id DESC';

		$qrySel1 =$this->db->pdoQuery("SELECT cu.id FROM $this->table as cu LEFT JOIN tbl_country as c ON c.id=cu.country_id LEFT JOIN tbl_state as s ON s.id=cu.state_id LEFT JOIN tbl_city as ct ON ct.id=cu.city_id".$sWhere." ORDER BY ".$sorting."", $aWhere)->results();
		
		$qrySel =$this->db->pdoQuery("SELECT cu.*, c.countryName, s.stateName, ct.cityName FROM $this->table as cu LEFT JOIN tbl_country as c ON c.id=cu.country_id LEFT JOIN tbl_state as s ON s.id=cu.state_id LEFT JOIN tbl_city as ct ON ct.id=cu.city_id".$sWhere." ORDER BY ".$sorting." LIMIT ".$offset." ,".$rows." ", $aWhere)->results();
		
		$totalRow = count($qrySel1);
		foreach($qrySel as $fetchRes) {
			$id = $fetchRes['id'];
			$operation='';
			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=reply&id=".$id."","class"=>"btn default blue btn-xs btn-sendbtn","extraAtt"=>"data-page_title='".$fetchRes['first_name']."'" ,"value"=>'<i class="fa fa-send"></i>&nbsp;Reply')):'';
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$id."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';

			$final_array =  array($fetchRes['first_name'].' '.$fetchRes['last_name'], base64_decode($fetchRes['email']), $fetchRes['cityName'].', '.$fetchRes['stateName'].', '.$fetchRes['countryName'], myTruncate($fetchRes['subject'], 100), myTruncate($fetchRes['message'], 100));
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