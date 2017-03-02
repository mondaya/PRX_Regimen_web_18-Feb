<?php
class wallet {
	
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

		$amount = $this->db->select("tbl_users",array("creditAmount","pendingAmount","redeemAmount"),array("id"=>$this->sessUserId))->result();

		$creditAmount = convertCurrency($this->currencyId,$amount['creditAmount']);
		$redeemAmount = convertCurrency($this->currencyId,$amount['redeemAmount']);
		$pendingAmount = convertCurrency($this->currencyId,$amount['pendingAmount']);

		$fields = array("%CREDIT%","%PENDING%","%REDEEM%","%CURRENCY_SIGN%","%SITE_NM%");

		$fields_replace = array(number_format($creditAmount,2),number_format($pendingAmount,2),number_format($redeemAmount,2),$this->currencySign,SITE_NM);

		$final_result = str_replace($fields, $fields_replace, $wallet);

		return $final_result;
	}

	
}