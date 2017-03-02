<?php
class pick extends Home{

	public $status;
	public $data = array();

	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {
		global $db, $fields, $sessCataId;
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_pick_points';

		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();
		if($this->id>0){
			$qrySel = $this->db->pdoQuery("SELECT pp.*,s.stateName,co.countryName FROM tbl_pick_points AS pp
			LEFT JOIN tbl_state AS s ON pp.stateId = s.id
			LEFT JOIN tbl_country AS co ON pp.countryId = co.id where pp.id = ".$this->id."")->result();
			$fetchRes = $qrySel;
			$this->data['id'] = $this->id = $fetchRes['id'];
			$this->data['stateId'] = $this->stateId = $fetchRes['stateId'];
			$this->data['countryId'] = $this->countryId = $fetchRes['countryId'];
			$this->data['countryName'] = $this->countryName = $fetchRes['countryName'];
			$this->data['stateName'] = $this->stateName = $fetchRes['stateName'];
			$this->data['pointName'] = $this->pointName = $fetchRes['pointName'];
			$this->data['pointAddress'] = $this->pointAddress = $fetchRes['pointAddress'];
			$this->data['isActive'] = $this->isActive = $fetchRes['isActive'];
		}else{
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

		$content='';
		$getSelectBoxOption=$this->getSelectBoxOption();
		$fields = array("%VALUE%","%SELECTED%","%DISPLAY_VALUE%");

		//country drop down
		$qrySelCountry=$this->db->pdoQuery("SELECT * FROM tbl_country where isActive='y' ORDER BY countryName")->results();
		foreach ($qrySelCountry as $fetchRes) {
			$selected = ($this->countryId==$fetchRes['id'])?"selected":"";
			$fields_replace = array($fetchRes['id'],$selected,$fetchRes['countryName']);
			$country_option.=str_replace($fields,$fields_replace,$getSelectBoxOption);
		}

		//For state select box
		if(!empty($this->stateId) && !empty($this->countryId)) {
			$qrySelCountry=$this->db->pdoQuery("SELECT * FROM tbl_state WHERE isActive=? AND countryId=? ORDER BY stateName ASC", array('y', $this->countryId))->results();
			foreach ($qrySelCountry as $fetchRes) {
				$selected = ($this->stateId==$fetchRes['id'])?"selected":"";
				$fields_replace = array($fetchRes['id'],$selected,$fetchRes['stateName']);
				$state_option.=str_replace($fields,$fields_replace,$getSelectBoxOption);
			}
		} else {
			$fields_replace = array('','','');
			$state_option.=str_replace($fields,$fields_replace,$getSelectBoxOption);
		}

		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
			$main_content = $main_content->parse();
			$status_a=($this->isActive == 'y' ? 'checked':'');
			$status_d=($this->isActive != 'y' ? 'checked':'');

			$fields = array("%MEND_SIGN%","%COUNTRY_OPTION%","%STATE_OPTION%","%POINT_NAME%","%POINT_ADD%","%STATUS_A%","%STATUS_D%","%TYPE%","%ID%");

			$fields_replace = array(MEND_SIGN,$country_option,$state_option,$this->data['pointName'],$this->data['pointAddress'],$status_a,$status_d,$this->type,$this->id);

			$content=str_replace($fields,$fields_replace,$main_content);
		return sanitize_output($content);

	}

	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		extract($this->searchArray);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );

		$aWhere = array(1);
		$sWhere = ' WHERE 1 = ? ';
		if(isset($chr) && $chr != '') {
			$sWhere .= " AND (s.stateName LIKE ? OR co.countryName LIKE ?)";
			$aWhere[] = "%$chr%";
			$aWhere[] = "%$chr%";
			//$aWhere[] = "%$chr%";
		}
		if(isset($sort)){
			//$sorting = (in_array($sort,array('stateName')) ? 's.' : 'c.').$sort.' '. $order;
			$alias = '';
			if($sort == 'countryName'){
				$alias = 'co.';
			}else if($sort == 'stateName'){
				$alias = 's.';
			}else if($sort == 'pointName' || $sort == 'pointAddress'){
				$alias = 'pp.';
			}
			$sorting = $alias.$sort.' '. $order;

		}else{
			 $sorting = 'da.id DESC';
		}

		$qrySel1 = $this->db->pdoQuery("SELECT pp.id FROM tbl_pick_points AS pp
			LEFT JOIN tbl_state AS s ON pp.stateId = s.id
			LEFT JOIN tbl_country AS co ON pp.countryId = co.id
			$sWhere ORDER BY $sorting", $aWhere)->results();

		$qrySel = $this->db->pdoQuery("SELECT pp.*,s.stateName,co.countryName FROM tbl_pick_points AS pp
			LEFT JOIN tbl_state AS s ON pp.stateId = s.id
			LEFT JOIN tbl_country AS co ON pp.countryId = co.id
			$sWhere ORDER BY $sorting limit $offset , $rows", $aWhere)->results();

		$totalRow = count($qrySel1);
		foreach($qrySel as $fetchRes) {
			$id =  $fetchRes['id'];
			$status = $fetchRes['isActive'];

			$status = ($fetchRes['isActive']=="y") ? "checked" : "";

			$switch  =(in_array('status',$this->Permission))?$this->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$id."","check"=>$status)):'';
			$operation='';

			$operation .= (in_array('edit',$this->Permission))?$this->operation(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$id."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$id."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';

			$final_array =  array($fetchRes["countryName"],$fetchRes["stateName"],$fetchRes["pointName"],$fetchRes["pointAddress"]);
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
	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/".$this->module.".tpl.php");
		$main_content->breadcrumb = $this->getBreadcrumb();
		$final_result = $main_content->parse();
		return $final_result;
	}
}