<?php
class accountContent extends Home {
	function __construct() {
		parent::__construct();
	}
	public function getForm() {
		$content = NULL;
		
		$qrySel = $this->db->select("tbl_myaccount_contant","*")->result();
		$fetchUser = $qrySel;
				
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
		$main_content = $main_content->parse();

		$fields = array("%PROFILE%","%PAYMENT_HISTORY%","%WALLET%","%ORDER%","%CUSTOM_ORDER%","%NEW_CUSTOM_ORDER%","%SETTINGS%","%NOTIFICATIONS%","%FAV_CATE%","%FAV_STORE%","%REFERRAL%","%MY_CART%");
		$fields_replace = array($fetchUser['profile'],$fetchUser['paymentHistory'],$fetchUser['wallet'],$fetchUser['orders'],$fetchUser['customOrder'],$fetchUser['newCustomOrder'],$fetchUser['settings'],$fetchUser['notifications'],$fetchUser['favoriteCate'],$fetchUser['favoriteStore'],$fetchUser['referral'],$fetchUser['cart']);
		$content=str_replace($fields,$fields_replace,$main_content);
		return $content;

	}
	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/".$this->module.".tpl.php");
		$main_content->breadcrumb = $this->getBreadcrumb();
		$main_content->getForm = $this->getForm();
		
		$final_result = $main_content->parse();
		return $final_result;
	}
}