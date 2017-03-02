<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	require("class.manage_subadmin-nct.php");
	
	$module = "manage_subadmin-nct";
	$table = "tbl_admin";
	
	$styles = array(array("data-tables/DT_bootstrap.css",SITE_ADM_PLUGIN),
					array("bootstrap-switch/css/bootstrap-switch.min.css",SITE_ADM_PLUGIN));
	
	$scripts= array("core/datatable.js",
					array("data-tables/jquery.dataTables.js",SITE_ADM_PLUGIN),
					array("data-tables/DT_bootstrap.js",SITE_ADM_PLUGIN),
					array("bootstrap-switch/js/bootstrap-switch.min.js",SITE_ADM_PLUGIN));
	
	chkPermission($module);
	$Permission=chkModulePermission($module);
	/*print_r($Permission); exit;*/
	
	$metaTag = getMetaTags(array("description"=>"Admin Panel",
			"keywords"=>'Admin Panel',
			'author'=>AUTHOR));
	
	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;	
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';	
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;	
	
	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Subadmin';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);		
	
	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->id = isset($id) ? $id : '';
		$objPost->uname = isset($txt_uname) ? $txt_uname : '';
		$objPost->email = isset($txt_email) ? $txt_email : '';
		$objPost->password = isset($txt_password) ? $txt_password : '';
		$objPost->status = isset($status) ? $status : '';
		$objPost->updated_date = date('Y-m-d H:i:s');
		
		if($objPost->uname != "" && $objPost->email != "" ){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){ 	
					$valArray = array("permission"=>"");
					$db->update("tbl_admin_permission",$valArray, array("admin_id"=>$id));
					
					$valArray = array("uName"=>$objPost->uname,"uEmail"=>$objPost->email,"ipAddress"=>get_ip_address(),"updated_date"=>$objPost->updated_date,"status"=>$objPost->status);
					$valArray +=($objPost->password!='')?array("uPass"=>md5($objPost->password)):array();
					
					$db->update($table,$valArray, array("id"=>$id));
					foreach($actions as $key=>$value){
							$objPost1 = new stdClass();
							$objPost1->admin_id=$id;
							$objPost1->page_id=getTableValue("tbl_adminrole","id",array("pagenm"=>$key));
							
							$objPost1->permission=implode(',',$value);
							$objPost1->created_date = date('Y-m-d H:i:s');
							$objPost1->updated_date = date('Y-m-d H:i:s');
							
							$total_records=getTotalRows('tbl_admin_permission',"admin_id='".$objPost1->admin_id."' and page_id='".$objPost1->page_id."'",'id');
							if($total_records>0){
								$valArray = array("permission"=>$objPost1->permission,"updated_date"=>$objPost1->updated_date);
								$db->update("tbl_admin_permission", $valArray, array("admin_id"=>$objPost1->admin_id,"page_id"=>$objPost1->page_id));
							}else{
								$valArray = array("admin_id"=>$objPost1->admin_id,"page_id"=>$objPost1->page_id,"permission"=>$objPost1->permission,"created_date"=>$objPost1->created_date,"updated_date"=>$objPost1->updated_date);
								$db->insert("tbl_admin_permission", $valArray);
							}
						}
					
					$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
					add_admin_activity($activity_array);
	
					$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				}else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
				}
			} else {
				if(in_array('add',$Permission)){
					 
					
						$objPost->adminType = 'g';
						$objPost->ip_address = get_ip_address();
						$objPost->created_date = date('Y-m-d H:i:s');
						$objPost->updated_date = date('Y-m-d H:i:s');
						
						$valArray = array("uName"=>$objPost->uname,"uPass"=>md5($objPost->password),"uEmail"=>$objPost->email,"ipAddress"=>get_ip_address(),"adminType"=>$objPost->adminType,"created_date"=>$objPost->created_date,"updated_date"=>$objPost->updated_date,"status"=>$objPost->status);
						$last_id=$db->insert("tbl_admin", $valArray)->getLastInsertId();
						$contArray = array(				
							"USER_NAME"=>$objPost->uname,
							"EMAIL"=>$objPost->email,
							"PASSWORD"=>$objPost->password,
							"LINK"=>SITE_ADM_MOD.'login-nct/'
						);
						sendMail($objPost->email,"subadmin_signup",$contArray);	
						foreach($actions as $key=>$value){
							$objPost1 = new stdClass();
							$objPost1->admin_id=$last_id;
							$objPost1->page_id=getTableValue("tbl_adminrole","id",array("pagenm"=>$key));
							
							$objPost1->permission=implode(',',$value);
							$objPost1->created_date = date('Y-m-d H:i:s');
							$objPost1->updated_date = date('Y-m-d H:i:s');
							
							$valArray = array("admin_id"=>$objPost1->admin_id,"page_id"=>$objPost1->page_id,"permission"=>$objPost1->permission,"created_date"=>$objPost1->created_date,"updated_date"=>$objPost1->updated_date);
							$db->insert("tbl_admin_permission", $valArray);
							
						}
						$activity_array = array("id"=>$last_id,"module"=>$module,"activity"=>'add');
						add_admin_activity($activity_array);
						
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
					
				}else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
				}
			}
			redirectPage(SITE_ADM_MOD.$module);
		}
		else {
			$msgType = array('type'=>'err','var'=>'fillAllvalues');
		}
	}	
	
	$objUser = new SubAdmin();	
	$pageContent = $objUser->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");