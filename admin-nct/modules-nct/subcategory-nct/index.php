<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");

	include("class.subcategory-nct.php");
	$module = "subcategory-nct";
	$table = "tbl_subcategory";
	
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
			"author"=>SITE_NM));
	
	$id = isset($_GET["id"]) ? (int)trim($_GET["id"]) : 0;	
	$postType = isset($_POST["type"])?trim($_POST["type"]):'';	
	$type = isset($_GET["type"])?trim($_GET["type"]):$postType;	
	
	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Sub Category';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);		
	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->subcategoryName = isset($subcategoryName) ? $subcategoryName : '';
		$objPost->subcategoryDesc = isset($subcategoryDesc) ? $subcategoryDesc : '';
		$objPost->categoryId = isset($categoryId) ? $categoryId : '';
		$objPost->isActive	= isset($isActive) && $isActive == 'y' ? 'y' : 'n';
		$objPost->createdDate = date("Y-m-d H:i:s");

		
		if($objPost->subcategoryName != ""){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){

						
						//For image upload
						$images_str=$_SESSION['temp_files'];
						$image = $images_str['0'];
   						
   						if($image !="" || $image !=NULL){
	    					 $to_path=DIR_UPD."temp_files/th1_$image";
	    					 $file_name= DIR_UPD.'subcategory/'.$id.'/';
							 
	    					 if(!file_exists($file_name)){
	    					 mkdir($file_name,0777,true);
	    					 }
	    					 
	    					 $data['subcategoryImage'] = $image;
							 copy($to_path, $file_name.$image);
							 		 
	    					 $_SESSION['temp_files']='';
	    					 $files = glob(DIR_UPD."temp_files/*"); // get all file names
								  foreach($files as $file){ // iterate files
								  if(is_file($file))
								  unlink($file); // delete file
							}	
						}



						$data['subcategoryName'] = $objPost->subcategoryName;
						$data['subcategoryDesc'] = $objPost->subcategoryDesc;
						$data['categoryId'] = $objPost->categoryId;
						$data['isActive'] = $objPost->isActive;

						$db->update($table, $data, array("id"=>$id));
						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
					add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
					
				}
				else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));		
				}
			} else {
				if(in_array('add',$Permission)){


					if(getTotalRows($table,array('subcategoryName'=>$objPost->subcategoryName,'categoryId'=>$objPost->categoryId))==0){
					
						$valArray = array("subcategoryName"=>$objPost->subcategoryName,"subcategoryDesc"=>$objPost->subcategoryDesc,"categoryId"=>$objPost->categoryId,"isActive"=>$objPost->isActive,"createdDate"=>$objPost->createdDate);
						$id=$db->insert("tbl_subcategory", $valArray)->getLastInsertId();
						
						

						//Image upload
						$images_str=$_SESSION['temp_files'];
		   				$image = $images_str['0'];	
	   					if($image!=""){
   					    	 $to_path=DIR_UPD."temp_files/th1_$image";
	    					 $file_name= DIR_UPD.'subcategory/'.$id.'/';
							 
	    					 if(!file_exists($file_name)){
	    					 mkdir($file_name,0777,true);
	    					 }
	    					 
	    					 $data['subcategoryImage'] = $image;
							 $db->update($table,$data,array("id"=>$id));
							 
							 copy($to_path, $file_name.$image);
							 
							 $_SESSION['temp_files']='';
	    					 $files = glob(DIR_UPD."temp_files/*"); // get all file names
							  foreach($files as $file){ // iterate files
							  if(is_file($file))
							  unlink($file); // delete file
							  }	
						}


						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'add');
						add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
					
					}else{
						$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'recExist'));
					}
					
				}
				else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
				}
			}
			redirectPage(SITE_ADM_MOD.$module);
		}
		else {
			$msgType = array('type'=>'err','var'=>'fillAllvalues');
		}
	}	
	$objSubcategory = new Subcategory($module, $id, NULL, $searchArray, $type);
	$pageContent = $objSubcategory->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");	
//	require_once(DIR_ADMIN_THEME."default.nct");