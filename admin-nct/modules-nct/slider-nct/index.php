<?php

	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");
	require_once("class.slider-nct.php");
	$module = "slider-nct";
	$table = "tbl_slider";

	$styles = array(array("data-tables/DT_bootstrap.css",SITE_ADM_PLUGIN),
					array("bootstrap-switch/css/bootstrap-switch.min.css",SITE_ADM_PLUGIN),
					array("image_crop_css/cropper.css",SITE_ADM_PLUGIN),
					array("image_crop_css/main.css",SITE_ADM_PLUGIN));


	$scripts= array("core/datatable.js",
					array("data-tables/jquery.dataTables.js",SITE_ADM_PLUGIN),
					array("data-tables/DT_bootstrap.js",SITE_ADM_PLUGIN),
					array("bootstrap-switch/js/bootstrap-switch.min.js",SITE_ADM_PLUGIN),
					array("image_crop/cropper.js",SITE_ADM_PLUGIN),
					array("image_crop/main.js",SITE_ADM_PLUGIN),
					array("image_crop/uploadimage.js",SITE_ADM_PLUGIN));
	chkPermission($module);
	$Permission=chkModulePermission($module);

	$metaTag = getMetaTags(array("description"=>"Admin Panel",
			"keywords"=>'Admin Panel',
			'author'=>AUTHOR));
	$breadcrumb = array("Slider");

	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;

	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Slider';
	$winTitle = $headTitle.' - '.SITE_NM;

	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->id = isset($id) ? $id : '';
		$objPost->slider_description = isset($slider_description) ? $slider_description : '';
		$objPost->isActive = isset($isActive) ? $isActive : 'n';

		if(!empty($objPost->slider_description)) {
			$data = array();
			$data['slider_description'] =  $objPost->slider_description;
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){

						$data['isActive'] = $objPost->isActive;
						$data['createdDate'] = date("Y-m-d h:i:s");
		   				$image = $_SESSION['temp_files'];
	   					
   					    	$to_path=DIR_UPD."temp_files/th1_$image";
	    					$file_name= DIR_UPD ."sliderImage/";
	    					if(!file_exists($file_name)){
	    						mkdir($file_name,0777,true);
	    					}
							copy($to_path, $file_name.$image);
							$_SESSION['temp_files']='';
							if(is_file(DIR_UPD."temp_files/th1_$image")) unlink(DIR_UPD."temp_files/th1_$image");
	    					$data['sliderImage'] = $image;


						$db->update($table, $data, array("id"=>$id));
						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
						add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
				} else {
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
				}
			} else {
				if(in_array('add',$Permission)){
						$data['isActive'] = $objPost->isActive;
						$data['createdDate'] = date("Y-m-d h:i:s");
		   				$image = $_SESSION['temp_files'];
	   					if(isset($_SESSION['temp_files']) && !empty($_SESSION['temp_files']) && !empty($image)) {
   					    	$to_path=DIR_UPD."temp_files/th1_$image";
	    					$file_name= DIR_UPD ."sliderImage/";
	    					if(!file_exists($file_name)){
	    						mkdir($file_name,0777,true);
	    					}
							copy($to_path, $file_name.$image);
							$_SESSION['temp_files']='';
							if(is_file(DIR_UPD."temp_files/th1_$image")) unlink(DIR_UPD."temp_files/th1_$image");
	    					$data['sliderImage'] = $image;
	    					$id=$db->insert("tbl_slider", $data)->getLastInsertId();
							$db->update('tbl_slider',$data,array("id"=>$id));
							$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'add');
							add_admin_activity($activity_array);
							$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
						} else {
							$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Please upload image'));
						}
				} else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
				}
			}
		} else {
			$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Please fill all values'));
		}
		redirectPage(SITE_ADM_MOD.$module);
	}
	$objContent = new slider($module);
	$pageContent = $objContent->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");
	//require_once(DIR_ADMIN_THEME."default.nct");