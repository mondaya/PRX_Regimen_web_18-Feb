<?php
class Users extends Home {
	
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
		$this->table = 'tbl_users';	
	
		$this->type = ($this->id > 0 ? 'edit' : 'add');
		$this->searchArray = $searchArray;
		parent::__construct();	
		if($this->id>0){
			$qrySel = $this->db->pdoQuery("SELECT tbl_users.*,tbl_country.countryName,tbl_state.stateName,tbl_city.cityName FROM tbl_users  	
													LEFT JOIN tbl_country  on tbl_country.id=tbl_users.countryId
													LEFT JOIN tbl_state  on tbl_state.id=tbl_users.stateId
													LEFT JOIN tbl_city  on tbl_city.id=tbl_users.cityId
													WHERE tbl_users.id=?",array($this->id))->result();
			//$qrySel = $this->db->select($this->table, array("id","firstName","email","paypal_id","country","status"),array("id"=>$id))->result();
			
			$fetchRes = $qrySel;
			$this->data['firstName'] = $this->firstName = $fetchRes['firstName'];
			$this->data['lastName'] = $this->lastName = $fetchRes['lastName'];
			$this->data['profileImage'] = $this->profileImage = checkImage('profile/'.$this->id.'/'.$fetchRes['profileImage']);
			$this->data['email'] = $this->email = base64_decode($fetchRes['email']);
			$this->data['paypalEmail'] = $this->paypalEmail = base64_decode($fetchRes['paypalEmail']); 				
			$this->data['stateName'] = $this->stateName = $fetchRes['stateName'];		
			$this->data['cityName'] = $this->cityName = $fetchRes['cityName'];
			$this->data['countryName'] = $this->countryName = $fetchRes['countryName'];
			$straddress =trim($fetchRes['address']);
			$straddress = $straddress==''?' ':$straddress.', ';
			$straddress.=$fetchRes['zipCode']==0?' ':$fetchRes['zipCode'];
			$this->data['address'] = $this->address = $straddress; //$fetchRes['address'].','.$fetchRes['zipCode'];
			$gender =$fetchRes['gender']=='m'?'Male':' ';
			$gender =$fetchRes['gender']=='f'?'Female':$gender;
			$this->data['gender'] = $this->gender = $gender;//$fetchRes['gender']=='m'?'Male':'Female';
			$this->data['createdDate'] = $this->createdDate = date('Y-m-d',strtotime($fetchRes['createdDate']));
			$this->data['mobileNumber'] = $this->mobileNumber = $fetchRes['mobileNumber'];
			$this->data['birthDate'] = $this->birthDate = $fetchRes['birthDate'];
			$this->data['isActive'] = $this->isActive = $fetchRes['isActive'];
			$this->data['buyStatus'] = $this->buyStatus = $fetchRes['buyStatus'];
			$this->data['creditAmount'] = $this->creditAmount = $fetchRes['creditAmount'];
			
		}else{
			$this->data['firstName'] = $this->firstName = '';
			$this->data['lastName'] = $this->lastName = '';			
			$this->data['email'] = $this->email = '';			
			$this->data['isActive'] = $this->isActive = 'n';
			$this->data['buyStatus'] = $this->buyStatus = 'n';	
			
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
			case 'viewdocument' : {
				$this->data['content'] =  (in_array('edit',$this->Permission))?$this->viewdocumentForm():'';
				break;
			}			
			case 'delete' : {
				$this->data['content'] =  (in_array('delete',$this->Permission))?json_encode($this->dataGrid()):'';
				break;
			}
			case 'datagrid' : {
				$this->data['content'] =  (in_array('module',$this->Permission))?json_encode($this->dataGrid()):'';
			}
		}

	}
	public function viewdocumentForm(){
		$qrySel = $this->db->pdoQuery("Select skillName,document,licence from tbl_skills where userId =".$this->id."");
		$result = $qrySel->results();
		//echo '<pre>';
		//print_r($result);exit;
		
		
		foreach($result as $fetchRes) {
			$content .= $this->displayBox(array("label"=>"Skill Name&nbsp;:","value"=>$fetchRes["skillName"]));	
			$image ="<a href='".USER_DOC.$this->id."/".$fetchRes["document"]."' download=".$fetchRes['document'].">".$fetchRes['document']."</a>";
			$licence_image ="<a href='".USER_DOC.$this->id."/".$fetchRes["licence"]."' download=".$fetchRes['licence'].">".$fetchRes['licence']."</a>";
			$content .= $this->displayBox(array("label"=>"Certified document:", "value"=>$image)).
			$this->displayBox(array("label"=>"License document:", "value"=>$licence_image));
		}
		return $content;
		
	}
	public function viewForm(){
		
		$image ="<img src='".$this->profileImage."' height='100' width='100'>";
		$content = $this->displayBox(array("label"=>"First Name&nbsp;:","value"=>$this->firstName)).
		$content = $this->displayBox(array("label"=>"Last Name&nbsp;:","value"=>$this->lastName)).
		$content = $this->displayBox(array("label"=>"Email&nbsp;:","value"=>$this->email)).
		$content = $this->displayBox(array("label"=>"Profile image&nbsp;:","value"=>$image)).
		$content = $this->displayBox(array("label"=>"Country&nbsp;:","value"=>$this->countryName)).
		$content = $this->displayBox(array("label"=>"State&nbsp;:","value"=>$this->stateName)).
		$content = $this->displayBox(array("label"=>"City&nbsp;:","value"=>$this->cityName)).
		$content = $this->displayBox(array("label"=>"Address&nbsp;:","value"=>$this->address)).
		$content = $this->displayBox(array("label"=>"Gender&nbsp;:","value"=>$this->gender)).
		$content = $this->displayBox(array("label"=>"Mobile no.&nbsp;:","value"=>$this->mobileNumber)).
		$content = $this->displayBox(array("label"=>"Birth date&nbsp;:","value"=>$this->birthDate)).
		$content = $this->displayBox(array("label"=>"Member since&nbsp;:","value"=>$this->createdDate)).
		$content = $this->displayBox(array("label"=>"Paypal email&nbsp;:","value"=>$this->paypalEmail)).
		$content = $this->displayBox(array("label"=>"Credit amount&nbsp;:","value"=>SITE_CURR.'&nbsp;'.number_format($this->creditAmount,2)));
		
		return $content;
	}	
	public function getForm() {
		$content = '';

			$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
			$main_content = $main_content->parse();
			$static_a=($this->isActive == 'y' ? 'checked':'');
			$static_d=($this->isActive != 'y' ? 'checked':'');
			
			$disable_removeAmount = '';
			if($this->data['creditAmount'] == 0){
				$disable_removeAmount = 'disabled';
			}

			$fields = array("%FIRSTNAME%","%STATUS_A%","%STATUS_D%","%TYPE%","%ID%","%SITE_CURR%","%CREDIT_AMOUNT%","%DISABLED_REMOVEAMOUNT%");
	
			$fields_replace = array($this->data['firstName'],$static_a,$static_d,$this->type,$this->id,SITE_CURR,$this->data['creditAmount'],$disable_removeAmount);
	
			$content=str_replace($fields,$fields_replace,$main_content);
		return sanitize_output($content);
	}
	
