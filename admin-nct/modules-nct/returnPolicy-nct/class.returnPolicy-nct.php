<?php
class returnPolicy extends Home {
	function __construct() {
		parent::__construct();
	}
	public function getForm() {
		$content = NULL;
		
		$qrySel = $this->db->select("tbl_home_contant","*")->result();
		$fetchUser = $qrySel;
				
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
		$main_content = $main_content->parse();

		$fields = array("%RETURN_POLICY%");
		$fields_replace = array($fetchUser['returnPolicy']);
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