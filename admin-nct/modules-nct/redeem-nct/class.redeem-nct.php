<?php
class redeem extends Home {
	
	public $status;
	public $data = array();
	
	public function __construct($module, $id=0, $objPost=NULL, $searchArray=array(), $type='') {		
		global $db, $fields, $sessCataId;		
		$this->db = $db;
		$this->data['id'] = $this->id = $id;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = 'tbl_return_request';	
	
		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();		
		if($this->id>0){
			$qrySel = $this->db->pdoQuery("SELECT u.firstName,u.paypalEmail,u.email,r.amount,r.createdDate,r.status,ct.cityName,co.countryName,s.stateName FROM tbl_redeem_request as r 
				  LEFT JOIN tbl_users as u ON(r.userId = u.id)
				  LEFT JOIN tbl_city as ct ON(u.cityId = ct.id)
				  LEFT JOIN tbl_state as s ON(u.stateId = s.id)
				  LEFT JOIN tbl_country as co ON(u.countryId = co.id)
				  where r.id = ".$this->id."")->result();
			
			$fetchRes = $qrySel;

			$this->data['userName'] = $this->userName = $fetchRes["firstName"];
			$this->data['email'] = $this->email = base64_decode($fetchRes["email"]);
			$this->data['location'] = $this->location = $fetchRes["cityName"].','.$fetchRes["stateName"].','.$fetchRes["countryName"];
			$this->data['createdDate'] = $this->createdDate = date('Y-m-d',strtotime($fetchRes["createdDate"]));
			$this->data['redeemAmount'] = $this->redeemAmount = SITE_CURR.'&nbsp'.$fetchRes["amount"];
			$this->data['paypalEmail'] = $this->paypalEmail = base64_decode($fetchRes["paypalEmail"]);
			if($fetchRes["status"] == 'p'){
				$status = 'Pending';
			}else if($fetchRes["status"] == 'f'){
				$status = 'Funded';
			}else if($fetchRes["status"] == 'r'){
				$status = 'Rejected';
			}
			$this->data['status'] = $this->status = $status;

		}else{
			//$this->data['blogName'] = $this->blogName = '';
			
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
			case 'viewdocument' : {
				$this->data['content'] =  (in_array('add',$this->Permission))?$this->viewdocumentForm():'';
				break;
			}
			case 'datagrid' : {
				$this->data['content'] =  json_encode($this->dataGrid());
			}
			
		}

	}
	public function viewForm(){		
		$content = $this->displayBox(array("label"=>"User Name&nbsp;:","value"=>$this->userName)).
		$this->displayBox(array("label"=>"User email&nbsp;:","value"=>$this->email)).
		$this->displayBox(array("label"=>"User paypal&nbsp;:","value"=>$this->paypalEmail)).
		$this->displayBox(array("label"=>"User location&nbsp;:","value"=>$this->location)).
		$this->displayBox(array("label"=>"Redeem amount&nbsp;:","value"=>$this->redeemAmount)).
		$this->displayBox(array("label"=>"Redeem Status&nbsp;:","value"=>$this->status));
		return $content;
	}

	public function viewdocumentForm(){
		$content = '';

			$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/message-nct.tpl.php");
			$main_content = $main_content->parse();
			echo $PageTitle;
			$fields = array("%ID%");
	
			$fields_replace = array($this->id);
	
			$content=str_replace($fields,$fields_replace,$main_content);
		return sanitize_output($content);
	}
	
	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		//print_r($this->searchArray);exit;
		extract($this->searchArray);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );
		
		$aWhere = array('1');
		$sWhere = ' WHERE 1 = ? ';
		if(isset($chr) && $chr != '') {
			$sWhere .= " AND u.firstName LIKE ? ";
			$aWhere[] = "%$chr%";
		}

		if($date_range !='~' && $date_range !=''){
			$date_range=explode("~",$date_range);
			//print_r($date_range);exit;
			if(!empty($date_range[0]) && !empty($date_range[1])) {
				$date_from=date("Y-m-d",strtotime($date_range[0]));
				$date_to=date("Y-m-d",strtotime($date_range[1]));
				$_SESSION["data_from"] =$data_from;
				$_SESSION["data_to"] =$data_to;
				$whereCond .= " AND (r.createdDate BETWEEN ? AND ?)";
				$aWhere[] = "$date_from";
				$aWhere[] = "$date_to";
			}
		}
		

		if(isset($sort)){
		//$sorting = (in_array($sort,array('stateName')) ? 's.' : 'c.').$sort.' '. $order;
		$alias = '';
		if($sort == 'userName'){
			$alias = 'u.';
		}else if($sort == 'amount'){
			$alias = 'r.';
		}else if($sort == 'createdDate'){
			$alias = 'r.';
		}
		$sorting = $alias.$sort.' '. $order;

		}else{
			 $sorting = 'p.id DESC';
		}

		$qrySel1 = $this->db->pdoQuery("SELECT u.id FROM tbl_redeem_request as r 
				  LEFT JOIN tbl_users as u ON(r.userId = u.id)
		 $sWhere ".$whereCond." ORDER BY $sorting", $aWhere)->results();
			
		$qrySel = $this->db->pdoQuery("SELECT u.firstName,r.id,r.amount,r.createdDate,r.status FROM tbl_redeem_request as r 
				  LEFT JOIN tbl_users as u ON(r.userId = u.id)
		 $sWhere ".$whereCond." ORDER BY $sorting limit $offset , $rows", $aWhere)->results();
		
		$totalRow = count($qrySel1);
		foreach($qrySel as $fetchRes) {
			$id =  $fetchRes['id'];
			$operation='';
			
			$redeemAmount = $fetchRes["amount"];
			
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$id."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';			
			
			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=view&id=".$id."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):'';
			
			if($fetchRes['status'] == 'p'){
				$operation .= (in_array('pay',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>SITE_URL.'admin-nct/modules-nct/redeem-nct/index.php?action=pay&id='.$id.'',"class"=>"btn default blue btn-xs","value"=>'&nbsp;Pay')):'';
				$operation .= (in_array('reject',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>SITE_URL.'admin-nct/modules-nct/redeem-nct/index.php?action=reject&id='.$id.'',"class"=>"btn default blue btn-xs","value"=>'&nbsp;Reject')):'';
			}

			$operation .=(in_array('add',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=viewdocument&id=".$fetchRes['id']."","class"=>"btn default btn-xs btn-viewbtn","extraAtt"=>"data-page_title='Send Message'","value"=>'<i class="fa fa-laptop"></i>&nbsp;Reply')):'';

			//$status = $fetchRes['status']=='p'?'Pending':'Funded';
			if($fetchRes["status"] == 'p'){
				$status = 'Pending';
			}else if($fetchRes["status"] == 'f'){
				$status = 'Funded';
			}else if($fetchRes["status"] == 'r'){
				$status = 'Rejected';
			}

			$final_array =  array($fetchRes["firstName"],SITE_CURR.' '.number_format($redeemAmount,2),date("Y-m-d",strtotime($fetchRes["createdDate"])),$status);
			
			if(in_array('pay',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) || in_array('status',$this->Permission)){ 		
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