	public function dataGrid() {
		$content = $operation = $whereCond = $totalRow = NULL;
		$result = $tmp_rows = $row_data = array();
		//print_r($this->searchArray);exit;
		extract($this->searchArray);
		$chr = str_replace(array('_', '%'), array('\_', '\%'),$chr );
		$whereCond = '';
		if(isset($chr) && $chr != '') {
			//$whereCond = array("firstName LIKE"=> "%$chr%");
			$whereCond = " where (firstName LIKE '%".$chr."%' OR lastName LIKE '%".$chr."%' OR email	LIKE '%".base64_encode($chr)."%' )";
		}

		if(isset($country) && $country != '') {
			$whereCond .= (empty($whereCond)?' WHERE ':' AND ')."(countryId = ".$country.")";
		}

		if(isset($state) && $state != '') {
			$whereCond .= (empty($whereCond)?' WHERE ':' AND ')."(stateId = ".$state.")";
		}

		if(isset($city) && $city != '') {
			$whereCond .= (empty($whereCond)?' WHERE ':' AND ')."(cityId = ".$city.")";
		}		

		if(isset($sort))
			$sorting = $sort.' '. $order;
		else
			$sorting = 'id DESC';

		$qrySel1 =$this->db->pdoQuery("SELECT id FROM tbl_users		
										".$whereCond." order by ".$sorting."")->results();			
		
		$qrySel =$this->db->pdoQuery("SELECT id,firstName,lastName,email,isActive,buyStatus,creditAmount FROM tbl_users		
										".$whereCond." order by ".$sorting." limit ".$offset." ,".$rows." ")->results();
		$totalRow = count($qrySel1);
		foreach($qrySel as $fetchRes) {
			$status = ($fetchRes['isActive']=="y") ? "checked" : "";
			$buyStatus = ($fetchRes['buyStatus']=="y") ? "checked" : "";		
			
			$switch  =(in_array('status',$this->Permission))?$this->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$fetchRes['id']."","check"=>$status)):'';

			$buySwitch  =(in_array('status',$this->Permission))?$this->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$fetchRes['id']."&action=updateBuyStatus","check"=>$buyStatus,"switch_action"=>"updateBuyStatus")):'';			
			
			$operation='';
			
			$operation .= (in_array('edit',$this->Permission))?$this->operation(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$fetchRes['id']."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';
			$operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$fetchRes['id']."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';
			$operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->operation(array("href"=>"ajax.".$this->module.".php?action=view&id=".$fetchRes['id']."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):'';
             

			$firstName = (isset($fetchRes["firstName"]) && $fetchRes["firstName"]!='')?$fetchRes["firstName"]:'';
			$lastName = (isset($fetchRes["lastName"]) && $fetchRes["lastName"]!='')?$fetchRes["lastName"]:'';
			$email = (isset($fetchRes["email"])  && $fetchRes["email"]!='')?base64_decode($fetchRes["email"]):'N/A';
			

			$final_array =  array($firstName,$lastName,$email,SITE_CURR.'&nbsp;'.number_format($fetchRes['creditAmount'],2));
			if(in_array('status',$this->Permission)){
				$final_array =  array_merge($final_array, array($switch));
			}
			$final_array =  array_merge($final_array, array($buySwitch));
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