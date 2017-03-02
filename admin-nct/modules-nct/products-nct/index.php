<?php
	$reqAuth=true;
	require_once("../../../includes-nct/config-nct.php");

	include("class.products-nct.php");
	$module = "products-nct";
	$table = "tbl_product_deals";
	
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
	
	$headTitle = $type == 'add' ? 'Add' : ($type == 'edit' ? 'Edit' : 'Manage').' Product deals';
	$winTitle = $headTitle.' - '.SITE_NM;
	$breadcrumb = array($headTitle);		
	if(isset($_POST["submitAddForm"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
		extract($_POST);
		$objPost->productName = isset($productName) ? $productName : '';
		$objPost->categoryId = isset($categoryId) ? $categoryId : '';
		$objPost->subcategoryId = isset($subcategoryId) ? $subcategoryId : '';
		$objPost->quantity = isset($quantity) ? $quantity : '';
		$objPost->productDescription = isset($productDescription) ? $productDescription : '';
		$objPost->actualPrice = isset($actualPrice) ? $actualPrice : '';
		$objPost->isDiscount = isset($isDiscount) && $isDiscount == 'y' ? 'y' : 'n';

		$objPost->discountPrice = isset($discountPrice) ? $discountPrice : '0';
		$discountPercentage = 100 - $objPost->discountPrice*100/$objPost->actualPrice;
		$objPost->discountPercentage = $objPost->isDiscount=='y'?number_format($discountPercentage,2):'0';
		$objPost->weight = isset($weight) ? $weight : '';
		
		$objPost->isActive	= isset($isActive) && $isActive == 'y' ? 'y' : 'n';
		$objPost->createdDate = date("Y-m-d H:i:s");

		
		if($objPost->productName != "" && $objPost->categoryId != "" && $objPost->subcategoryId != "" && $objPost->quantity != "" && $objPost->productDescription != "" && $objPost->actualPrice != ""){
			if($type == 'edit' && $id > 0){
				if(in_array('edit',$Permission)){

						$data['productName'] = $objPost->productName;
						$data['categoryId'] = $objPost->categoryId;
						$data['subcategoryId'] = $objPost->subcategoryId;
						$data['quantity'] = $objPost->quantity;
						$data['productDescription'] = $objPost->productDescription;
						$data['actualPrice'] = $objPost->actualPrice;
						$data['isDiscount'] = $objPost->isDiscount;
						$data['discountPrice'] = $objPost->discountPrice;
						$data['discountPercentage'] = $objPost->discountPercentage;
						$data['weight'] = $objPost->weight;
						$data['isActive'] = $objPost->isActive;
						//$data['createdDate'] = $objPost->createdDate;

						$db->update($table, $data, array("id"=>$id));


						//For image upload
						$images_str=$_SESSION['temp_files'];
						
	   					if($images_str!=""){
	   					     foreach ($images_str as $key => $value) {
	   	    					if($value !="" || $value !=NULL){
	   	    					 $to_path=DIR_UPD."temp_files/th1_$value";
		    					 $file_name= DIR_UPD ."product/".$id."/";
								 //$file_name1= DIR_IMG ."places/".$id."/100X100/";
								 //$file_name2= DIR_IMG ."places/".$id."/200X200/";
								 //$file_name3= DIR_IMG ."places/".$id."/360X360/";


								
		    					 if(!file_exists($file_name)){
		    					 mkdir($file_name,0777,true);
		    					 }
		    					 /*if(!file_exists($file_name1)){
		    					 mkdir($file_name1,0777,true);
		    					 }
		    					 if(!file_exists($file_name2)){
		    					 mkdir($file_name2,0777,true);
		    					 }
		    					 if(!file_exists($file_name3)){
		    					 mkdir($file_name3,0777,true);
		    					 }*/
		    					 $valArray1 = array("productId"=>$id,"name"=>$value,"createdDate"=>$objPost->createdDate);
								 $db->insert("tbl_product_image", $valArray1);
								 copy($to_path, $file_name.$value);
								 //resizeImage($file_name.$value,$file_name1.$value, 100,100);
								 //resizeImage($file_name.$value,$file_name2.$value, 200,200);
								 //resizeImage($file_name.$value,$file_name3.$value, 360,360);
										
				    					 
		    					 	
								}
	    					 }

	    					 $_SESSION['temp_files']='';
		    				 $files = glob(DIR_UPD."temp_files/*"); // get all file names
								  foreach($files as $file){ // iterate files
								  if(is_file($file))
								  unlink($file); // delete file
							 }
	    				}

						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'edit');
						add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
					
				}
				else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));		
				}
			} else {
				if(in_array('add',$Permission)){



					
						$data['productName'] = $objPost->productName;
						$data['categoryId'] = $objPost->categoryId;
						$data['subcategoryId'] = $objPost->subcategoryId;
						$data['quantity'] = $objPost->quantity;
						$data['productDescription'] = $objPost->productDescription;
						$data['actualPrice'] = $objPost->actualPrice;
						$data['isDiscount'] = $objPost->isDiscount;
						$data['discountPrice'] = $objPost->discountPrice;
						$data['discountPercentage'] = $objPost->discountPercentage;
						$data['weight'] = $objPost->weight;
						$data['isActive'] = $objPost->isActive;
						$data['createdDate'] = $objPost->createdDate;
						
						$id=$db->insert($table, $data)->getLastInsertId();
						
						//For image upload
						$images_str=$_SESSION['temp_files'];
						
	   					if($images_str!=""){
	   					     foreach ($images_str as $key => $value) {
	   	    					if($value !="" || $value !=NULL){
	   	    					 $to_path=DIR_UPD."temp_files/th1_$value";
		    					 $file_name= DIR_UPD ."product/".$id."/";
								 //$file_name1= DIR_IMG ."places/".$id."/100X100/";
								 //$file_name2= DIR_IMG ."places/".$id."/200X200/";
								 //$file_name3= DIR_IMG ."places/".$id."/360X360/";


								
		    					 if(!file_exists($file_name)){
		    					 mkdir($file_name,0777,true);
		    					 }
		    					 /*if(!file_exists($file_name1)){
		    					 mkdir($file_name1,0777,true);
		    					 }
		    					 if(!file_exists($file_name2)){
		    					 mkdir($file_name2,0777,true);
		    					 }
		    					 if(!file_exists($file_name3)){
		    					 mkdir($file_name3,0777,true);
		    					 }*/
		    					 $valArray1 = array("productId"=>$id,"name"=>$value,"createdDate"=>$objPost->createdDate);
								 $db->insert("tbl_product_image", $valArray1);
								 copy($to_path, $file_name.$value);
								 //resizeImage($file_name.$value,$file_name1.$value, 100,100);
								 //resizeImage($file_name.$value,$file_name2.$value, 200,200);
								 //resizeImage($file_name.$value,$file_name3.$value, 360,360);
										
				    					 
		    					 	
								}
	    					 }

	    					 $_SESSION['temp_files']='';
		    				 $files = glob(DIR_UPD."temp_files/*"); // get all file names
								  foreach($files as $file){ // iterate files
								  if(is_file($file))
								  unlink($file); // delete file
							 }
	    				}

	    				//For notification
	    				$users = $db->pdoQuery("Select id,firstName,email from tbl_users where isActive='y'")->results();

	    				foreach ($users as $key => $value) {
	    					$newProductPosted = getTableValue("tbl_notifications","newProductPosted",array("userId"=>$value['id']));

	    					//For dashboard notifications
	    					$db->insert("tbl_user_notifications",array("notificationType"=>3,"fromId"=>'',"toId"=>$value['id'],"refId"=>$id,"createdDate"=>date("Y-m-d H:i:s")));

	    					//For email notification
	    					$productName = '<a href="'.SITE_URL.'product/'.$id.'">'.$objPost->productName.'</a>';
							if($newProductPosted == 'y'){
								$contArray = array(
									"USER_NM"=>$value['firstName'],
									"PRODUCT_NM"=>$productName
								);
								sendMail(base64_decode($value['email']),"new_product_posted",$contArray);
							}
	    				}



						$activity_array = array("id"=>$id,"module"=>$module,"activity"=>'add');
						add_admin_activity($activity_array);
						$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recAdded'));
					
					
				}
				else{
					$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'NoPermission'));
				}
			}
			redirectPage(SITE_ADM_MOD.$module);
		}
		else {
			$msgType = $_SESSION["msgType"] = array('type'=>'err','var'=>'fillAllvalues');
		}
	}	
	$objSubcategory = new products($module, $id, NULL, $searchArray, $type);
	$pageContent = $objSubcategory->getPageContent();
	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");	
//	require_once(DIR_ADMIN_THEME."default.nct");