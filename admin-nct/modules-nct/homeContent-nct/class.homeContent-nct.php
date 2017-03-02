<?php
class homeContent extends Home {
	function __construct() {
		parent::__construct();
	}
	public function getForm() {
		$content = NULL;
		
		$qrySel = $this->db->select("tbl_home_contant","*")->result();
		$fetchUser = $qrySel;
				
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/form-nct.tpl.php");
		$main_content = $main_content->parse();

		$fields = array("%STEP1TITLE%","%STEP1BRIEF%","%STEP1DESC%","%STEP2TITLE%","%STEP2BRIEF%","%STEP2DESC%","%STEP3TITLE%","%STEP3BRIEF%","%STEP3DESC%","%WHYWLLNESS%","%WHYTITLE%");
		$fields_replace = array($fetchUser['step1Title'],$fetchUser['step1brif'],$fetchUser['step1Desc'],$fetchUser['step2Title'],$fetchUser['step2brif'],$fetchUser['step2Desc'],$fetchUser['step3Title'],$fetchUser['step3brif'],$fetchUser['step3Desc'],$fetchUser['whyWellness'],$fetchUser['whyTitle']);
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