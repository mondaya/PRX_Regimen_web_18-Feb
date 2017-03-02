<?php
class dashboard {
	
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

		$content = $this->db->select('tbl_myaccount_contant','*',array('id'=>1))->result();

		$fields = array(
			'%ID%','%USER_NM%',"%PROFILE%","%PAYMENT_HISTORY%","%WALLET%","%ORDER%","%CUSTOM_ORDER%","%NEW_CUSTOM_ORDER%","%SETTINGS%","%NOTIFICATIONS%","%FAV_CATE%","%FAV_STORE%","%REFERRAL%","%MY_CART%"
		);
		$userName = getTableValue('tbl_users','firstName',array('id'=>$this->sessUserId));
		$fields_replace = array(
			$this->sessUserId,$userName,$content['profile'],$content['paymentHistory'],$content['wallet'],$content['orders'],$content['customOrder'],$content['newCustomOrder'],$content['settings'],$content['notifications'],$content['favoriteCate'],$content['favoriteStore'],$content['referral'],$content['cart']
		);

		$final_result = str_replace($fields, $fields_replace, $account);

		return $final_result;
	}

	
}