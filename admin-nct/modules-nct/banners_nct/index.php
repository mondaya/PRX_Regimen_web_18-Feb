<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	include("class.banners_nct.php");
	$module = "banners_nct";
	$table = "tbl_banners";

	$styles = array(array("data-tables/DT_bootstrap.css",SITE_ADM_PLUGIN),
					array("bootstrap-switch/css/bootstrap-switch.min.css",SITE_ADM_PLUGIN));
	$scripts= array("core/datatable.js",
			  array("data-tables/jquery.dataTables.js",SITE_ADM_PLUGIN),
			  array("data-tables/DT_bootstrap.js",SITE_ADM_PLUGIN),
			  array("bootstrap-switch/js/bootstrap-switch.min.js",SITE_ADM_PLUGIN));
	chkPermission($module);
	$Permission=chkModulePermission($module);

	$metaTag = getMetaTags(array("description"=>"Admin Panel",
			"keywords"=>'Admin Panel',
			'author'=>AUTHOR));

	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;

	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Banners';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);

	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		// printr($_FILES);
		// printr($_POST, true);
		$maxsize = 2097152; //Bytes apprxo 2mb
		$types = array('jpg', 'png', 'jpeg');

		$objPost->banner_name = isset($banner_name) ? $banner_name : '';
		$objPost->banner_link = isset($banner_link) ? $banner_link : '';
		$objPost->is_active = isset($status) ? $status : 'n';
		$name = $_FILES['banner_image']['name'];
		$tmp_name = $_FILES['banner_image']['tmp_name'];
		$size = $_FILES['banner_image']['size'];

		if(!empty($objPost->banner_name) && !empty($objPost->banner_link)) {
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){
					if(empty($_FILES['banner_image']['error']) && !empty($name)) {
						$ext = strtolower(getExt($name));
						if(in_array($ext, $types)) {
							if ($size < $maxsize) {
								$name = md5($tmp_name + rand() * 100000).'.'.$ext;
								$filenam = upload_file(array('name'=>$name, 'tmp_name'=>$tmp_name), array("type"=>"banner", 'id'=>$id, "old_file"=>$old_image));
								$objPost->banner_image = $filenam['file_name'];
							} else {
								$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Image size must be less then 2MB.'));
								redirectPage(SITE_ADM_MOD.$module);
							}
						} else {
							$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Only jpg and png format supported.'));
							redirectPage(SITE_ADM_MOD.$module);
						}
					}
					$db->update($table, (array)$objPost, array("id"=>$id));
					$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
					add_admin_activity($activity_array);
					$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				}else{
						$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
					}
			} else {
				if(in_array('add',$Permission)){
					if(getTotalRows($table, array('banner_name'=>$objPost->banner_name), 'userId')==0){
						$objPost->created_date = date('Y-m-d H:i:s');
						$id = $db->insert($table, (array)$objPost)->getLastInsertId();
						if(empty($_FILES['banner_image']['error']) && !empty($name)) {
							$ext = strtolower(getExt($name));
							if(in_array($ext, $types)) {
								if ($size < $maxsize) {
									$name = md5($tmp_name + rand() * 100000).'.'.$ext;
									$filenam = upload_file(array('name'=>$name, 'tmp_name'=>$tmp_name), array("type"=>"banner", 'id'=>$id));
									$db->update($table, array('banner_image'=>$filenam['file_name']), array('id'=>$id));
								} else {
									$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Image size must be less then 2MB.'));
									redirectPage(SITE_ADM_MOD.$module);
								}
							} else {
								$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Only jpg and png format supported.'));
								redirectPage(SITE_ADM_MOD.$module);
							}
						}
						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'add');
						add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
					}else{
						$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'recExist'));
					}
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

	$objUsers=new Banners($module);
	$pageContent = $objUsers->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");