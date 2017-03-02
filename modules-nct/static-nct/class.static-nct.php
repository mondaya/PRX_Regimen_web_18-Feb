<?php

class Content {
	function __construct($module,$id) {
		global $db,$fields,$sessUserId,$sessUserType;
		
		$this->db = $db;
		$this->fields = $fields;
		$this->module = $module;
		$this->table = '';
		$this->id = $id;
	}
	public function getStaticPageContent(){
		$final_result = array();

		$qryRes=$this->db->pdoQuery("SELECT * FROM tbl_content WHERE pId='".$this->id."' and isActive='y'")->result();
		$fetchRes = $qryRes;

		$id = (isset($fetchRes['pId']))?$fetchRes['pId']:0;
		$page_name = (isset($fetchRes['pageName']))?$fetchRes['pageName']:'';
		$page_title = (isset($fetchRes['pageTitle']))?$fetchRes['pageTitle']:'';
		$meta_keyword = (isset($fetchRes['metaKeyword']))?$fetchRes['metaKeyword']:'';
		$meta_desc = (isset($fetchRes['metaDesc']))?$fetchRes['metaDesc']:'';
		$page_desc = (isset($fetchRes['pageDesc']))?$fetchRes['pageDesc']:'';

		$final_result = array("id"=>$id,"page_name"=>$page_name,"page_title"=>$page_title,"meta_keyword"=>$meta_keyword,"meta_desc"=>$meta_desc,"page_desc"=>$page_desc);
		return $final_result;
	}

	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();
		return $final_result;
	}
}

?>
