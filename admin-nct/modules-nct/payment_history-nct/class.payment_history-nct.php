<?php
class PaymentHistory extends Home {
	public $data = array();

	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {
		global $db, $fields, $sessCataId;
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_payment_history';
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
		$whereCond = ' WHERE 1="1"'; $aWhere = array();
		if(isset($chr) && !empty($chr)) {
			$whereCond = " AND (u.firstName LIKE ? OR u.lastName LIKE ? OR u.email LIKE ?)";
			$aWhere[] = "%$chr%"; $aWhere[] = "%$chr%"; $aWhere[] = "%".base64_encode($chr)."%";
		}

		if(!empty($date_range) && $date_range!='~'){
			$date_range =explode("~",$date_range);
			if(!empty($date_range[0]) && !empty($date_range[1])) {
				$date_from = date("Y-m-d H:i:s", strtotime($date_range[0]));
				$date_to = date("Y-m-d  23:59:59", strtotime($date_range[1]));
				$whereCond .= " AND p.created_date >= ?  AND p.created_date <= ?";
				$aWhere[] = "$date_from"; $aWhere[] = "$date_to";
			}
		}

		if(isset($sort)) $sorting = $sort.' '. $order;
		else $sorting = 'id DESC';

		$qrySel1 =$this->db->pdoQuery("SELECT p.id FROM $this->table as p INNER JOIN tbl_users as u ON u.id=p.user_id".$whereCond." ORDER BY ".$sorting."", $aWhere)->results();
		
		$totalRow = count($qrySel1);
		$qrySel =$this->db->pdoQuery("SELECT p.*, u.firstName, u.lastName, u.email FROM $this->table as p INNER JOIN tbl_users as u ON u.id=p.user_id".$whereCond." ORDER BY ".$sorting." LIMIT ".$offset." ,".$rows." ", $aWhere)->results();


		foreach($qrySel as $fetchRes) {
			$operation =(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$fetchRes['id']."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';
			$final_array =  array($fetchRes["firstName"].' '.$fetchRes["lastName"], base64_decode($fetchRes["email"]), (SITE_CURR.number_format(($fetchRes["paid_amount"]), 2)), date('m-d-Y', strtotime($fetchRes["created_date"])), $fetchRes["transaction_id"], (($fetchRes["payment_gateway"]=='p')?"Paypal":(($fetchRes["payment_gateway"]=='pg')?"Paga":(($fetchRes["payment_gateway"]=='vi')?"Visa":(($fetchRes["payment_gateway"]=='w')?"Wallet":"Verve")))));
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