<?php
class deposite {
	
	protected $db;
	public $module;
		
	function __construct($module) {
		global $db,$fields,$sessUserId,$currencyId,$currencyCode,$currencySign;
				
		$this->db = $db;
		$this->sessUserId = $sessUserId;
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
		$final_result = $main_content->parse();

		$amount = $this->db->select("tbl_users",array("firstName","lastName","email"),array("id"=>$this->sessUserId))->result();

		$firstName = $amount['firstName'];
		$lastName = $amount['lastName'];
		$email = $amount['email'];

		$fields = array("%FNAME%","%LNAME%","%EMAIL%","%SITE_CURR%");

		$fields_replace = array($firstName,$lastName,base64_decode($email),SITE_CURR);

		$final_result = str_replace($fields, $fields_replace, $final_result);

		return $final_result;
	}

	
}