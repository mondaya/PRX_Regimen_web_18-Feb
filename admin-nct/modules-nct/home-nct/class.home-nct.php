<?php
class Home{
	public function __construct() {
		foreach($GLOBALS as $key=>$values){
			$this->$key = $values;
		}
	}
	public function index() {
		$content = NULL;
		return $content;
	}

	public function getLeftMenu() {
		$admSl = $this->db->select("tbl_admin", array("adminType"), array("id ="=>(int)$this->adminUserId))->result();

		$final_result = $sub_final_result = NULL;
		$main_content = new Templater(DIR_ADMIN_TMPL."left_panel-nct.tpl.php");

		$sub_content_menu = new Templater(DIR_ADMIN_TMPL."left_panel_menu-nct.tpl.php");
		$sub_content_menu = $sub_content_menu->parse();

		$sub_content_submenu = new Templater(DIR_ADMIN_TMPL."left_panel_submenu-nct.tpl.php");
		$sub_content_submenu = $sub_content_submenu->parse();

		$sub_content_submenu_item = new Templater(DIR_ADMIN_TMPL."left_panel_submenu_item-nct.tpl.php");
		$sub_content_submenu_item = $sub_content_submenu_item->parse();

		$fields = array("%IMAGE%","%SECTION_NAME%","%SUBMENU_LIST%");
		$fields_submenu = array("%SUBMENU_ITEMS%");
		$fields_submenu_item = array("%PAGE_NAME%","%PAGE_URL%","%TITLE%");

		$qrySel = $this->db->select("tbl_adminsection",array("id","section_name","image"),array("id >"=> 0),"ORDER BY `order` ASC")->results();
			if(!empty($qrySel[0]) > 0) {

				foreach($qrySel as $fetchRes) {
					$sub_final_result_submenu_item=$sub_final_result_submenu=NULL;
					$id =  $fetchRes['id'];
					$qSelMenu =$this->db->select("tbl_adminrole", array('id,title,pagenm'),array("sectionid" => $id,"and status !="=> "d"), "ORDER BY seq ASC")->results();
					//$totalRow = $this->db->count("tbl_adminrole", array("sectionid" => $id,"and status !="=> "d"));
					$qSelMenu_sub = $this->db->select("tbl_adminrole", array('GROUP_CONCAT(id) as id'), array("sectionid" => $id,"and status !="=> "d"))->result();

					if($qSelMenu_sub['id']!=''){
						//echo $qSelMenu_sub['id'];
						//exit;
						$qSelMenu_total= $this->db->pdoQuery("select count(id) as total_records from tbl_admin_permission where admin_id = '".(int)$this->adminUserId."' and page_id in (".$qSelMenu_sub['id'].") and permission!=''")->result();
						$totalRow = $qSelMenu_total['total_records'];
					}

					if(!empty($qSelMenu[0]) > 0) {
						foreach($qSelMenu as $fetchMenu) {
							$chkPermssion = $this->db->select("tbl_admin_permission", array("permission"), array("admin_id"=>(int)$this->adminUserId,"page_id"=>$fetchMenu['id']))->result();
							if((!empty($chkPermssion['permission'])) || $admSl['adminType'] != 'g'){
								$title = $fetchMenu['title'];
								$pagenm = $fetchMenu['pagenm'];
								$fields_replace_submenu_item = array($pagenm,SITE_ADM_MOD.$pagenm,$title);
								$sub_final_result_submenu_item .=str_replace($fields_submenu_item,$fields_replace_submenu_item,$sub_content_submenu_item);
							}
						}
						$fields_replace_submenu = array($sub_final_result_submenu_item);
						$sub_final_result_submenu .= str_replace($fields_submenu,$fields_replace_submenu,$sub_content_submenu);

					}

					 //(!empty($chkPermssion['permission']) ||

					if($totalRow>0 || $admSl['adminType'] != 'g'){
						$fields_replace = array($fetchRes['image'],$fetchRes['section_name'],$sub_final_result_submenu);
						$sub_final_result .=str_replace($fields,$fields_replace,$sub_content_menu);
					}
				}
			}

			$main_content->set('getMenuList',$sub_final_result);
			$final_result = $main_content->parse();

			return $final_result;
	}
	public function SubadminAction(){
		$final_result = array();
		$qryRes= $this->db->pdoQuery("SELECT id,constant,title FROM tbl_subadmin_action")->results();
		foreach($qryRes as $fetchRes){
			$id = (isset($fetchRes['id']))?$fetchRes['id']:0;
			//$constant = (isset($fetchRes['constant']))?$fetchRes['constant']:'';
			$title = (isset($fetchRes['title']))?$fetchRes['title']:'';

			$final_result = $final_result + array($id=>$title);
		}
		return $final_result;
	}
	public function SubadminActionDetails(){
		$final_result = array();
		$qryRes= $this->db->pdoQuery("SELECT id,constant,title FROM tbl_subadmin_action")->results();
		foreach($qryRes as $fetchRes){
			$id = (isset($fetchRes['id']))?$fetchRes['id']:0;
			$constant = (isset($fetchRes['constant']))?$fetchRes['constant']:'';
			$title = (isset($fetchRes['title']))?$fetchRes['title']:'';

			$final_result[] = array("id"=>$id,"constant"=>$constant,"title"=>$title);
		}
		return $final_result;
	}
	public function adminPageList(){
		$final_result = array();
		$qryRes= $this->db->pdoQuery("SELECT id,title,pagenm,page_action FROM tbl_adminrole WHERE status='a'")->results();
		foreach($qryRes as $fetchRes){
			$id = (isset($fetchRes['id']))?$fetchRes['id']:0;
			$title = (isset($fetchRes['title']))?$fetchRes['title']:'';
			$pagenm = (isset($fetchRes['pagenm']))?$fetchRes['pagenm']:'';
			$page_action = (isset($fetchRes['page_action']))?$fetchRes['page_action']:0;
			$page_action_id = array();
			if($page_action!=''){
				$qryRes_sub= $this->db->pdoQuery("SELECT id,title FROM tbl_subadmin_action WHERE id in (".$page_action.")")->results();
				foreach($qryRes_sub as $fetchRes_sub){
					$page_action_id[] = (isset($fetchRes_sub['title']))?$fetchRes_sub['title']:'';
				}
			}
			$final_result[] = array("id"=>$id,"title"=>$title,"pagenm"=>$pagenm,"pagenm"=>$pagenm,"page_action"=>$page_action,"page_action_id"=>$page_action_id);
		}
		return $final_result;
	}
	public function getBreadcrumb(){

		$final_result= $sub_final_result = NULL;
		$main_content=new Templater(DIR_ADMIN_TMPL."breadcrumb-nct.tpl.php");
		$content_list=new Templater(DIR_ADMIN_TMPL."breadcrumb_item-nct.tpl.php");
		$content_list=$content_list->parse();
		$field_array = array("%TITLE%");
		$data = $this->breadcrumb;

		for($i=0;$i<count($data);$i++){
			$replace=array($data[$i]);
			$sub_final_result .= str_replace($field_array,$replace,$content_list);
		}
		$main_content->set("breadcrumb_list",$sub_final_result);
		$final_result = $main_content->parse();
		return $final_result;
	}
	public function getPageContent(){
		$admSl = $this->db->select("tbl_admin", array("adminType"), array("id ="=>(int)$this->adminUserId))->result();
		$final_result = $sub_final_result = NULL;
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/".$this->module.".tpl.php");
		$main_content->breadcrumb = $this->getBreadcrumb();
		$sub_content = new Templater(DIR_ADMIN_TMPL.$this->module."/dashboard_list-nct.tpl.php");
		$sub_content = $sub_content->parse();
		$fields = array('%PAGE_LINK%','%COLOR%','%IMAGE%','%PAGE_TITLE%');

		$qSelMenu = $this->db->select("tbl_adminrole", array('id,title,pagenm,image'),array("status !="=>"d"), "ORDER BY seq ASC")->results();
        if(!empty($qSelMenu[0]) > 0) {
			$i=0;
			$color_array = array("blue","green","red","yellow","dark","purple");
            foreach($qSelMenu as $fetchMenu) {
				$chkPermssion = $this->db->select("tbl_admin_permission", array("permission"), array("admin_id"=>(int)$this->adminUserId,"page_id"=>$fetchMenu['id']))->result();
				if((!empty($chkPermssion['permission'])) || $admSl['adminType'] != 'g'){
					$fields_replace = array(SITE_ADM_MOD.$fetchMenu['pagenm'],$color_array[$i],$fetchMenu['image'],$fetchMenu['title']);
					$sub_final_result .=str_replace($fields,$fields_replace,$sub_content);
					$i=($i==5)?-1:$i;
					$i++;
				}
			}
		}
		$main_content->set('dashboard_list',$sub_final_result);
		$final_result = $main_content->parse();
		return $final_result;
	}
}