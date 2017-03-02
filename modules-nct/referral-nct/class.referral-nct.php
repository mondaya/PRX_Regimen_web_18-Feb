<?php
class referral {
	
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
		$wallet = $main_content->parse();

		$totalReferredUser = getTotalRows('tbl_referral_users',array('userId'=>$this->sessUserId),'id');
		$totalRegisteredUser = getTotalRows('tbl_referral_users',array('userId'=>$this->sessUserId,'isRegister'=>'y'),'id');
		$totalPurchasedUser = getTotalRows('tbl_referral_users',array('userId'=>$this->sessUserId,'isPurchase'=>'y'),'id');
		$referralAmount = getTableValue('tbl_users',"referralAmount",array('id'=>$this->sessUserId));

		$fields = array(
			"%REFERRAL_URL%","%TOTAL_REFERRED_USER%","%TOTAL_REGISTERED_USER%","%TOTAL_PURCHASED_USER%","%TOTAL_REFERRAL_AMOUNT%","%SITE_CURR%"
		);

		$fields_replace = array(
			SITE_URL.'referral/'.base64_encode($this->sessUserId),$totalReferredUser,$totalRegisteredUser,$totalPurchasedUser,number_format(convertCurrency($this->currencyId,$referralAmount),2),$this->currencySign
		);

		$final_result = str_replace($fields, $fields_replace, $wallet);

		return $final_result;
	}

	
}