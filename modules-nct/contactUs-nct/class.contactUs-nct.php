<?php
class contactUs extends home{
	
	protected $db;
	public $module;
		
	function __construct($module) {
		global $db,$fields,$sessUserId,$sessUserName,$currencyId,$currencyCode,$currencySign;
				
		$this->db = $db;
		$this->sessUserId = $sessUserId;
		$this->sessUserName = $sessUserName;
		$this->currencyId = $currencyId;
		$this->currencyCode = $currencyCode;
		$this->currencySign = $currencySign;
		$this->fields = $fields;
		$this->table = 'tbl_product_deals';
		$this->module=$module;
		
	}

	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$account = $main_content->parse();

		$users = $this->db->select('tbl_users','*',array('id'=>$this->sessUserId))->result();
		
		$country = $this->getCountry($users['countryId']);
		$states = $this->getStates($users['countryId'],$users['stateId']);
		$city = $this->getCities($users['stateId'],$users['cityId']);

		$fields = array(
			'%COUNTRY%','%STATE%','%CITY%','%FNAME%','%LNAME%','%EMAIL%'
		);

		$fields_replace = array(
			$country,$states['states'],$city['cities'],$users['firstName'],$users['lastName'],base64_decode($users['email'])
		);

		$final_result = str_replace($fields, $fields_replace, $account);

		return $final_result;
	}

	
}